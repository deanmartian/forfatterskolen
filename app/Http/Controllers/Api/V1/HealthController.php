<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function show(): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'env' => app()->environment(),
            'time' => now()->toIso8601String(),
        ]);
    }
}
