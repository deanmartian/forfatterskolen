<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\CourseApplication;
use App\User;
use App\Http\FrontendHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CourseApplicationController extends ApiController
{
    public function show(int $id): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        if (! $course->is_free && (! FrontendHelpers::isCourseActive($course) || $course->packages()->count() === 0)) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        $lovableBase = rtrim((string) config('api.lovable_url'), '/');

        if (! $course->pay_later_with_application) {
            return response()->json([
                'success' => true,
                'action' => 'redirect_checkout',
                'redirect_url' => $lovableBase.'/skrivekurs/'.$course->id.'/checkout',
            ]);
        }

        return response()->json([
            'success' => true,
            'action' => 'show_application',
            'redirect_url' => $lovableBase.'/skrivekurs/'.$course->id.'/application',
        ]);
    }

    public function store(int $id, Request $request): JsonResponse
    {
        $course = Course::find($id);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        if (! $course->is_free && (! FrontendHelpers::isCourseActive($course) || $course->packages()->count() === 0)) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        $lovableBase = rtrim((string) config('api.lovable_url'), '/');

        if (! $course->pay_later_with_application) {
            return response()->json([
                'success' => true,
                'action' => 'redirect_checkout',
                'redirect_url' => $lovableBase.'/skrivekurs/'.$course->id.'/checkout',
            ]);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|alpha_spaces',
            'last_name' => 'required|alpha_spaces',
            'phone' => 'required',
            'manuscript' => 'required|file|mimes:odt,pdf,doc,docx',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (! $user) {
            $user = User::create([
                'email' => $validated['email'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'password' => Hash::make((string) str()->random(40)),
            ]);
        }

        Auth::login($user);

        $package = $course->packagesIsShow()->first();
        if (! $package) {
            return $this->errorResponse('Course package not found.', 'package_not_found', 404);
        }

        $existingApplication = CourseApplication::query()
            ->where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->exists();

        if ($existingApplication) {
            return $this->errorResponse(trans('site.duplicate-application-message'), 'duplicate_application', 422);
        }

        $filePath = FrontendHelpers::saveFile($request, 'course-application', 'manuscript');

        $courseApplication = CourseApplication::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'file_path' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'action' => 'application_submitted',
            'application_id' => $courseApplication->id,
            'redirect_url' => $lovableBase.'/skrivekurs/'.$course->id.'/application/thank-you',
        ], 201);
    }
}
