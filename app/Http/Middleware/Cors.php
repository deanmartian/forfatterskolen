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
        $allowCredentials = (bool) config('api.cors.allow_credentials', false);

        if ($allowWildcard && app()->environment('production')) {
            $allowedOrigins = array_values(array_filter($allowedOrigins, static fn ($value) => $value !== '*'));
            $allowWildcard = false;
        }

        $allowedOrigin = $this->resolveAllowedOrigin($origin, $allowedOrigins, $allowWildcard);

        if ($allowedOrigin) {
            header('Access-Control-Allow-Origin: '.$allowedOrigin);
            if ($allowCredentials) {
                header('Access-Control-Allow-Credentials: true');
            }
        }

        header('Vary: Origin');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept');

        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200);
        }

        return $next($request);
    }

    private function resolveAllowedOrigin(?string $origin, array $allowedOrigins, bool $allowWildcard): ?string
    {
        if (!$origin) {
            return null;
        }

        if ($allowWildcard) {
            return $origin;
        }

        foreach ($allowedOrigins as $allowedOrigin) {
            if ($origin === $allowedOrigin) {
                return $origin;
            }

            if ($this->originMatchesPattern($origin, $allowedOrigin)) {
                return $origin;
            }
        }

        return null;
    }

    private function originMatchesPattern(string $origin, string $pattern): bool
    {
        if (!str_contains($pattern, '*')) {
            return false;
        }

        $regex = '#^'.str_replace('\*', '.*', preg_quote($pattern, '#')).'$#i';

        return (bool) preg_match($regex, $origin);
    }
}
