<?php

namespace App\Http\Controllers\Api\V1;

use App\FreeManuscript;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class FreeManuscriptController extends Controller
{
    private const RATE_LIMIT_ATTEMPTS = 5;
    private const RATE_LIMIT_DECAY_SECONDS = 3600;

    public function store(Request $request): JsonResponse
    {
        $rateLimitKey = sprintf('free-manuscripts:ip:%s', $request->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, self::RATE_LIMIT_ATTEMPTS)) {
            return response()->json([
                'message' => 'Too many submissions. Please try again later.',
            ], 429);
        }

        RateLimiter::hit($rateLimitKey, self::RATE_LIMIT_DECAY_SECONDS);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'first_name' => 'required|alpha_spaces',
            'last_name' => 'required|alpha_spaces',
            'genre' => 'required',
            'text' => 'required|no_links',
        ]);

        $validator->after(function ($validator) use ($request) {
            $wordCount = FrontendHelpers::get_num_of_words($request->input('text', ''));
            if ($wordCount > 500) {
                $validator->errors()->add('text', trans('site.content-max-500-words'));
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = strtolower(trim($request->input('email')));

        if (FreeManuscript::where('email', $email)->exists()) {
            return response()->json([
                'message' => 'Submission already exists for this email.',
                'errors' => [
                    'email' => ['Submission already exists for this email.'],
                ],
            ], 422);
        }

        $freeManuscript = new FreeManuscript([
            'name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $email,
            'genre' => $request->input('genre'),
            'from' => 'Lovable',
            'content' => $request->input('text'),
            'deadline' => Carbon::today()->addDays(6),
        ]);

        if (Schema::hasColumn('free_manuscripts', 'received_at')) {
            $freeManuscript->received_at = now();
        }

        $freeManuscript->save();

        return response()->json([
            'id' => $freeManuscript->id,
        ], 201);
    }
}
