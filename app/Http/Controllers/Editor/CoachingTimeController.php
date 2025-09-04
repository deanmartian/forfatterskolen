<?php

namespace App\Http\Controllers\Editor;

use App\CoachingTimeRequest;
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
        })->where('status', 'pending')
            ->with(['manuscript.user', 'slot'])
            ->get();

        return view('editor.coaching-time.index', compact('requests'));
    }

    public function calendar()
    {
        return view('editor.coaching-time.calendar');
    }

    public function fetchTimeSlot()
    {
        $slots = EditorTimeSlot::where('editor_id', Auth::user()->id)->get();

        $events = $slots->map(function ($slot) {
            $startUtc = Carbon::parse("{$slot->date} {$slot->start_time}", 'UTC');
            $endUtc   = (clone $startUtc)->addMinutes($slot->duration);

            return [
                'id'    => $slot->id,
                'title' => $slot->duration . ' min',
                'start' => $startUtc->toIso8601ZuluString(),
                'end'   => $endUtc->toIso8601ZuluString(),
            ];
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

        $manuscript = $request->manuscript;
        $manuscript->editor_id = Auth::id();
        $manuscript->editor_time_slot_id = $request->editor_time_slot_id;
        $manuscript->save();

        return redirect()->back()->with('success', 'Request accepted.');
    }

    public function declineRequest($id): RedirectResponse
    {
        $request = CoachingTimeRequest::with('slot')->findOrFail($id);

        if ($request->slot->editor_id !== Auth::id()) {
            abort(403);
        }

        $request->status = 'declined';
        $request->save();

        return redirect()->back()->with('success', 'Request declined.');
    }
}
