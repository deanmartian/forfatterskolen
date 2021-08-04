<?php

namespace App\Http\Controllers\Auth;

use App\Http\AdminHelpers;
use App\Http\Requests\ChangePasswordRequest;
use App\Mail\SubjectBodyEmail;
use Illuminate\Http\Request;
use App\Mail\PasswordResetEmail;
use App\Http\Controllers\Controller;
use App\User;
use App\PasswordReset;
use Validator;
use Illuminate\Support\Str;
use Mail;

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



    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $exists = User::where('email', $request->reset_email)->where('role', 2)->first();
        if( $exists ) :
            $i = 0;
            while( $i == 0 ) :
                $token = Str::random(60);
                $token_used = PasswordReset::where('token', $token)->first();
                if( !$token_used ) break;
            endwhile;

            $passwordReset = new PasswordReset();
            $passwordReset->email = $request->reset_email;
            $passwordReset->token = $token;
            $passwordReset->save();

            // send password reset link to email
            $actionText = 'Tilbakestille Passord';
            $actionUrl = url('/auth/passwordreset'). '/' . $passwordReset->token;
            $level = 'default';
            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            //mail($request->reset_email, 'Forespørsel om å tilbakestille passordet ditt', view('emails.passwordreset', compact('actionText', 'actionUrl', 'level')), $headers);
            /*AdminHelpers::send_email('Forespørsel om å tilbakestille passordet ditt',
                'postmail@forfatterskolen.no', $request->reset_email, view('emails.passwordreset', compact('actionText', 'actionUrl', 'level')));*/
            $to = $request->reset_email; //
            $emailData = [
                'email_subject' => 'Forespørsel om å tilbakestille passordet ditt',
                'email_message' => view('emails.passwordreset', compact('actionText', 'actionUrl', 'level'))->render(),
                'from_name' => '',
                'from_email' => 'postmail@forfatterskolen.no',
                'attach_file' => NULL
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            //Mail::to($request->reset_email)->send(new PasswordResetEmail($passwordReset));

            return redirect()->back()->with(['passwordreset_success' => 'Vi har sendt en passord tilbakestillingslink til din epost.']);
        else :
            return redirect()->route('auth.login.show', 't=passwordreset')->withErrors("We can't find the email in our records.");
        endif;
    }




    public function resetForm($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        return view('frontend.auth.passwordreset', compact('passwordReset'));
    }



    public function updatePassword($token, Request $request)
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        $validator = $this->update_validator($request->all());
        if($validator->fails()) :
            return redirect()->back()->withErrors($validator);
        endif;

        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->password;
        $user->save();

        $passwordReset = PasswordReset::where('email', $passwordReset->email)->delete();

        return redirect(route('frontend.login.store'));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::where('email',$request->email)->first();

        if (!\Hash::check($request->current_password, $user->password)) {

            return redirect()->route('auth.login.show', 't=password-change')
                ->withInput()
                ->withErrors("User credentials doesn't match");
        }

        $user->fill([
            'password' => \Hash::make($request->password)
        ])->save();

        return redirect()->route('auth.login.show', 't=password-change')
            ->with(['password_change_success' => 'Password changed successfully.']);
    }
}
