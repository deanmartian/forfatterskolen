<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Admin
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return response(view('backend.auth.login'));
            }
        } else {
            // allow role 4 = giutbok admin to access some page
            if (! in_array($this->auth->user()->role, [1, 4])) {
                $this->auth->logout();
                echo 'Forbidden <br />';

                return redirect('/');
            }
        }

        return $next($request);
    }
}
