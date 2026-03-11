<?php

namespace App\Http\Controllers\Api;

use App\CoursesTaken;
use App\Http\Controllers\Api\V1\ApiController;
use App\User;
use App\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunityUserController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        return response()->json([
            'external_user_id' => $user->id,
            'name' => trim($user->first_name.' '.$user->last_name),
            'email' => $user->email,
            'community_access' => $this->communityAccessForUser($user),
            'course_access' => $this->courseAccessForUser($user),
            'roles' => $this->rolesForUser($user),
        ]);
    }

    private function communityAccessForUser(User $user): bool
    {
        $preference = UserPreference::query()
            ->where('user_id', $user->id)
            ->first();

        if ($preference && ! is_null($preference->joined_reader_community)) {
            return (bool) $preference->joined_reader_community;
        }

        return $user->is_active === 1;
    }

    private function courseAccessForUser(User $user): array
    {
        $courseTitles = CoursesTaken::query()
            ->join('packages', 'packages.id', '=', 'courses_taken.package_id')
            ->join('courses', 'courses.id', '=', 'packages.course_id')
            ->where('courses_taken.user_id', $user->id)
            ->where('courses_taken.is_active', 1)
            ->whereNull('courses_taken.deleted_at')
            ->where(function ($query) {
                $query->whereNull('courses_taken.end_date')
                    ->orWhere('courses_taken.end_date', '>=', now()->toDateString());
            })
            ->pluck('courses.title');

        return $courseTitles
            ->map(function (string $title): string {
                return str_replace('-', '', Str::slug($title));
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function rolesForUser(User $user): array
    {
        return match ((int) $user->role) {
            User::AdminRole => ['admin'],
            User::EditorRole => ['editor'],
            User::GiutbokRole => ['giutbok'],
            default => ['member'],
        };
    }
}
