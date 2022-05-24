<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Editor
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
     * @param  Guard  $auth
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
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) :
            if ($request->ajax()) :
                return response('Unauthorized.', 401);
            else :
                return response(view('editor.auth.editor_login'));
            endif;
        else :
            if (($this->auth->user()->role != 3 && $this->auth->user()->admin_with_editor_access != 1)
                || $this->auth->user()->is_active != 1) :
                $this->auth->logout();
                echo "Forbidden <br />";
                return redirect('/');
            endif;
        endif;

        return $next($request);
    }
}
