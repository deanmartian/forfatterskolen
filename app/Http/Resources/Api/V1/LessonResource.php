<?php

namespace App\Http\Resources\Api\V1;

use App\Http\FrontendHelpers;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray($request): array
    {
        $startedAt = $request->attributes->get('course_started_at');

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'content' => $this->content,
            'lesson_content' => $this->whenLoaded('lessonContent', function () {
                return $this->lessonContent->values();
            }),
            'description' => $this->description,
            'description_simplemde' => $this->description_simplemde,
            'whole_lesson_file' => $this->whole_lesson_file,
            'delay' => $this->delay,
            'period' => $this->period,
            'order' => $this->order,
            'allow_lesson_download' => (bool) $this->allow_lesson_download,
            'available_at' => FrontendHelpers::lessonAvailability($startedAt, $this->delay, $this->period),
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toIso8601String() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toIso8601String() : null,
        ];
    }
}
