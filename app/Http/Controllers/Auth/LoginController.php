<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\BrowserDetection;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\LearnerLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\User;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function adminLogin(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('role', 1)->first();

        if(!$user) return redirect()->back()->withErrors('Unknown email');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1])) :
            // Authentication passed...
            return redirect()->back();
        endif;


        return redirect()->back()->withInput()->withErrors('Feil passord');
    }





    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('role', 2)->first();
        if(!$user) return redirect()->back()->withErrors('Unknown email');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) :
            // Authentication passed...

            $browser = new BrowserDetection();
            $browserName     = $browser->getName();
            $platformName    = $browser->getPlatformVersion();

            $login = LearnerLogin::create([
                'user_id'       => Auth::user()->id,
                'ip'            => $request->ip(),
                'country'       => 'Norway',//AdminHelpers::ip_info($request->ip(), "Country"),
                'country_code'  => 'NO',//AdminHelpers::ip_info($request->ip(), "Country Code"),
                'provider'      => $browserName,
                'platform'      => $platformName
            ]);

            \Session::put('learner_login_id', $login->id);

            return redirect(route('learner.dashboard'));
        endif;

        return redirect()->back()->withInput()->withErrors('Feil passord');
    }



    

    public function checkoutLogin(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('role', 2)->first();
        if(!$user) return redirect()->back()->withInput()->withErrors(['login_error' => 'Unknown email']);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) :
            // Authentication passed...
            return redirect()->back();
        endif;

        return redirect()->back()->withInput()->withErrors(['login_error' => 'Feil passord']);
    }

    /** login using encrypted email
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emailLogin($email)
    {
        $email = decrypt($email);

        $user = User::where('email', $email)->where('role', 2)->first();
        if(!$user) return redirect()->route('front.home');

        Auth::loginUsingId($user->id);
        return redirect()->route('learner.dashboard');
    }

    public function logout()
    {
        Auth::logout();
        // forget all stored session
        foreach (\Session::all() as $k => $sess) {
            \Session::forget($k);
        }
        return redirect('/');
    }


    public function showFrontend()
    {
        return view('frontend.auth.login');
    }

    /**
     * Create a redirect method to facebook api.
     *
     * @return \Response
     */
    public function redirectToFacebook()
    {
        \Session::push('redirect_page',\URL::previous());
        return Socialite::driver('facebook')->redirect();
    }
    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function handleFacebookCallback()
    {

        $redirectPage = route('learner.dashboard');//\Session::get('redirect_page')[0];

        // add fields function to get specific fields *optional
        $userFacebook = Socialite::driver('facebook')
            ->fields(['name', 'first_name', 'last_name', 'email', 'gender', 'verified'])
            ->user();

        $findUser = User::where('email', $userFacebook->email)->first();

        if ($findUser) {
            Auth::login($findUser);
            return redirect($redirectPage);
        }

        $user               = new User();
        $user->first_name   = $userFacebook->user['first_name'];
        $user->last_name    = $userFacebook->user['last_name'];
        $user->email        = $userFacebook->email;
        $user->password     = bcrypt(123);
        $user->save();

        \Session::put('new_user_social', 1);
        \Session::forget('redirect_page');

        Auth::login($user);
        return redirect($redirectPage);
    }

    /**
     * Create a redirect method to google api.
     *
     * @return \Response
     */
    public function redirectToGoogle()
    {
        \Session::push('redirect_page',\URL::previous());
        return Socialite::driver('google')->redirect();
    }
    /**
     * Return a callback method from google api.
     *
     * @return callback URL from google
     */
    public function handleGoogleCallback()
    {

        $redirectPage = route('learner.dashboard');//\Session::get('redirect_page')[0];

        $userGoogle = Socialite::driver('google')
            ->stateless()
            ->user();

        $findUser = User::where('email', $userGoogle->email)->first();

        if ($findUser) {
            Auth::login($findUser);
            return redirect($redirectPage);
        }

        $user               = new User();
        $user->first_name   = $userGoogle->user['name']['givenName'];
        $user->last_name    = $userGoogle->user['name']['familyName'];
        $user->email        = $userGoogle->email;
        $user->password     = bcrypt(123);
        $user->save();

        \Session::put('new_user_social', 1);
        \Session::forget('redirect_page');

        Auth::login($user);
        return redirect($redirectPage);
    }
}
