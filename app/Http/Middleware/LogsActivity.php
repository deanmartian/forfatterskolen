<?php

namespace App\Http\Middleware;

use App\Helpers\BrowserDetection;
use App\LearnerLogin;
use App\LearnerLoginActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogsActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check if user is login before adding activity log
        // learner_login_id set in login controller
        if (auth()->check()) {

            // check first if there's a session to avoid error
            if (is_null(\Session::get('learner_login_id')) || ! \Session::has('learner_login_id')) {
                $browser = new BrowserDetection;
                $browserName = $browser->getName();
                $platformName = $browser->getPlatformVersion();

                $login = LearnerLogin::create([
                    'user_id' => \Auth::user()->id,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'country' => 'Norway', // AdminHelpers::ip_info($request->ip(), "Country"),
                    'country_code' => 'NO', // AdminHelpers::ip_info($request->ip(), "Country Code"),
                    'provider' => $browserName,
                    'platform' => $platformName,
                ]);

                \Session::put('learner_login_id', $login->id);

            }

            LearnerLoginActivity::create([
                'learner_login_id' => \Session::get('learner_login_id'),
                'activity' => "User visited {$request->fullUrl()} | {$request->method()}",
            ]);
        }

        return $next($request);
    }
}
