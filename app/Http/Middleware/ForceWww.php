<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceWww
{
    public function handle(Request $request, Closure $next)
    {
        // Kun redirect forfatterskolen.no (uten subdomain) til www
        if ($request->getHost() === 'forfatterskolen.no') {
            return redirect()->to(
                str_replace('://forfatterskolen.no', '://www.forfatterskolen.no', $request->fullUrl()),
                307
            );
        }

        return $next($request);
    }
}
