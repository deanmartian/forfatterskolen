<?php

namespace App\Http\Middleware;

use AdminHelpers;
use Auth;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Learner
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
                return response(view('frontend.auth.login'));
            }
        } else {
            $user = $this->auth->user();

            // Check if the user has the correct role
            if ($user->role != 2) {
                Auth::logout();
                echo 'Forbidden <br />';

                return redirect('/');
            }

            // Check if the user's email is verified
            /* if (is_null($user->email_verified_at)) {
                Auth::logout();
                return redirect()->route('auth.login.show')->with([
                    'errors' => AdminHelpers::createMessageBag('Your email is not verified. Please verify your email to continue.'),
                    'alert_type' => 'danger'
                ]);
            } */
        }

        return $next($request);
    }
}
