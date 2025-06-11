<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function adminLogin(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('role', 1)->first();

        if (! $user) {
            return redirect()->back()->withErrors('Unknown email');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1])) {
            // Authentication passed...
            return redirect()->back();
        }

        return redirect()->back()->withInput()->withErrors('Wrong password');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('role', 2)->first();
        if (! $user) {
            return redirect()->back()->withErrors('Unknown email');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) {
            // Authentication passed...
            return redirect(route('learner.course'));
        }

        return redirect()->back()->withInput()->withErrors('Wrong password');
    }

    public function checkoutLogin(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('role', 2)->first();
        if (! $user) {
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Unknown email']);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) {
            // Authentication passed...
            return redirect()->back();
        }

        return redirect()->back()->withInput()->withErrors(['login_error' => 'Wrong password']);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function showFrontend()
    {
        return view('frontend.auth.login');
    }
}
