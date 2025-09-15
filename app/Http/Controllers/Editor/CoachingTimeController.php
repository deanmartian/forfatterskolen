<?php

namespace App\Http\Controllers\Editor;

use AdminHelpers;
use App\CoachingTimeRequest;
use App\CoachingTimerManuscript;
use App\EditorTimeSlot;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CoachingTimeController extends Controller
{
    public function index()
    {
        $requests = CoachingTimeRequest::whereHas('slot', function ($q) {
            $q->where('editor_id', Auth::id());
        })
            ->where('status', 'pending')
            ->with(['manuscript.user', 'slot'])
            ->get();

        $bookings = CoachingTimeRequest::whereHas('slot', function ($q) {
            $q->where('editor_id', Auth::id());
        })
            ->where('status', 'accepted')
            ->whereHas('manuscript', function ($q) {
                $q->where('status', '!=', CoachingTimerManuscript::STATUS_FINISHED);
            })
            ->with(['manuscript.user', 'slot'])
            ->get()
            ->filter(function ($booking) {
                $slotDateTime = Carbon::parse(
                    $booking->slot->date . ' ' . $booking->slot->start_time,
                    'UTC'
                );

                return $slotDateTime->greaterThanOrEqualTo(Carbon::now('UTC'));
            })
            ->sortBy(function ($booking) {
                return $booking->slot->date . ' ' . $booking->slot->start_time;
            });

        $bookingsThisWeek = $bookings->filter(function ($booking) {
            $dt = Carbon::parse(
                $booking->slot->date . ' ' . $booking->slot->start_time,
                'UTC'
            )->setTimezone(config('app.timezone'));

            return $dt->isSameWeek(Carbon::now(config('app.timezone')));
        })->count();

        $availableSlots = EditorTimeSlot::where('editor_id', Auth::id())
            ->whereDoesntHave('requests', function ($q) {
                $q->where('status', 'accepted');
            })
            ->get()
            ->filter(function ($slot) {
                $slotDateTime = Carbon::parse(
                    $slot->date . ' ' . $slot->start_time,
                    'UTC'
                );

                return $slotDateTime->greaterThanOrEqualTo(Carbon::now('UTC'));
            })
            ->count();

        return view('editor.coaching-time.index', [
            'requests'       => $requests,
            'bookings'       => $bookings,
            'bookingsThisWeek' => $bookingsThisWeek,
            'availableSlots'  => $availableSlots,
        ]);
    }

    public function calendar()
    {
        return view('editor.coaching-time.calendar');
    }

    public function fetchTimeSlot()
    {
        $slots = EditorTimeSlot::where('editor_id', Auth::user()->id)
            ->with(['requests.manuscript.user'])
            ->get();

        $events = $slots->map(function ($slot) {
            $startUtc = Carbon::parse("{$slot->date} {$slot->start_time}", 'UTC');
            $endUtc   = (clone $startUtc)->addMinutes($slot->duration);

            $event = [
                'id'    => $slot->id,
                'title' => $slot->duration . ' min',
                'start' => $startUtc->toIso8601ZuluString(),
                'end'   => $endUtc->toIso8601ZuluString(),
            ];

            $accepted = $slot->requests->firstWhere('status', 'accepted');
            if ($accepted) {
                $event['backgroundColor'] = '#28a745';
                $event['borderColor'] = '#28a745';
                $event['textColor'] = '#ffffff';
                $event['extendedProps'] = [
                    'booked'      => true,
                    'student'     => $accepted->manuscript->user->full_name ?? null,
                    'duration'    => $slot->duration,
                    'helps_with'  => $accepted->manuscript->help_with,
                ];
            }

            return $event;
        });

        return response()->json($events);
    }

    public function storeTimeSlot(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end   = Carbon::parse($request->end);
        $duration = $start->diffInMinutes($end);

        if (!in_array($duration, [30, 60])) {
            return response()->json(['success' => false, 'message' => 'Only 30 or 60 minute slots are allowed.'], 422);
        }

        $slot = EditorTimeSlot::create([
            'editor_id' => Auth::user()->id,
            'date'          => $start->copy()->utc()->toDateString(),
            'start_time'    => $start->copy()->utc()->toTimeString(),
            'duration'      => $duration,
        ]);

        return response()->json(['success' => true, 'id' => $slot->id]);
    }

    public function destroyTimeSlot($id)
    {
        EditorTimeSlot::destroy($id);
        return response()->json(['success' => true]);
    }

    public function acceptRequest($id): RedirectResponse
    {
        $request = CoachingTimeRequest::with(['slot', 'manuscript'])->findOrFail($id);

        if ($request->slot->editor_id !== Auth::id()) {
            abort(403);
        }

        $request->status = 'accepted';
        $request->save();

        CoachingTimeRequest::where('editor_time_slot_id', $request->editor_time_slot_id)
            ->where('id', '!=', $request->id)
            ->where('status', 'pending')
            ->update(['status' => 'declined']);

        $manuscript = $request->manuscript;
        $manuscript->editor_id = Auth::id();
        $manuscript->editor_time_slot_id = $request->editor_time_slot_id;
        $manuscript->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Request accepted.'),
                'alert_type' => 'success']);
    }

    public function declineRequest($id): RedirectResponse
    {
        $request = CoachingTimeRequest::with('slot')->findOrFail($id);

        if ($request->slot->editor_id !== Auth::id()) {
            abort(403);
        }

        $request->status = 'declined';
        $request->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Request declined.'),
                'alert_type' => 'success']);
    }
}
