<?php

namespace App\Http\Controllers\Api\V1;

use App\ApiRefreshToken;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RefreshRequest;
use App\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials.', 'unauthorized', 401);
        }

        $user = Auth::user();

        if ($user->is_active === 0) {
            return $this->errorResponse('User is inactive.', 'forbidden', 403);
        }

        [$accessToken, $expiresIn] = $this->createAccessToken($user);
        $refreshToken = $this->createRefreshToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
        ]);
    }

    public function refresh(RefreshRequest $request): JsonResponse
    {
        $tokenHash = $this->hashToken($request->input('refresh_token'));

        $refreshToken = ApiRefreshToken::where('token_hash', $tokenHash)->first();

        if (! $refreshToken || $refreshToken->revoked_at) {
            return $this->errorResponse('Invalid refresh token.', 'unauthorized', 401);
        }

        if (Carbon::now()->greaterThan($refreshToken->expires_at)) {
            return $this->errorResponse('Refresh token expired.', 'unauthorized', 401);
        }

        $user = User::find($refreshToken->user_id);

        if (! $user) {
            return $this->errorResponse('User not found.', 'unauthorized', 401);
        }

        if ($user->is_active === 0) {
            return $this->errorResponse('User is inactive.', 'forbidden', 403);
        }

        [$accessToken, $expiresIn] = $this->createAccessToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
        ]);
    }

    public function logout(RefreshRequest $request): JsonResponse
    {
        $tokenHash = $this->hashToken($request->input('refresh_token'));

        $refreshToken = ApiRefreshToken::where('token_hash', $tokenHash)->first();

        if (! $refreshToken || $refreshToken->revoked_at) {
            return response()->json(['revoked' => true]);
        }

        $refreshToken->update(['revoked_at' => Carbon::now()]);

        return response()->json(['revoked' => true]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->attributes->get('api_user');
        $certificates = DB::table('course_certificates')
            ->leftJoin('courses', 'course_certificates.course_id', '=', 'courses.id')
            ->leftJoin('packages', 'packages.id', '=', 'course_certificates.package_id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->select('course_certificates.*', 'courses.title as course_title')
            ->where('courses.completed_date', '<=', Carbon::now())
            ->whereNotNull('courses.issue_date')
            ->whereNotNull('course_certificates.package_id')
            ->where('courses_taken.user_id', $user->id)
            // ->whereNull('courses_taken.deleted_at') //remove this to not show deleted courses_taken
            ->groupBy('course_certificates.id')
            ->get();

        return response()->json([
            'id' => $user->id,
            'name' => trim($user->first_name.' '.$user->last_name),
            'email' => $user->email,
            'roles' => $this->rolesForUser($user),
            'certificates' => $certificates,
        ]);
    }

    private function createAccessToken(User $user): array
    {
        $issuedAt = Carbon::now()->timestamp;
        $expiresAt = Carbon::now()->addMinutes(config('api.jwt.access_ttl_minutes'))->timestamp;

        $payload = [
            'iss' => config('app.url'),
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'jti' => Str::uuid()->toString(),
        ];

        $token = JWT::encode($payload, config('services.jwt.secret'), 'HS256');

        return [$token, $expiresAt - $issuedAt];
    }

    private function createRefreshToken(User $user): string
    {
        $plainToken = Str::random(64);
        $expiresAt = Carbon::now()->addDays(config('api.jwt.refresh_ttl_days'));

        ApiRefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => $this->hashToken($plainToken),
            'expires_at' => $expiresAt,
        ]);

        return $plainToken;
    }

    private function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    private function rolesForUser(User $user): array
    {
        $roles = [];
        $map = [
            User::AdminRole => 'admin',
            User::LearnerRole => 'learner',
            User::EditorRole => 'editor',
            User::GiutbokRole => 'giutbok',
        ];

        if (isset($map[$user->role])) {
            $roles[] = $map[$user->role];
        }

        return $roles;
    }

}
