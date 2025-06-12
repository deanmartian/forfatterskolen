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
        header('Access-Control-Allow-Origin: https://apitest.vipps.no');
        header('Access-Control-Allow-Origin: https://api.vipps.no');

        return $next($request);
    }
}
