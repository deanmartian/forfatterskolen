<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\Http\FrontendHelpers;
use Illuminate\Http\JsonResponse;

class CourseApplicationController extends ApiController
{
    public function show(int $id): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        if (! $course->is_free && (! FrontendHelpers::isCourseActive($course) || $course->packages()->count() === 0)) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        $lovableBase = rtrim((string) config('api.lovable_url'), '/');

        if (! $course->pay_later_with_application) {
            return response()->json([
                'success' => true,
                'action' => 'redirect_checkout',
                'redirect_url' => $lovableBase.'/course/'.$course->id.'/checkout',
            ]);
        }

        return response()->json([
            'success' => true,
            'action' => 'show_application',
            'application_url' => $lovableBase.'/course/'.$course->id.'/application',
        ]);
    }
}
