<?php
namespace App\Http\Controllers\Backend;

use App\CalendarNote;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarNoteCreateRequest;

class CalendarNoteController extends Controller {

    /**
     * Display all calendar notes
     */
    public function index()
    {
        $calendar = CalendarNote::with('course')->get();
        return view('backend.calendar.index',compact('calendar'));
    }

    /**
     * Display the create page of note
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $calendar = [
            'note' => '',
            'from_date' => '',
            'to_date' => '',
            'course_id' => ''
        ];
        return view('backend.calendar.create', compact('calendar'));
    }

    /**
     * Create new note
     * @param CalendarNoteCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CalendarNoteCreateRequest $request)
    {
        $calendar = new CalendarNote();
        $calendar->note = $request->note;
        $calendar->from_date = $request->from_date;
        $calendar->to_date = $request->to_date;
        $calendar->course_id = $request->course_id;
        $calendar->save();
        return redirect(route('admin.calendar-note.index'));
    }

    /**
     * Display the edit page
     * @param $id
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
     * @param $id
     * @param CalendarNoteCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, CalendarNoteCreateRequest $request)
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $calendar = CalendarNote::find($id);
        if ($calendar) {
            $calendar->forceDelete();
        }
        return redirect()->route('admin.calendar-note.index');
    }
}