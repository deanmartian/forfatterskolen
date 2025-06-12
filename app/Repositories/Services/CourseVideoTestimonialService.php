<?php

namespace App\Repositories\Services;

use App\Http\AdminHelpers;

class CourseVideoTestimonialService
{
    /**
     * CourseVideoTestimonialService constructor.
     */
    public function __construct($model)
    {
        $this->courseTestimonial = $model;
    }

    /**
     * Insert testimonial
     */
    public function store($request): bool
    {
        $createData = [
            'name' => $request->name,
            'course_id' => $request->course_id,
            'testimony' => $request->testimony,
            'is_video' => 1,
        ];

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

            $createData['user_image'] = '/'.$fileName;
        }

        // call the create from the RepositoryInterface
        if ($this->courseTestimonial->create($createData)) {
            return true;
        }

        return false;
    }

    /**
     * Update testimonial
     */
    public function update($request, $id): bool
    {
        $updateData = [
            'name' => $request->name,
            'course_id' => $request->course_id,
            'testimony' => $request->testimony,
            'is_video' => 1,
        ];

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

            $updateData['user_image'] = '/'.$fileName;
        }

        // call the update from the RepositoryInterface
        if ($this->courseTestimonial->update($updateData, $id)) {
            return true;
        }

        return false;
    }
}
