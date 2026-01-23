<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTakenResource extends JsonResource
{
    public function toArray($request): array
    {
        $course = $this->package ? $this->package->course : null;

        return [
            'id' => $this->id,
            'course_id' => $course ? $course->id : null,
            'package_id' => $this->package_id,
            'is_active' => (bool) $this->is_active,
            'started_at' => $this->started_at ? Carbon::parse($this->started_at)->toIso8601String() : null,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->toDateString() : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->toDateString() : null,
            'access_lessons' => $this->access_lessons,
            'years' => $this->years,
            'is_free' => (bool) $this->is_free,
            'course' => $course ? new CourseResource($course) : null,
        ];
    }
}
