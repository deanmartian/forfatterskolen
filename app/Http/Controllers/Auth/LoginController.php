<?php

namespace App\Http\Controllers\Auth;

use App\AccessToken;
use App\Address;
use App\Course;
use App\Helpers\BrowserDetection;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\LearnerLogin;
use App\Mail\SubjectBodyEmail;
use App\User;
use App\UserEmail;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function adminLogin(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->whereIn('role', [1])->first();

        if (! $user) {
            return redirect()->back()->withErrors('Unknown email');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1])) {
            // Authentication passed...
            return redirect()->back();
        }

        return redirect()->back()->withInput()->withErrors('Feil passord');
    }

    public function editorLogin(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)
            ->where(function ($query) {
                $query->whereIn('role', [3])->orWhere('admin_with_editor_access', 1);
            })->first();

        if (! $user) {
            return redirect()->back()->withErrors('Unknown email');
        }

        if ($user->is_active != 1) {
            return redirect()->back()->withErrors('Invalid User');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 3])) {
            // Authentication passed...
            return redirect()->back();
        } elseif (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'admin_with_editor_access' => 1])) {
            // Authentication passed...
            return redirect()->back();
        }

        return redirect()->back()->withInput()->withErrors('Feil passord');
    }

    public function giutbokLogin(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)
            ->where(function ($query) {
                $query->whereIn('role', [4])->orWhere('admin_with_giutbok_access', 1);
            })->first();

        if (! $user) {
            return redirect()->back()->withErrors('Unknown email');
        }

        if ($user->is_active != 1) {
            return redirect()->back()->withErrors('Invalid User');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 4])) {
            // Authentication passed...
            return redirect()->back();
        } elseif (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'admin_with_giutbok_access' => 1])) {
            // Authentication passed...
            return redirect()->back();
        }

        return redirect()->back()->withInput()->withErrors('Feil passord');
    }

    public function giutbokEmailLoginRedirect($email, $redirect_link): RedirectResponse
    {
        $email = decrypt($email);
        $redirect_link = decrypt($redirect_link);

        $user = User::where('email', $email)->where('admin_with_giutbok_access', 1)->first();
        if (! $user) {
            return redirect()->route('g-admin.dashboard');
        }

        Auth::loginUsingId($user->id);

        return redirect()->to($redirect_link);
    }

    public function editorEmailLogin($email)
    {
        $email = decrypt($email);

        $user = User::where('email', $email)
            ->where(function ($query) {
                $query->whereIn('role', [3])->orWhere('admin_with_editor_access', 1);
            })->first();

        if (! $user) {
            return response(view('editor.auth.editor_login'));
        }

        Auth::login($user);

        return redirect()->route('editor.dashboard');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'g-recaptcha-response' => 'required|captcha',
        ]);
        $user = User::where('email', $request->email)->where('role', 2)->first();
        $secondaryEmail = UserEmail::where('email', $request->email)->first();

        if (! $user && ! $secondaryEmail) {
            return redirect()->back()->withErrors('Unknown email');
        }
        if ($secondaryEmail) {
            $user = $secondaryEmail->users->first();
        }

        if (Auth::attempt(['email' => $user->email, 'password' => $request->password, 'role' => 2])) {
            // Authentication passed...

            $browser = new BrowserDetection;
            $browserName = $browser->getName();
            $platformName = $browser->getPlatformVersion();

            $login = LearnerLogin::create([
                'user_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'country' => 'Norway', // AdminHelpers::ip_info($request->ip(), "Country"),
                'country_code' => 'NO', // AdminHelpers::ip_info($request->ip(), "Country Code"),
                'provider' => $browserName,
                'platform' => $platformName,
            ]);

            \Session::put('learner_login_id', $login->id);

            return redirect(route('learner.dashboard'));
        }

        return redirect()->route('auth.login.show')->withInput()->withErrors('Feil passord');
    }

    public function selfPublishingLogin(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        /* ->where('is_self_publishing_learner', 1) */
        $user = User::where('email', $request->email)->where('role', 2)->first();
        $secondaryEmail = UserEmail::where('email', $request->email)->first();

        if (! $user && ! $secondaryEmail) {
            return redirect()->back()->withErrors('Unknown email');
        }
        if ($secondaryEmail) {
            $user = $secondaryEmail->users->first();
        }

        if (Auth::attempt(['email' => $user->email, 'password' => $request->password, 'role' => 2])) {
            // Authentication passed...

            $browser = new BrowserDetection;
            $browserName = $browser->getName();
            $platformName = $browser->getPlatformVersion();

            $login = LearnerLogin::create([
                'user_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'country' => 'Norway', // AdminHelpers::ip_info($request->ip(), "Country"),
                'country_code' => 'NO', // AdminHelpers::ip_info($request->ip(), "Country Code"),
                'provider' => $browserName,
                'platform' => $platformName,
            ]);

            \Session::put('learner_login_id', $login->id);
            \Session::put('current-portal', 'self-publishing');

            return redirect(route('learner.dashboard'));
        }

        return redirect()->route('auth.login.show')->withInput()->withErrors('Feil passord');
    }

    public function checkoutLogin(LoginRequest $request)
    {
        if ($request->ajax()) {

            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->where('role', 2)->first();
            if (! $user) {
                return response()->json([
                    'error' => 'Unknown email',
                ], 401);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) {
                $user = Auth::user();
                $user['address'] = $user->address;

                // check if course id is passed
                if ($request->has('course_id')) {
                    $course = Course::find($request->course_id);
                    $course_packages = $course->packages->pluck('id')->toArray();
                    $courseTaken = \Auth::user()->coursesTaken()->where('user_id', \Auth::user()->id)
                        ->whereIn('package_id', $course_packages)->first();

                    // check if the user already have the course
                    if ($courseTaken) {
                        $user['course_link'] = route('learner.course.show', $courseTaken->id);
                    }
                }

                return response()->json(['success' => 'You successfully log in', 'user' => $user], 200);
            }

            return response()->json([
                'error' => 'Feil passord',
            ], 401);

        } else {
            $user = User::where('email', $request->email)->where('role', 2)->first();
            if (! $user) {
                return redirect()->back()->withInput()->withErrors(['login_error' => 'Unknown email']);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) {
                // Authentication passed...
                return redirect()->back();
            }

            return redirect()->back()->withInput()->withErrors(['login_error' => 'Feil passord']);
        }
    }

    /** login using encrypted email
     */
    public function emailLogin($email, Request $request): RedirectResponse
    {
        $email = decrypt($email);

        $user = User::where('email', $email)->where('role', 2)->first();
        if (! $user) {
            return redirect()->route('front.home');
        }

        Auth::login($user);
        if ($request->has('redirect')) {
            if ($request->get('redirect') === 'upgrade') {
                return redirect()->route('learner.upgrade');
            }
        }

        return redirect()->route('learner.dashboard');
    }

    public function emailLoginNormal($email, Request $request): RedirectResponse
    {

        $user = User::where('email', $email)->where('role', 2)->first();
        if (! $user) {
            return redirect()->route('front.home');
        }

        Auth::login($user);
        if ($request->has('redirect')) {
            if ($request->get('redirect') === 'upgrade') {
                return redirect()->route('learner.upgrade');
            }
        }

        return redirect()->route('learner.dashboard');
    }

    /**
     * Email login with redirect link
     */
    public function emailLoginRedirect($email, $redirect_link): RedirectResponse
    {
        $email = decrypt($email);
        $redirect_link = decrypt($redirect_link);

        $user = User::where('email', $email)->where('role', 2)->first();
        if (! $user) {
            return redirect()->route('front.home');
        }

        Auth::loginUsingId($user->id);

        return redirect()->to($redirect_link);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        // forget all stored session
        foreach (\Session::all() as $k => $sess) {
            \Session::forget($k);
        }

        return redirect('/');
    }

    public function showFrontend(): View
    {
        return view('frontend.auth.login');
    }

    public function showSelfPublishing(): View
    {
        return view('frontend.auth.self-publishing-login');
    }

    /**
     * Create a redirect method to facebook api.
     */
    public function redirectToFacebook()/* : Response */
    {
        $prevUrl = explode('?', \URL::previous());
        $queryString = count(\request()->query()) ? '?'.http_build_query(\request()->query()) : '';
        $redirectPage = $prevUrl[0].$queryString;
        \Session::push('redirect_page', $redirectPage);

        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Return a callback method from facebook api.
     *
     * @return callable URL from facebook
     */
    public function handleFacebookCallback()/* : RedirectResponse */
    {
        $redirectPage = \Session::has('redirect_page') ? \Session::get('redirect_page')[0]
            : route('learner.dashboard');

        // add fields function to get specific fields *optional
        $userFacebook = Socialite::driver('facebook')
            ->fields(['name', 'first_name', 'last_name', 'email', 'gender', 'verified'])
            ->user();

        $findUser = User::where('email', $userFacebook->email)->first();

        if ($findUser) {
            Auth::login($findUser);

            return redirect($redirectPage);
        }

        $user = new User;
        $user->first_name = $userFacebook->user['first_name'];
        $user->last_name = $userFacebook->user['last_name'];
        $user->email = $userFacebook->email;
        $user->password = bcrypt(123);
        $user->save();

        \Session::put('new_user_social', 1);
        \Session::forget('redirect_page');

        Auth::login($user);

        return redirect($redirectPage);
    }

    /**
     * Create a redirect method to google api.
     */
    public function redirectToGoogle()/* : Response */
    {
        $prevUrl = explode('?', \URL::previous());
        $queryString = count(\request()->query()) ? '?'.http_build_query(\request()->query()) : '';
        $redirectPage = $prevUrl[0].$queryString;
        \Session::push('redirect_page', $redirectPage);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Return a callback method from google api.
     *
     * @return callable URL from google
     */
    public function handleGoogleCallback()/* : RedirectResponse */
    {

        $redirectPage = \Session::has('redirect_page') ? \Session::get('redirect_page')[0]
            : route('learner.dashboard');

        $userGoogle = Socialite::driver('google')
            ->stateless()
            ->user();

        $findUser = User::where('email', $userGoogle->email)->first();

        if ($findUser) {
            Auth::login($findUser);

            return redirect($redirectPage);
        }

        $user = new User;
        $user->first_name = $userGoogle->user['name']['givenName'];
        $user->last_name = $userGoogle->user['name']['familyName'];
        $user->email = $userGoogle->email;
        $user->password = bcrypt(123);
        $user->save();

        \Session::put('new_user_social', 1);
        \Session::forget('redirect_page');

        Auth::login($user);

        return redirect($redirectPage);
    }

    /**
     * Generate a token for checking before the actual login
     */
    public function crossDomainToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken(); // get the bearer token on the header request
        // decode the passed jwt token
        $jwt = JWT::decode($token, config('services.jwt.secret'), ['HS256']);

        try {

            AccessToken::create([
                'jti' => $jwt->jti,
                'iat' => $jwt->iat,
                'exp' => $jwt->exp,
            ]);

            // return the generated jwt response
            return response()->json([
                $jwt,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 422);

        }

    }

    /**
     * Login from other domain
     */
    public function crossDomainLogin(Request $request): JsonResponse
    {
        $checkToken = AccessToken::where('jti', $request->jti)->first();
        $now = Carbon::now()->timestamp;

        if (! $checkToken) {
            return response()->json([
                'message' => 'Invalid token',
            ], 422);
        }

        if ($checkToken->iat >= $now && $checkToken->exp <= $now) {
            return response()->json([
                'message' => 'Token expired',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            $userEmail = UserEmail::where('email', $request->email)->first();
            if (! $userEmail) {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

            } else {
                $user = User::find($userEmail->user_id);
            }
        }

        $encode_email = $user->email;

        return response()->json([
            'redirect_url' => route('auth.login.email-normal', $encode_email),
        ]);
    }

    public function vippsLogin($state = 'login_state')
    {
        $query = [
            // 'client_id' => config('services.vipps.client_id'),
            'client_id' => config('services.vipps.client_id'),
            'response_type' => 'code',
            'state' => $state,
            'redirect_uri' => config('services.vipps.login_redirect_uri'),
            'scope' => config('services.vipps.login_scope'),
        ];

        $vipps_auth_url = config('services.vipps.login_auth_link');

        if ($state === 'checkout_state') {
            return $vipps_auth_url.'?'.http_build_query($query);
        }

        return redirect()->to($vipps_auth_url.'?'.http_build_query($query));
    }

    /**
     * This is where the vipps would redirect after getting auth
     * Use the generated code to get access_token
     *
     * @return $this
     */
    public function vippsLoginRedirect(Request $request)
    {

        /*$vipps_credentials = base64_encode(config('services.vipps.client_id') . ":"
            . config('services.vipps.client_secret'));*/
        $vipps_credentials = base64_encode(config('services.vipps.client_id').':'
            .config('services.vipps.client_secret'));

        $long_url = config('services.vipps.login_token_link');

        $code = $request->code;
        $redirect_url = config('services.vipps.login_redirect_uri');

        $body = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirect_url,
        ];

        $header = [];
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/x-www-form-urlencoded';
        $header[] = 'Authorization: Basic '.$vipps_credentials;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $long_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body)); // use HTTP POST to send form data
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            return redirect()->route('auth.login.show')->withInput()->withErrors($decoded_response->error_description);
        }

        return $this->vippsUserInfo($decoded_response->access_token, $request->state);
    }

    /**
     * Get the user info from vipps
     */
    public function vippsUserInfo($access_token, $state): RedirectResponse
    {
        $long_url = config('services.vipps.login_user_info_link');

        $header = [];
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/x-www-form-urlencoded';
        $header[] = 'Authorization: Bearer '.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $long_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            return redirect()->route('auth.login.show')->withInput()->withErrors($decoded_response->title);
        }

        if (! $decoded_response->email_verified) {
            return redirect()->route('auth.login.show')->withInput()->withErrors('Email not yet verified.');
        }

        $user = User::where('email', $decoded_response->email)->where('role', 2)->first();
        $secondaryEmail = UserEmail::where('email', $decoded_response->email)->first();

        if (! $user && ! $secondaryEmail) {
            $user = new User;
            $user->first_name = $decoded_response->given_name;
            $user->last_name = $decoded_response->family_name;
            $user->email = $decoded_response->email;
            $user->password = bcrypt(123);
            $user->save();

            Address::create([
                'user_id' => $user->id,
                'phone' => $decoded_response->phone_number,
                'street' => $decoded_response->address->street_address,
                'city' => $decoded_response->address->region,
                'zip' => $decoded_response->address->postal_code,
                'vipps_phone_number' => $decoded_response->phone_number,
            ]);

            $actionText = 'Se dine kurs';
            $actionUrl = \URL::to('/account/course');

            $to = $user->email; //
            $emailData = [
                'email_subject' => 'Velkommen til Forfatterskolen',
                'email_message' => view('emails.registration', compact('actionText', 'actionUrl', 'user'))->render(),
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];

            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            \Session::put('new_user_social', 1);
        }

        if (! $user && $secondaryEmail) {
            $user = $secondaryEmail->users->first();
        }

        Auth::login($user);

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            ['vipps_phone_number' => $decoded_response->phone_number]
        );

        if ($state === 'checkout_state') {
            $vipps = \Session::get('vipps_checkout');

            if ($vipps['item_type'] === 'course') {
                return redirect()->route('front.course.checkout.process-vipps', $vipps['course_id']);
            }

            if ($vipps['item_type'] === 'shop-manuscript') {
                $vipps = \Session::get('vipps_checkout');

                return redirect()->route('front.shop-manuscript.checkout.process-vipps', $vipps['shop_manuscript_id']);
            }
        }

        return redirect(route('learner.dashboard'));
    }
}
