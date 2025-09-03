<?php

namespace App\Http\Controllers\Editor;

use App\EditorTimeSlot;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoachingTimeController extends Controller {

    public function index()
    {
        return view('editor.coaching-time.index');
    }

    public function calendar()
    {
        return view('editor.coaching-time.calendar');
    }

    public function fetchTimeSlot()
    {
        $slots = EditorTimeSlot::where('editor_id', Auth::user()->id)->get();

        $events = $slots->map(function ($slot) {
            // 👇 Tell Carbon these DB fields are UTC
            $startUtc = Carbon::parse("{$slot->date} {$slot->start_time}", 'UTC');
            $endUtc   = (clone $startUtc)->addMinutes($slot->duration);

            return [
                'id'    => $slot->id,
                'title' => $slot->duration . ' min',
                // 👇 Send Zulu time so the client knows it's UTC
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

}