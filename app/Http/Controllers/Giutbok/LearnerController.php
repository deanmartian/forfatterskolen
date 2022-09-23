<?php

namespace App\Http\Controllers\Giutbok;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use App\User;
use Illuminate\Http\Request;

class LearnerController extends Controller
{

    public function index(Request $request, User $user)
    {
        $learners = $user->newQuery();
        if( $request->sid || $request->sfname || $request->slname || $request->semail) :
            if ($request->sid) {
                $learners->where('id', $request->sid);
            }

            if ($request->sfname) {
                $learners->where('first_name', 'LIKE', '%' . $request->sfname  . '%');
            }

            if ($request->slname) {
                $learners->where('last_name', 'LIKE', '%' . $request->slname  . '%');
            }

            if ($request->semail) {
                $learners->where('email', 'LIKE', '%' . $request->semail  . '%');
            }

            $learners->orderBy('first_name', 'asc')
                ->orderBy('email', 'asc');
        endif;

        if ($request->has('free-course')) {
            $learners->has('freeCourses');
        }

        if ($request->has('workshop')) {
            $learners->has('workshopsTaken');
        }

        if ($request->has('shop-manuscript')) {
            $learners->has('shopManuscriptsTaken');
        }

        if ($request->has('course')) {
            if ($request->has('free-course')) {
                $learners->has('coursesTaken');
            } else {
                $learners->has('coursesTakenNoFree');
            }
        }

        $learners->where('is_self_publishing_learner', 1);
        $learners->orderBy('created_at', 'desc');
        $learners = $learners->paginate(25);

        return view('giutbok.learner.index', compact('learners'));
    }

    public function registerLearner( Request $request )
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->default_password = $request->password;
        $user->need_pass_update = 1;
        $user->is_self_publishing_learner = 1;
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
            'attach_file' => NULL
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Learner created successfully.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }

}