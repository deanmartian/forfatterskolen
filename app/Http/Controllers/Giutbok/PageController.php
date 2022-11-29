<?php
namespace App\Http\Controllers\Giutbok;

use App\Http\Controllers\Controller;
use App\Project;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\User;

class PageController extends Controller
{

    public function dashboard()
    {
        $learners = User::where('role', 2)->get();
        $selfPublishingApprovedFeedbacks = SelfPublishingFeedback::where('is_approved', 1)->pluck('self_publishing_id')->toArray();
        $selfPublishingList = SelfPublishing::whereNotIn('id', $selfPublishingApprovedFeedbacks)->get();
        $projects = Project::all();
        return view('giutbok.dashboard', compact('selfPublishingList', 'learners', 'projects'));
    }

}