<?php

namespace App\Http\Controllers\Api\V1;

use App\CoursesTaken;
use App\Http\Resources\Api\V1\CourseTakenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $coursesTaken = CoursesTaken::with('package.course')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(3)
            ->get();

        $totalCoursesTaken = CoursesTaken::where('user_id', $user->id)->count();

        return response()->json([
            'courses_taken_total' => $totalCoursesTaken,
            'courses_taken' => CourseTakenResource::collection($coursesTaken)->toArray($request),
        ]);
    }
}
