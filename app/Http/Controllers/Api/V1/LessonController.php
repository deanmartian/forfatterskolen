<?php

namespace App\Http\Controllers\Api\V1;

use App\Lesson;
use App\Http\Resources\Api\V1\LessonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends ApiController
{
    public function show(Request $request, int $id): JsonResponse
    {
        $lesson = Lesson::with('course')->find($id);

        if (! $lesson) {
            return $this->errorResponse('Lesson not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $lesson->course || ! $this->userOwnsCourse($user, $lesson->course)) {
            return $this->errorResponse('You do not have access to this lesson.', 'forbidden', 403);
        }

        return response()->json([
            'data' => new LessonResource($lesson),
        ]);
    }
}
