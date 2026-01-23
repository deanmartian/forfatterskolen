<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiJwtAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'error' => [
                    'message' => 'Missing bearer token.',
                    'code' => 'unauthorized',
                ],
            ], 401);
        }

        try {
            $payload = JWT::decode($token, new Key(config('services.jwt.secret'), 'HS256'));
        } catch (\Exception $exception) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid or expired token.',
                    'code' => 'unauthorized',
                ],
            ], 401);
        }

        $user = User::find($payload->sub ?? null);

        if (! $user) {
            return response()->json([
                'error' => [
                    'message' => 'User not found.',
                    'code' => 'unauthorized',
                ],
            ], 401);
        }

        if ($user->is_active === 0) {
            return response()->json([
                'error' => [
                    'message' => 'User is inactive.',
                    'code' => 'forbidden',
                ],
            ], 403);
        }

        $request->attributes->set('api_user', $user);
        $request->attributes->set('api_token_payload', $payload);

        return $next($request);
    }
}
