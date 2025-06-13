<?php

namespace App\Http\Controllers\Giutbok;

use App\Http\Controllers\Controller;
use App\SelfPublishing;
use App\User;
use Illuminate\View\View;

class SelfPublishingController extends Controller
{
    public function index(): View
    {
        $publishingList = SelfPublishing::all();
        $learners = User::where('role', 2)->where('is_self_publishing_learner', 1)->get();

        return view('giutbok.self-publishing.index', compact('publishingList', 'learners'));
    }

    public function learners($id): View
    {
        $selfPublishing = SelfPublishing::find($id);
        $learners = $selfPublishing->learners;
        $availableLearners = User::where('role', 2)->whereNotIn('id', $learners->pluck('user_id')->toArray())
            ->where('is_self_publishing_learner', 1)->get();

        return view('giutbok.self-publishing.learners', compact('selfPublishing', 'learners', 'availableLearners'));
    }
}
