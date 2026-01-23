<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'description_simplemde' => $this->description_simplemde,
            'course_image' => $this->course_image,
            'type' => $this->type,
            'instructor' => $this->instructor,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_free' => (bool) $this->is_free,
        ];
    }
}
