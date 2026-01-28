<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTakenResource extends JsonResource
{
    public function toArray($request): array
    {
        $course = $this->package ? $this->package->course : null;
        $startedAt = $this->started_at_value ?? null;
        $startDate = $this->start_date_value ?? null;
        $endDate = $this->end_date_value ?? null;

        return [
            'id' => $this->id,
            'course_id' => $course ? $course->id : null,
            'package_id' => $this->package_id,
            'is_active' => (bool) $this->is_active,
            'started_at' => $startedAt ? Carbon::parse($startedAt)->toIso8601String() : null,
            'start_date' => $startDate ? Carbon::parse($startDate)->toDateString() : null,
            'end_date' => $endDate ? Carbon::parse($endDate)->toDateString() : null,
            'access_lessons' => $this->access_lessons,
            'years' => $this->years,
            'is_free' => (bool) $this->is_free,
            'course' => $course ? new CourseResource($course) : null,
        ];
    }
}
