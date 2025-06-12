<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        header('Access-Control-Allow-Origin: https://apitest.vipps.no');
        header('Access-Control-Allow-Origin: https://api.vipps.no');

        return $next($request);
    }
}
