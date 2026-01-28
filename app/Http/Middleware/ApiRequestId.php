<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id') ?: (string) Str::uuid();

        $request->attributes->set('request_id', $requestId);

        $response = $next($request);

        $response->headers->set('X-Request-Id', $requestId);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            if (is_array($data) && ! array_is_list($data)) {
                if (! array_key_exists('request_id', $data)) {
                    $data['request_id'] = $requestId;
                }

                $response->setData($data);
            }
        }

        return $response;
    }
}
