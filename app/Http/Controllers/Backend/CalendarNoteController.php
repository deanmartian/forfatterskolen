<?php

namespace App\Http\Controllers\Backend;

use App\CalendarNote;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarNoteCreateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CalendarNoteController extends Controller
{
    /**
     * Display all calendar notes
     */
    public function index(): View
    {
        $calendar = CalendarNote::with('course')->get();

        return view('backend.calendar.index', compact('calendar'));
    }

    /**
     * Display the create page of note
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $calendar = [
            'note' => '',
            'from_date' => '',
            'to_date' => '',
            'course_id' => '',
        ];

        return view('backend.calendar.create', compact('calendar'));
    }

    /**
     * Create new note
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CalendarNoteCreateRequest $request): RedirectResponse
    {
        $calendar = new CalendarNote;
        $calendar->note = $request->note;
        $calendar->from_date = $request->from_date;
        $calendar->to_date = $request->to_date;
        $calendar->course_id = $request->course_id;
        $calendar->save();

        return redirect(route('admin.calendar-note.index'));
    }

    /**
     * Display the edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $calendar = CalendarNote::find($id);
        if ($calendar) {
            $calendar = $calendar->toArray();

            return view('backend.calendar.edit', compact('calendar'));
        }

        return redirect()->back();
    }

    /**
     * Update the note
     */
    public function update($id, CalendarNoteCreateRequest $request): RedirectResponse
    {
        $calendar = CalendarNote::find($id);
        if ($calendar) {
            $calendar->note = $request->note;
            $calendar->from_date = $request->from_date;
            $calendar->to_date = $request->to_date;
            $calendar->course_id = $request->course_id;
            $calendar->save();
        }

        return redirect()->back();
    }

    /**
     * Delete a note
     */
    public function destroy($id): RedirectResponse
    {
        $calendar = CalendarNote::find($id);
        if ($calendar) {
            $calendar->forceDelete();
        }

        return redirect()->route('admin.calendar-note.index');
    }
}
