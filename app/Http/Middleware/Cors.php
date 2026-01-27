<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = array_filter(array_map('trim', explode(',', config('api.cors.lovable_origins'))));
        $origin = $request->headers->get('Origin');
        $allowWildcard = in_array('*', $allowedOrigins, true);

        if ($allowWildcard && app()->environment('production')) {
            $allowedOrigins = array_values(array_filter($allowedOrigins, static fn ($value) => $value !== '*'));
            $allowWildcard = false;
        }

        if ($origin && ($allowWildcard || in_array($origin, $allowedOrigins, true))) {
            header('Access-Control-Allow-Origin: '.$origin);
        }

        header('Vary: Origin');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept');
        header('Access-Control-Allow-Credentials: true');

        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204);
        }

        return $next($request);
    }
}
