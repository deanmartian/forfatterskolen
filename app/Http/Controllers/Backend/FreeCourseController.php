<?php

namespace App\Http\Controllers\Backend;

use App\FreeCourse;
use App\FreeWebinar;
use App\FreeWebinarPresenter;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddWebinarRequest;
use App\Http\Requests\FreeCourseCreateRequest;
use App\Http\Requests\FreeCourseUpdateRequest;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class FreeCourseController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkPageAccess:2');
    }

    public function index(): View
    {
        $freeCourses = FreeCourse::orderBy('created_at', 'desc')->get();
        $freeWebinars = FreeWebinar::orderBy('created_at', 'desc')->get();

        return view('backend.free-course.index', compact('freeCourses', 'freeWebinars'));
    }

    public function store(FreeCourseCreateRequest $request): RedirectResponse
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

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Free Course created successfully.'),
            'alert_type' => 'success']);
    }

    public function update($id, FreeCourseUpdateRequest $request): RedirectResponse
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

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Free Course updated successfully.'),
            'alert_type' => 'success']);
    }

    public function destroy($id): RedirectResponse
    {
        $freeCourse = FreeCourse::findOrFail($id);
        $image = substr($freeCourse->course_image, 1);
        if (File::exists($image)) {
            File::delete($image);
        }
        $freeCourse->forceDelete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Free Course deleted successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * Create free webinar
     */
    public function storeWebinar(AddWebinarRequest $request): RedirectResponse
    {
        $webinar = new FreeWebinar;
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->start_date = $request->start_date;
        $webinar->gtwebinar_id = $request->gtwebinar_id;

        if ($request->hasFile('image')) {
            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            // check if the folder exists if not then create the folder
            if (! file_exists('storage/free-webinars/')) {
                File::makeDirectory('storage/free-webinars/', 0775, true);
            }

            $largeImageLoc = 'storage/free-webinars/'.$fileName; // upload path
            $thumbImageLoc = 'storage/free-webinars/thumb/'.$fileName; // upload path thumb

            if (move_uploaded_file($fileTmp, $largeImageLoc)) {
                // file permission
                chmod($largeImageLoc, 0777);

                // get dimensions of the original image
                [$width_org, $height_org] = getimagesize($largeImageLoc);

                // get image coords
                $x = (int) $request->x;
                $y = (int) $request->y;
                $width = (int) $request->w;
                $height = (int) $request->h;

                // define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                // crop and resize image
                $newImage = imagecreatetruecolor($width_new, $height_new);

                switch ($fileType) {
                    case 'image/gif':
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage, $source, 0, 0, $x, $y, $width_new, $height_new, $width, $height);

                // check if the folder exists if not then create the folder
                if (! file_exists('storage/free-webinars/thumb/')) {
                    File::makeDirectory('storage/free-webinars/thumb/', 0775, true);
                }

                switch ($fileType) {
                    case 'image/gif':
                        imagegif($newImage, $thumbImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        imagejpeg($newImage, $thumbImageLoc, 90);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        imagepng($newImage, $thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                // remove large image
                unlink($largeImageLoc);

                $webinar->image = '/'.$thumbImageLoc;
            }
        }

        $webinar->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Free Webinar created successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * Update the webinar
     *
     * @param  $id  int FreeWebinar id
     */
    public function updateWebinar($id, AddWebinarRequest $request): RedirectResponse
    {
        $webinar = FreeWebinar::findOrFail($id);
        $webinar->title = $request->title;
        $webinar->description = $request->description;
        $webinar->start_date = $request->start_date;
        $webinar->gtwebinar_id = $request->gtwebinar_id;

        if ($request->hasFile('image')) {

            $fileExt = $request->image->extension(); // getting image extension
            $fileType = $request->image->getMimeType();
            $fileSize = $request->image->getSize();
            $fileTmp = $request->image->getPathName();
            $fileName = time().'.'.$fileExt; // renaming image

            $largeImageLoc = 'storage/free-webinars/'.$fileName; // upload path
            $thumbImageLoc = 'storage/free-webinars/thumb/'.$fileName; // upload path thumb

            if (move_uploaded_file($fileTmp, $largeImageLoc)) {
                // file permission
                chmod($largeImageLoc, 0777);

                // get dimensions of the original image
                [$width_org, $height_org] = getimagesize($largeImageLoc);

                // get image coords
                $x = (int) $request->x;
                $y = (int) $request->y;
                $width = (int) $request->w;
                $height = (int) $request->h;

                // define the final size of the cropped image
                $width_new = $width;
                $height_new = $height;

                $source = '';

                // crop and resize image
                $newImage = imagecreatetruecolor($width_new, $height_new);

                switch ($fileType) {
                    case 'image/gif':
                        $source = imagecreatefromgif($largeImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        $source = imagecreatefromjpeg($largeImageLoc);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        $source = imagecreatefrompng($largeImageLoc);
                        break;
                }

                imagecopyresampled($newImage, $source, 0, 0, $x, $y, $width_new, $height_new, $width, $height);

                // check if the folder exists if not then create the folder
                if (! file_exists('storage/free-webinars/thumb/')) {
                    File::makeDirectory('storage/free-webinars/thumb/', 0775, true);
                }

                switch ($fileType) {
                    case 'image/gif':
                        imagegif($newImage, $thumbImageLoc);
                        break;
                    case 'image/pjpeg':
                    case 'image/jpeg':
                    case 'image/jpg':
                        imagejpeg($newImage, $thumbImageLoc, 90);
                        break;
                    case 'image/png':
                    case 'image/x-png':
                        imagepng($newImage, $thumbImageLoc);
                        break;
                }
                imagedestroy($newImage);

                // remove large image
                unlink($largeImageLoc);

                $webinar->image = '/'.$thumbImageLoc;
            }

        }

        $webinar->save();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Free Webinar updated successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * Delete the free webinar
     *
     * @param  Request  $request
     */
    public function deleteWebinar($id): RedirectResponse
    {
        $webinar = FreeWebinar::findOrFail($id);
        $webinar->forceDelete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Free Webinar deleted successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * Create webinar presenter
     */
    public function storeWebinarPresenter($webinar_id, Request $request): RedirectResponse
    {
        $webinar = FreeWebinar::findOrFail($webinar_id);
        $webinarPresenter = new FreeWebinarPresenter;
        $webinarPresenter->free_webinar_id = $webinar->id;
        $webinarPresenter->first_name = $request->first_name;
        $webinarPresenter->last_name = $request->last_name;
        $webinarPresenter->email = $request->email;

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/free-webinar-presenters/'; // upload path
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
            $webinarPresenter->image = '/'.$destinationPath.$fileName;
        }

        $webinarPresenter->save();

        return redirect()->back();
    }

    /**
     * Update webinar presenter
     */
    public function updateWebinarPresenter($webinar_id, $id, Request $request): RedirectResponse
    {
        $webinarPresenter = FreeWebinarPresenter::findOrFail($id);
        $webinarPresenter->first_name = $request->first_name;
        $webinarPresenter->last_name = $request->last_name;
        $webinarPresenter->email = $request->email;

        if ($request->hasFile('image')) {
            $image = substr($webinarPresenter->image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $destinationPath = 'storage/webinar-presenter-images/'; // upload path
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
            $webinarPresenter->image = '/'.$destinationPath.$fileName;
        }

        $webinarPresenter->save();

        return redirect()->back();
    }

    /**
     * Delete webinar presenter
     */
    public function deleteWebinarPresenter($webinar_id, $id): RedirectResponse
    {
        $webinarPresenter = FreeWebinarPresenter::findOrFail($id);
        $webinarPresenter->forceDelete();

        return redirect()->back();
    }
}
