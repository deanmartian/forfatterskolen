<?php

namespace App\Http\Controllers\Backend;

use App\FreeCourse;
use App\Http\Controllers\Controller;
use App\Http\Requests\FreeCourseCreateRequest;
use App\Http\Requests\FreeCourseUpdateRequest;
use File;

class FreeCourseController extends Controller
{
    public function index()
    {
        $freeCourses = FreeCourse::orderBy('created_at', 'desc')->get();

        return view('backend.free-course.index', compact('freeCourses'));
    }

    public function store(FreeCourseCreateRequest $request)
    {
        $freeCourse = new FreeCourse;
        $freeCourse->title = $request->title;
        $freeCourse->description = $request->description;

        if ($request->hasFile('course_image')) {
            $destinationPath = 'storage/free-course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $freeCourse->course_image = '/'.$destinationPath.$fileName;
        }
        $freeCourse->url = $request->url;
        $freeCourse->save();

        return redirect()->back();
    }

    public function update($id, FreeCourseUpdateRequest $request)
    {
        $freeCourse = FreeCourse::findOrFail($id);
        $freeCourse->title = $request->title;
        $freeCourse->description = $request->description;

        if ($request->hasFile('course_image')) {
            $image = substr($freeCourse->course_image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/free-course-images/'; // upload path
            $extension = $request->course_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->course_image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $freeCourse->course_image = '/'.$destinationPath.$fileName;
        }
        $freeCourse->url = $request->url;
        $freeCourse->save();

        return redirect()->back();
    }

    public function destroy($id)
    {
        $freeCourse = FreeCourse::findOrFail($id);
        $image = substr($freeCourse->course_image, 1);
        if (File::exists($image)) {
            File::delete($image);
        }
        $freeCourse->forceDelete();

        return redirect()->back();
    }
}
