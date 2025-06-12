<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssignedWebinarController extends Controller
{
    public function show(): View
    {
        // $assignedWebinar = Auth::user()->assignedWebinars;
        $webinars = DB::table('webinars')
            ->select('webinars.*', 'webinar_editors.presenter_url', 'courses.title as course_title')
            ->leftJoin('webinar_editors', 'webinars.id', '=', 'webinar_editors.webinar_id')
            ->leftJoin('courses', 'webinars.course_id', '=', 'courses.id')
            ->whereDate('webinars.start_date', '>=', now()->format('Y-m-d'))
            ->where('editor_id', Auth::id())
            ->orderBy('webinars.start_date', 'ASC')
            ->get();

        return view('editor.assigned-webinars', compact('webinars'));
    }
}
