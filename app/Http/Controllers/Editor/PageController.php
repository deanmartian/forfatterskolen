<?php

namespace App\Http\Controllers\Editor;

use App\Assignment;
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
        $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned manuscript group / course
        ->where('status', 0)
        ->whereHas('assignment', function($query){
            $query->whereNull('parent');
            $query->orWhere('parent', 'assignment');
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

    public function upcomingAssignments()
    {
        $upcomingAssignments = Assignment::where('editor_id', '=', Auth::user()->id)
            ->whereDoesntHave('manuscripts') // check if there's no submitted manuscript yet
            ->oldest('submission_date')
            ->get();

        return view('editor.upcoming-assignment', compact('upcomingAssignments'));
    }

    public function assignmentArchive(Request $request)
    {
        if( $request->search_shop_manuscript && !empty($request->search_shop_manuscript) ) :
            $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
            ->whereHas('feedbacks', function($query){
                $query->where('approved', 1);
            }) //only the finished
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_shop_manuscript);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assigned_shop_manuscripts");
        else :
            $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
            ->whereHas('feedbacks', function($query){
                $query->where('approved', 1);
            }) //only the finished
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assigned_shop_manuscripts");
        endif;
       
        if( $request->search_my_assignments && !empty($request->search_my_assignments) ) :
            $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->where('user_id', $request->search_my_assignments)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignments");
        else :
            $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignments");
        endif;

        if( $request->search_coaching_timer && !empty($request->search_coaching_timer) ) :
            $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',1) 
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_coaching_timer);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "coachingTimers");
        else :
            $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status',1) 
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "coachingTimers");
        endif;

        if( $request->search_correction && !empty($request->search_correction) ) : 
            $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_correction);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "corrections");
        else :
            $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "corrections");
        endif;
        if( $request->search_copy_editing && !empty($request->search_copy_editing) ) :
            $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_copy_editing);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "copyEditings");
        else :
            $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
            ->where('status', 2)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "copyEditings");
        endif;

        if( $request->search_personal_assignment && !empty($request->search_personal_assignment) ) :
            $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->where('parent','users');
            })
            ->whereHas('user', function($query) use ($request){
                $query->where('id',$request->search_personal_assignment);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignmentManuscripts");
            // $courses = Course::where('title', 'LIKE', '%' . $request->search  . '%')->orderBy('created_at', 'desc')->paginate(25);
        else :
            $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) //assigned manuscript no group
            ->where('status', 1)
            ->whereHas('assignment', function($query){
                $query->where('parent','users');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ["*"], "assignedAssignmentManuscripts");
        endif;
       

        return view('editor.assignment-archive', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
        'corrections', 'copyEditings', 'assignedAssignmentManuscripts'));
    }

    public function yearlyCalendar()
    {
        return view('editor.yearly-calendar');
    }

}
