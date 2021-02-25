<?php

namespace App\Http\Controllers\Editor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\ShopManuscriptsTaken;
use App\AssignmentManuscript;
use App\Course;

class PageController extends Controller
{
    public function dashboard()
    {
        $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)->get();
        $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
        ->where('status', 0)
        ->whereHas('assignment', function($query){
            $query->whereNull('parent');
        })
        ->get();
        
        $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',0)->get();
        $corrections = Auth::user()->assignedCorrections;
        $copyEditings = Auth::user()->assignedCopyEditing;
        $singleCourses = Course::where('type', 'Single')
            ->where('id', '!=', 17)
            ->where('is_free', 0)
            ->get()->pluck('id');
        $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 0)
            ->whereHas('assignment', function($query) {
                $query->where('parent', 'users');
            })
            ->get();

        return view('editor.dashboard', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
        'corrections', 'copyEditings','pendingTasks', 'assignedAssignmentManuscripts'));

    }
}
