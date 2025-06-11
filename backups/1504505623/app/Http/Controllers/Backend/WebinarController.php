<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWebinarRequest;
use App\Webinar;
use File;
use Illuminate\Http\Request;

class WebinarController extends Controller
{
    public function store(AddWebinarRequest $request)
    {
        $course = Course::findOrFail($request->course_id);
        $webinar = new Webinar;
        $webinar->course_id = $course->id;
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->start_date = $request->start_date;
        $webinar->link = $request->link;

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/webinars/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $webinar->image = '/'.$destinationPath.$fileName;
        }

        $webinar->save();

        return redirect()->back();
    }

    public function update($id, AddWebinarRequest $request)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->start_date = $request->start_date;
        $webinar->link = $request->link;

        if ($request->hasFile('image')) {
            $image = substr($webinar->image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/webinars/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renameing image
            $request->image->move($destinationPath, $fileName);
            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($destinationPath.$fileName);
                imagepng($image, $destinationPath.$fileName, 9);
            } else {
                $image = imagecreatefromjpeg($destinationPath.$fileName);
                imagejpeg($image, $destinationPath.$fileName, 70);
            }
            $webinar->image = '/'.$destinationPath.$fileName;
        }

        $webinar->save();

        return redirect()->back();
    }

    public function destroy($id, Request $request)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->forceDelete();

        return redirect()->back();
    }
}
