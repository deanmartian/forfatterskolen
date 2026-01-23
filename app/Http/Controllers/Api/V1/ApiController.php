<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\CoursesTaken;
use App\Http\Controllers\Controller;
use App\Package;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected function apiUser(Request $request): User
    {
        return $request->attributes->get('api_user');
    }

    protected function userOwnsCourse(User $user, Course $course): bool
    {
        return Package::where('course_id', $course->id)
            ->whereIn('id', function ($query) use ($user) {
                $query->select('package_id')
                    ->from((new CoursesTaken())->getTable())
                    ->where('user_id', $user->id);
            })
            ->exists();
    }

    protected function errorResponse(string $message, string $code, int $status, array $details = null): JsonResponse
    {
        $payload = [
            'error' => [
                'message' => $message,
                'code' => $code,
            ],
        ];

        if ($details !== null) {
            $payload['error']['details'] = $details;
        }

        return response()->json($payload, $status);
    }
}
