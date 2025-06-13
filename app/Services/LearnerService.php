<?php

namespace App\Services;

use App\Mail\SubjectBodyEmail;
use App\User;
use Illuminate\Http\Request;

class LearnerService
{
    public function registerLearner(Request $request, $is_self_publishing = false): User
    {
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->default_password = $request->password;
        $user->need_pass_update = 1;

        if ($is_self_publishing) {
            $user->is_self_publishing_learner = 1;
        }

        $user->save();

        $encode_email = encrypt($user->email);

        // Send welcome email
        $actionText = 'Klikk her for å logge inn';
        $actionUrl = route('auth.login.email', $encode_email);

        $to = $user->email;
        $emailData = [
            'email_subject' => 'Velkommen til Forfatterskolen',
            'email_message' => view('emails.registration', compact('actionText', 'actionUrl', 'user'))->render(),
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        return $user;
    }
}
