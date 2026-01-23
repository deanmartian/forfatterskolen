<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'content' => $this->content,
            'description' => $this->description,
            'description_simplemde' => $this->description_simplemde,
            'whole_lesson_file' => $this->whole_lesson_file,
            'delay' => $this->delay,
            'period' => $this->period,
            'order' => $this->order,
            'allow_lesson_download' => (bool) $this->allow_lesson_download,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
