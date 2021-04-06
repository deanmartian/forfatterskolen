<?php

namespace App\Http\Controllers\Editor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\ShopManuscriptsTaken;
use App\AssignmentManuscript;
use App\Course;
use App\CorrectionManuscript;
use App\CopyEditingManuscript;
use Carbon\Carbon;

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
        $shopManuscriptRequests = Auth::user()->shopManuscriptRequests->where('answer', '')->where('answer_until', '>=', strftime('%Y-%m-%d', strtotime(Carbon::now())));

        return view('editor.dashboard', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
        'corrections', 'copyEditings', 'assignedAssignmentManuscripts', 'shopManuscriptRequests'));

    }

    public function assignmentArchive()
    {

        $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
                                                    ->whereHas('feedbacks', function($query){
                                                        $query->where('approved', 1);
                                                    }) //only the finished
                                                    ->orderBy('created_at', 'desc')
                                                    ->paginate(10, ["*"], "assigned_shop_manuscripts");
        $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
        ->where('status', 1)
        ->whereHas('assignment', function($query){
            $query->whereNull('parent');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10, ["*"], "assignedAssignments");

        $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',1) 
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10, ["*"], "coachingTimers");
        $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
                                            ->where('status', 2)
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(10, ["*"], "corrections");
        $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
                                            ->where('status', 2)
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(10, ["*"], "copyEditings");
        $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->where('parent','users');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignmentManuscripts");

        return view('editor.assignment-archive', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
        'corrections', 'copyEditings', 'assignedAssignmentManuscripts'));
    }

    public function yearlyCalendar()
    {
        return view('editor.yearly-calendar');
    }

}
