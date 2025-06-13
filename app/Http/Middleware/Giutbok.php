<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Giutbok
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
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return response(view('giutbok.auth.login'));
            }
        } else {
            if (($this->auth->user()->role != 4 && $this->auth->user()->admin_with_giutbok_access != 1)
                || $this->auth->user()->is_active != 1) {
                $this->auth->logout();
                echo 'Forbidden <br />';

                return redirect('/');
            }
        }

        return $next($request);
    }
}
