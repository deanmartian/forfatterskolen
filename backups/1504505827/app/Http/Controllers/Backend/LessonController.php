<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\Lesson;
use App\Http\Requests\LessonCreateRequest;
use App\Http\Requests\LessonUpdateRequest;

class LessonController extends Controller
{
   

    public function index($course_id)
    {
        $course = Course::findOrFail($course_id);
        $section = NULL;

        return view('backend.lesson.index', compact('course', 'section'));
    }

    public function edit($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id)->toArray();
        $videos = Lesson::findOrFail($id)->videos;
        $section = NULL;

    	return view('backend.lesson.edit', compact('course', 'lesson', 'videos', 'section'));
    }

    public function create($id)
    {
    	$course = Course::findOrFail($id);
    	$section = NULL;
        $lesson = [
            'title' => old('title'),
            'content' => old('content'),
            'delay' => old('delay'),
        ];

    	return view('backend.lesson.create', compact('course', 'lesson', 'section'));
    }

    public function store($course_id, LessonCreateRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $lesson = new Lesson();
        $lesson->course_id = $course->id;
        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->delay = $request->delay;
        $lesson->save();

        return redirect(route('admin.lesson.edit', ['course_id' => $lesson->id, 'id' => $course->id]));
    }


    public function update($course_id, $id, LessonUpdateRequest $request)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $lesson->course_id = $course->id;
        $lesson->title = $request->title;
        $lesson->content = $request->content;
        $lesson->delay = $request->delay;
        $lesson->save();

        return redirect(route('admin.lesson.edit', ['course_id' => $lesson->id, 'id' => $course->id]));
    }

    public function destroy($course_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $lesson = Lesson::findOrFail($id);
        $lesson->forceDelete();

        return redirect(route('admin.course.show', $course->id).'?section=lessons');
    }


    public function save_order(Request $request)
    {
        $lessons = explode(',', $request->lesson_order);
        $i = 0;

        foreach( $lessons as $lesson ) :
            $lesson = Lesson::find($lesson);
            if( $lesson ) :
                $lesson->order = $i;
                $lesson->save();
                $i++;
            endif;
        endforeach;

        return redirect()->back();
    }
}
