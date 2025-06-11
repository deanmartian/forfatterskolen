<?php

namespace App\Http\Controllers\Backend;

use App\Assignment;
use App\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::orderBy('created_at', 'desc')->paginate(15);

        return view('backend.assignment.index', compact('assignments'));
    }

    public function show($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($id);
        $section = 'assignments';
        if ($assignment->course->id == $course->id) {
            return view('backend.assignment.show', compact('course', 'assignment', 'section'));
        }

        return abort('404');
    }

    public function store($course_id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        if ($request->title) {
            Assignment::create([
                'title' => $request->title,
                'description' => $request->description,
                'course_id' => $course->id,
            ]);
        }

        return redirect()->back();
    }

    public function update($course_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($id);

        if ($assignment->course->id == $course->id && $request->title) {
            $assignment->title = $request->title;
            $assignment->description = $request->description;
            $assignment->save();
        }

        return redirect()->back();
    }

    public function destroy($course_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($id);

        if ($assignment->course->id == $course->id) {
            $assignment->forceDelete();
        }

        return redirect(route('admin.course.show', $course->id).'?section=assignments');
    }
}
