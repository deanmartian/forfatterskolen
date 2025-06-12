<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPageAccess
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $page_id): Response
    {
        // check if the admin have set page access and if no access to the page id passed
        if (\Auth::user()->pageAccess->count() && ! in_array($page_id, \Auth::user()->pageAccess->pluck('page_id')->toArray())) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}
