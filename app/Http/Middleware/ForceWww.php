<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceWww
{
    public function handle(Request $request, Closure $next)
    {
        if (!str_starts_with($request->getHost(), 'www.') &&
            str_contains($request->getHost(), 'forfatterskolen.no')) {
            return redirect()->to(
                str_replace('://forfatterskolen.no', '://www.forfatterskolen.no', $request->fullUrl()),
                307 // 307 beholder POST-metode
            );
        }

        return $next($request);
    }
}
