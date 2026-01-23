<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\CoursesTaken;
use App\Http\Resources\Api\V1\CourseTakenResource;
use App\Http\Resources\Api\V1\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CourseController extends ApiController
{
    public function taken(Request $request): AnonymousResourceCollection
    {
        $user = $this->apiUser($request);

        $coursesTaken = CoursesTaken::with('package.course')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return CourseTakenResource::collection($coursesTaken);
    }

    public function lessons(Request $request, int $id): JsonResponse|AnonymousResourceCollection
    {
        $course = Course::find($id);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $this->userOwnsCourse($user, $course)) {
            return $this->errorResponse('You do not have access to this course.', 'forbidden', 403);
        }

        $lessons = $course->lessons()->orderBy('order', 'asc')->get();

        return LessonResource::collection($lessons);
    }
}
