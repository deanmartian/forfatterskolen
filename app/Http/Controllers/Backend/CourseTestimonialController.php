<?php

namespace App\Http\Controllers\Backend;

use App\CourseTestimonial;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseTestimonialCreateRequest;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseTestimonialController extends Controller
{
    /**
     * Display all testimonials
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $testimonials = CourseTestimonial::paginate(15);

        return view('backend.course.testimonials.index', compact('testimonials'));
    }

    /**
     * Display the create testimonial page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $testimonial = [
            'name' => '',
            'testimony' => '',
            'user_image' => '',
            'course_id' => '',
        ];

        return view('backend.course.testimonials.create', compact('testimonial'));
    }

    /**
     * Create new testimonial
     */
    public function store(CourseTestimonialCreateRequest $request): RedirectResponse
    {
        $testimonial = new CourseTestimonial;
        $testimonial->name = $request->name;
        $testimonial->testimony = $request->testimony;
        $testimonial->course_id = $request->course_id;

        if ($request->hasFile('user_image')) {
            $destinationPath = 'images/course-testimonials'; // upload path

            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = $request->user_image->extension(); // getting image extension
            $uploadedFile = $request->user_image->getClientOriginalName();
            $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->user_image->move($destinationPath, $fileName);

            // optimize image
            if (strtolower($extension) == 'png') {
                $image = imagecreatefrompng($fileName);
                imagepng($image, $fileName, 9);
            } else {
                $image = imagecreatefromjpeg($fileName);
                imagejpeg($image, $fileName, 70);
            }
            $testimonial->user_image = '/'.$fileName;
        }

        $testimonial->save();

        return redirect()->route('admin.course-testimonial.index');
    }

    /**
     * Display the edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id): View
    {
        $testimonial = CourseTestimonial::findOrFail($id)->toArray();

        return view('backend.course.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update a testimonial
     */
    public function update($id, CourseTestimonialCreateRequest $request): RedirectResponse
    {
        $testimonial = CourseTestimonial::find($id);
        if ($testimonial) {
            $testimonial->name = $request->name;
            $testimonial->testimony = $request->testimony;
            $testimonial->course_id = $request->course_id;

            if ($request->hasFile('user_image')) {
                $destinationPath = 'images/course-testimonials'; // upload path

                if (! \File::exists($destinationPath)) {
                    \File::makeDirectory($destinationPath);
                }

                $extension = $request->user_image->extension(); // getting image extension
                $uploadedFile = $request->user_image->getClientOriginalName();
                $actual_name = pathinfo($uploadedFile, PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                $request->user_image->move($destinationPath, $fileName);

                // optimize image
                if (strtolower($extension) == 'png') {
                    $image = imagecreatefrompng($fileName);
                    imagepng($image, $fileName, 9);
                } else {
                    $image = imagecreatefromjpeg($fileName);
                    imagejpeg($image, $fileName, 70);
                }
                $testimonial->user_image = '/'.$fileName;
            }

            $testimonial->save();
        }

        return redirect()->route('admin.course-testimonial.index');
    }

    /**
     * Delete testimonial
     */
    public function destroy($id): RedirectResponse
    {
        $testimonial = CourseTestimonial::find($id);
        if ($testimonial) {
            $image = substr($testimonial->user_image, 1);
            if (File::exists($image)) {
                File::delete($image);
            }
            $testimonial->forceDelete();
        }

        return redirect()->route('admin.course-testimonial.index');
    }

    /**
     * Clone a testimony
     */
    public function cloneRecord($id): RedirectResponse
    {
        $testimonial = CourseTestimonial::find($id);
        if ($testimonial) {
            $image = substr($testimonial->user_image, 1);
            $fileName = null;

            // check if file exist
            if (File::exists($image)) {
                $expFilePath = explode('/', $image);
                $destinationPath = 'images/course-testimonials';
                $extractNameExtension = explode('.', end($expFilePath));
                $extension = end($extractNameExtension);
                $getFilename = array_slice($extractNameExtension, 0, -1);
                $actual_name = implode(' ', $getFilename);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
                copy($image, $fileName); // clone the file
            }

            $newTestimonial = new CourseTestimonial;
            $newTestimonial->course_id = $testimonial->course_id;
            $newTestimonial->name = $testimonial->name;
            $newTestimonial->testimony = $testimonial->testimony;
            $newTestimonial->user_image = $fileName;
            $newTestimonial->is_video = $testimonial->is_video;
            $newTestimonial->save();

            if ($testimonial->is_video) {
                return redirect()->route('admin.course-video-testimonial.edit', $newTestimonial->id);
            } else {
                return redirect()->route('admin.course-testimonial.edit', $newTestimonial->id);
            }
        }

        return redirect()->route('admin.course-testimonial.index');
    }
}
