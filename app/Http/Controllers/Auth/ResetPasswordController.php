<?php

namespace App\Http\Controllers\Auth;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Mail\PasswordResetEmail;
use App\Mail\SubjectBodyEmail;
use App\PasswordReset;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Mail;
use Validator;

class ResetPasswordController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'reset_email' => 'required|string|email|max:255',
        ]);
    }

    protected function update_validator(array $data)
    {
        return Validator::make($data, [
            'password' => 'required|string|confirmed|max:255',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $exists = User::where('email', $request->reset_email)->where('role', 2)->first();
        if ($exists) {
            $i = 0;
            while ($i == 0) {
                $token = Str::random(60);
                $token_used = PasswordReset::where('token', $token)->first();
                if (! $token_used) {
                    break;
                }
            }

            $passwordReset = new PasswordReset;
            $passwordReset->email = $request->reset_email;
            $passwordReset->token = $token;
            $passwordReset->save();

            // send password reset link to email
            $actionText = 'Tilbakestille Passord';
            $actionUrl = url('/auth/passwordreset').'/'.$passwordReset->token;
            $level = 'default';
            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // mail($request->reset_email, 'Forespørsel om å tilbakestille passordet ditt', view('emails.passwordreset', compact('actionText', 'actionUrl', 'level')), $headers);
            /*AdminHelpers::send_email('Forespørsel om å tilbakestille passordet ditt',
                'postmail@forfatterskolen.no', $request->reset_email, view('emails.passwordreset', compact('actionText', 'actionUrl', 'level')));*/
            $to = $request->reset_email; //
            $emailData = [
                'email_subject' => 'Forespørsel om å tilbakestille passordet ditt',
                'email_message' => view('emails.passwordreset', compact('actionText', 'actionUrl', 'level'))->render(),
                'from_name' => '',
                'from_email' => 'postmail@forfatterskolen.no',
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            // Mail::to($request->reset_email)->send(new PasswordResetEmail($passwordReset));

            if ($request->has('redirect')) {
                return redirect()->to($request->redirect)
                    ->with(['passwordreset_success' => 'Vi har sendt en passord tilbakestillingslink til din epost.']);
            }

            return redirect()->back()->with(['passwordreset_success' => 'Vi har sendt en passord tilbakestillingslink til din epost.']);
        } else {
            return redirect()->route('auth.login.show', 't=passwordreset')->withErrors("We can't find the email in our records.");
        }
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $exists = User::where('email', $request->email)->where('role', 1)->first();

        if ($exists) {
            $i = 0;
            while ($i == 0) {
                $token = Str::random(60);
                $token_used = PasswordReset::where('token', $token)->first();
                if (! $token_used) {
                    break;
                }
            }

            $passwordReset = new PasswordReset;
            $passwordReset->email = $request->email;
            $passwordReset->token = $token;
            $passwordReset->save();

            $actionUrl = route('admin.passwordreset.form', $token);
            $to = $request->email;

            $this->sendEmail($actionUrl, $to);

            return redirect()->back()->with(['status' => 'Vi har sendt en passord tilbakestillingslink til din epost.']);
        }

        return redirect()->back()->withErrors("We can't find the email in our records.");
    }

    public function editorStore(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $exists = User::where('email', $request->email)->where('admin_with_editor_access', 1)->first();

        if ($exists) {
            $i = 0;
            while ($i == 0) {
                $token = Str::random(60);
                $token_used = PasswordReset::where('token', $token)->first();
                if (! $token_used) {
                    break;
                }
            }

            $passwordReset = new PasswordReset;
            $passwordReset->email = $request->email;
            $passwordReset->token = $token;
            $passwordReset->save();

            $actionUrl = route('editor.passwordreset.form', $token);
            $to = $request->email;

            $this->sendEmail($actionUrl, $to);

            return redirect()->back()->with(['status' => 'Vi har sendt en passord tilbakestillingslink til din epost.']);
        }

        return redirect()->back()->withErrors("We can't find the email in our records.");
    }

    public function resetForm($token): View
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();

        return view('frontend.auth.passwordreset', compact('passwordReset'));
    }

    public function adminResetForm($token): View
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();

        return view('backend.auth.passwordreset', compact('passwordReset'));
    }

    public function editorResetForm($token): View
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();

        return view('editor.auth.passwordreset', compact('passwordReset'));
    }

    public function updatePassword($token, Request $request): RedirectResponse
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        $validator = $this->update_validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->password;
        $user->save();

        $passwordReset = PasswordReset::where('email', $passwordReset->email)->delete();

        return redirect(route('frontend.login.store'));
    }

    public function adminUpdatePassword($token, Request $request): RedirectResponse
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        $validator = $this->update_validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->password;
        $user->save();

        $passwordReset = PasswordReset::where('email', $passwordReset->email)->delete();

        return redirect()->to('/')->with(['password_change_success' => 'Password changed successfully.']);
    }

    public function editorUpdatePassword($token, Request $request): RedirectResponse
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        $validator = $this->update_validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->password;
        $user->save();

        $passwordReset = PasswordReset::where('email', $passwordReset->email)->delete();

        return redirect()->to('/')->with(['password_change_success' => 'Password changed successfully.']);
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! \Hash::check($request->current_password, $user->password)) {

            return redirect()->route('auth.login.show', 't=password-change')
                ->withInput()
                ->withErrors("User credentials doesn't match");
        }

        $user->fill([
            'password' => \Hash::make($request->password),
        ])->save();

        return redirect()->route('auth.login.show', 't=password-change')
            ->with(['password_change_success' => 'Password changed successfully.']);
    }

    private function sendEmail($actionUrl, $to)
    {
        // send password reset link to email
        $actionText = 'Tilbakestille Passord';
        $level = 'default';
        $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $emailData = [
            'email_subject' => 'Forespørsel om å tilbakestille passordet ditt',
            'email_message' => view('emails.passwordreset', compact('actionText', 'actionUrl', 'level'))->render(),
            'from_name' => '',
            'from_email' => 'postmail@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
    }
}
