<?php

namespace App\Http\Controllers\Auth;

use App\Http\AdminHelpers;
use App\Mail\SubjectBodyEmail;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Mail\RegistrationEmail;
use Mail;

class RegisterController extends Controller
{

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'register_first_name' => 'required|string|max:255',
            'register_last_name' => 'required|string|max:255',
            'register_email' => 'required|string|email|max:255|unique:users,email',
            'register_password' => 'required|string',
            'g-recaptcha-response' => 'required|captcha'
        ]);
    }

    
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->first_name = $request->register_first_name;
        $user->last_name = $request->register_last_name;
        $user->email = $request->register_email;
        $user->password = bcrypt($request->register_password);
        $user->save();

        // Send welcome email
        $actionText = 'Se dine kurs';
        $actionUrl = \URL::to('/account/course');
        $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        //mail($user->email, 'Velkommen til Forfatterskolen', view('emails.registration', compact('actionText', 'actionUrl', 'user')), $headers);
        /*AdminHelpers::send_email('Velkommen til Forfatterskolen',
            'post@forfatterskolen.no', $user->email, view('emails.registration', compact('actionText', 'actionUrl', 'user')));*/
        $to = $user->email; //
        $emailData = [
            'email_subject' => 'Velkommen til Forfatterskolen',
            'email_message' => view('emails.registration', compact('actionText', 'actionUrl', 'user'))->render(),
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => NULL
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

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

}
