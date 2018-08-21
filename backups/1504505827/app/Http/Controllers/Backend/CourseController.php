<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Course;
use App\SimilarCourse;
use App\Http\AdminHelpers;
use App\Http\Requests\CourseCreateRequest;
use App\Http\Requests\CourseUpdateRequest;
use File; 

class CourseController extends Controller
{
   
    public function index(Request $request)
    {
        if( $request->search && !empty($request->search) ) :
            $courses = Course::where('title', 'LIKE', '%' . $request->search  . '%')->orderBy('created_at', 'desc')->paginate(25);
        else :
            $courses = Course::orderBy('created_at', 'desc')->paginate(25);
        endif;
    	return view('backend.course.index', compact('courses'));
    }



    public function show(Request $request, $id)
    {
    	$section = isset( $request->section ) ? $request->section : "overview";
    	AdminHelpers::validateCourseSubpage($section);

    	$course = Course::findOrFail($id);
    	return $this->showSection($section, $course);
    }



    public function showSection($section, $course)
    {
    	return view('backend.course.' . $section, compact('course', 'section'));
    }




    public function create()
    {
        $course = [
            'title' => old('title'),
            'description' => old('description'),
            'description_simplemde' => old('description_simplemde'),
            'course_image' => '',
            'type' => '',
            'course_plan' => '',
            'start_date' => '',
            'end_date' => '',
        ];
        return view('backend.course.create', compact('course'));
    }



    public function store(CourseCreateRequest $request)
    {
        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;

        if ($request->hasFile('course_image')) :
            $destinationPath = 'storage/course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) : 
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $course->course_image = '/'.$destinationPath.$fileName;
        endif;

        $course->type = $request->type;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->save();
        return redirect(route('admin.course.show', $course->id));
    }




    public function edit($id)
    {
        $course = Course::findOrFail($id)->toArray();
        return view('backend.course.edit', compact('course'));
    }





    public function update($id, CourseUpdateRequest $request)
    {
        $course = Course::findOrFail($id);
        $course->title = $request->title;
        $course->description = $request->description;
        $course->course_plan = $request->course_plan;

        if ($request->hasFile('course_image') && $request->file('course_image')->isValid()) :
            $image = substr($course->course_image, 1);
            if( File::exists($image) ) :
                File::delete($image);
            endif;
            $destinationPath = 'storage/course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if ( strtolower( $extension ) == "png" ) : 
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            else :
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            endif;
            $course->course_image = '/'.$destinationPath.$fileName;
        endif;

        $course->type = $request->type;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->save();
        return redirect(route('admin.course.show', $course->id));
    }





    public function destroy($id){
        $course = Course::findOrFail($id);
        $image = substr($course->course_image, 1);
        if( File::exists($image) ) :
            File::delete($image);
        endif;
        $course->forceDelete();
        return redirect(route('admin.course.index'));
    }





    public function update_email($id, Request $request)
    {
        $course = Course::findOrFail($id);
        $course->email = $request->email;
        $course->save();
        return redirect()->back();
    }




    public function clone_course($id, Request $request)
    {
        $course = Course::findOrFail($id);
        $clone_course = $course->replicate();
        $clone_course->push();

        foreach( $course->lessons as $lesson ) :
            $clone_lesson = $lesson->replicate();
            $clone_lesson->course_id = $clone_course->id;
            $clone_lesson->push();
        endforeach;

        return redirect(route('admin.course.show', $clone_course->id));
    }





    public function add_similar_course($id, Request $request)
    {
        $course = Course::findOrFail($id);
        $similar_course_id = Course::findOrFail($request->similar_course_id);

        $similar_course = new SimilarCourse();
        $similar_course->course_id = $course->id;
        $similar_course->similar_course_id = $similar_course_id->id;
        $similar_course->save();
        return redirect()->back();
    }



    public function remove_similar_course($similar_course_id)
    {
        $similar_course = SimilarCourse::findOrFail($similar_course_id);
        $similar_course->forceDelete();
        return redirect()->back();
    }

    
}
