<?php

namespace App\Http\Controllers\Api;

use App\ApiCommunitySsoCode;
use App\Http\Controllers\Api\V1\ApiController;
use App\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommunitySsoController extends ApiController
{
    public function issueCode(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $code = Str::random(64);
        $expiresAt = Carbon::now()->addSeconds((int) config('api.community_sso.code_ttl_seconds', 120));

        ApiCommunitySsoCode::query()->create([
            'user_id' => $user->id,
            'code_hash' => hash('sha256', $code),
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'code' => $code,
            'expires_at' => $expiresAt->toISOString(),
        ]);
    }

    public function exchangeCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $codeHash = hash('sha256', $data['code']);

        /** @var ApiCommunitySsoCode|null $record */
        $record = ApiCommunitySsoCode::query()
            ->where('code_hash', $codeHash)
            ->first();

        if (! $record) {
            return $this->errorResponse('Invalid code.', 'unauthorized', 401);
        }

        if ($record->used_at !== null) {
            return $this->errorResponse('Code already used.', 'unauthorized', 401);
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            return $this->errorResponse('Code expired.', 'unauthorized', 401);
        }

        $user = User::query()->find($record->user_id);

        if (! $user || $user->is_active === 0) {
            return $this->errorResponse('User unavailable.', 'forbidden', 403);
        }

        $record->update([
            'used_at' => Carbon::now(),
        ]);

        [$accessToken, $expiresIn] = $this->createAccessToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'token_type' => 'Bearer',
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
}
