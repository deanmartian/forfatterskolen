<?php

namespace App\Http\Controllers\Giutbok;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class LearnerController extends Controller
{

    public function index(Request $request, User $user)
    {
        $learners = $user->newQuery();
        if( $request->sid || $request->sfname || $request->slname || $request->semail) :
            if ($request->sid) {
                $learners->where('id', $request->sid);
            }

            if ($request->sfname) {
                $learners->where('first_name', 'LIKE', '%' . $request->sfname  . '%');
            }

            if ($request->slname) {
                $learners->where('last_name', 'LIKE', '%' . $request->slname  . '%');
            }

            if ($request->semail) {
                $learners->where('email', 'LIKE', '%' . $request->semail  . '%');
            }

            $learners->orderBy('first_name', 'asc')
                ->orderBy('email', 'asc');
        endif;

        if ($request->has('free-course')) {
            $learners->has('freeCourses');
        }

        if ($request->has('workshop')) {
            $learners->has('workshopsTaken');
        }

        if ($request->has('shop-manuscript')) {
            $learners->has('shopManuscriptsTaken');
        }

        if ($request->has('course')) {
            if ($request->has('free-course')) {
                $learners->has('coursesTaken');
            } else {
                $learners->has('coursesTakenNoFree');
            }
        }

        $learners->orderBy('created_at', 'desc');
        $learners = [];//$learners->paginate(25);

        return view('giutbok.learner.index', compact('learners'));
    }

}