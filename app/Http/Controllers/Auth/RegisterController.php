<?php

namespace App\Http\Controllers\Auth;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use App\User;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;
use Str;

class RegisterController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'register_first_name' => 'required|string|max:255',
            'register_last_name' => 'required|string|max:255',
            'register_email' => 'required|string|email|max:255|unique:users,email',
            'register_password' => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = new User;
        $user->first_name = $request->register_first_name;
        $user->last_name = $request->register_last_name;
        $user->email = $request->register_email;
        $user->password = bcrypt($request->register_password);
        // $user->email_verification_token = Str::random(32);
        $user->save();

        // Send welcome email
        $actionText = 'Se dine kurs';
        $actionUrl = \URL::to('/account/course');
        $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // mail($user->email, 'Velkommen til Forfatterskolen', view('emails.registration', compact('actionText', 'actionUrl', 'user')), $headers);
        /*AdminHelpers::send_email('Velkommen til Forfatterskolen',
            'post@forfatterskolen.no', $user->email, view('emails.registration', compact('actionText', 'actionUrl', 'user')));*/

        $to = $user->email; //
        $emailData = [
            'email_subject' => 'Velkommen til Forfatterskolen',
            'email_message' => view('emails.registration', compact('actionText', 'actionUrl', 'user'))->render(),
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        /* $verificationUrl = route('email.verify', ['token' => $user->email_verification_token]);

        $to = $user->email;
        $emailData = [
            'email_subject' => 'Velkommen til Forfatterskolen - Bekreft e-post',
            'email_message' => view('emails.verify_email', compact('verificationUrl', 'user'))->render(),
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => NULL
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Registration success, please verify you emai.'),
            'alert_type' => 'success'
        ]); */
        Auth::login($user);

        if ($request->has('redirect')) {
            if ($request->redirect === 'redeem-gift') {
                return redirect(route('front.gift.show-redeem'));
            } else {
                return redirect()->to($request->redirect);
            }
        }

        return redirect(route('learner.course'));
    }

    public function verifyEmail($token): RedirectResponse
    {
        $user = User::where('email_verification_token', $token)->first();

        if (! $user) {
            return redirect()->route('auth.login.show')->with([
                'errors' => AdminHelpers::createMessageBag('Invalid verification token.'),
                'alert_type' => 'danger',
            ]);
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null; // Clear the token after verification
        $user->save();

        // Send welcome email
        $actionText = 'Se dine kurs';
        $actionUrl = \URL::to('/account/course');
        $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $to = $user->email; //
        $emailData = [
            'email_subject' => 'Velkommen til Forfatterskolen',
            'email_message' => view('emails.registration', compact('actionText', 'actionUrl', 'user'))->render(),
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        Auth::login($user);

        return redirect(route('learner.course'));
    }
}
