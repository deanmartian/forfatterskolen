<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\CoursesTaken;
use App\Http\Resources\Api\V1\CourseTakenResource;
use App\Http\Resources\Api\V1\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CourseController extends ApiController
{
    public function forSale(): JsonResponse
    {
        $courses = Cache::remember('api.v1.courses.for-sale', 300, static function (): array {
            return Course::query()
                ->where('for_sale', 1)
                ->get()
                ->map(static function (Course $course): array {
                    $shortDescription = $course->getAttribute('short_description');

                    if ($shortDescription === null) {
                        $shortDescription = $course->description_raw;
                    }

                    $slug = $course->getAttribute('slug');

                    if (! $slug) {
                        $slug = Str::slug($course->title);
                    }

                    $thumbnailUrl = $course->getAttribute('thumbnail_url') ?: $course->course_image;
                    $checkoutUrl = $course->pay_later_with_application
                        ? route('front.course.application', ['id' => $course->id])
                        : route('front.course.checkout', ['id' => $course->id]);

                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $slug,
                        'short_description' => $shortDescription,
                        'is_active' => (bool) $course->status,
                        'is_free' => (bool) $course->is_free,
                        'start_date' => $course->getRawOriginal('start_date'),
                        'end_date' => $course->getRawOriginal('end_date'),
                        'thumbnail_url' => $thumbnailUrl,
                        'checkout_url' => $checkoutUrl,
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json(['data' => $courses]);
    }

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
