<?php

namespace App\Http\Controllers\Backend;

use App\Assignment;
use App\AssignmentGroup;
use App\AssignmentGroupLearner;
use App\Course;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class AssignmentGroupController extends Controller
{
    public function show($course_id, $assignment_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);
        $section = 'assignments';
        if ($assignment->course->id == $course->id) {
            return view('backend.assignment.group_show', compact('course', 'assignment', 'section', 'group'));
        }

        return abort('404');
    }

    public function store($course_id, $assignment_id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        if ($request->title) {
            AssignmentGroup::create([
                'assignment_id' => $assignment->id,
                'title' => $request->title,
            ]);
        }

        return redirect()->back();
    }

    public function update($course_id, $assignment_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);

        if ($assignment->course->id == $course->id && $request->title) {
            $group->title = $request->title;
            $group->save();
        }

        return redirect()->back();
    }

    public function destroy($course_id, $assignment_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);

        if ($assignment->course->id == $course->id) {
            $group->forceDelete();
        }

        return redirect(route('admin.assignment.show', ['course_id' => $course->id, 'id' => $assignment->id]));
    }

    public function add_learner($course_id, $assignment_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        $manuscriptUsers = $assignment->manuscripts->pluck('user_id')->toArray();
        $groupLearners = $group->learners->pluck('user_id')->toArray();

        if ($assignment->course->id == $course->id && in_array($user->id, $manuscriptUsers) && ! in_array($user->id, $groupLearners)) {
            AssignmentGroupLearner::create([
                'assignment_group_id' => $group->id,
                'user_id' => $user->id,
            ]);
        }

        return redirect()->back();
    }

    public function remove_learner($course_id, $assignment_id, $group_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($group_id);
        $assignmentLearner = AssignmentGroupLearner::findOrFail($id);

        $assignmentLearner->forceDelete();

        return redirect()->back();
    }
}
