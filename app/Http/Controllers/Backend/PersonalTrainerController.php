<?php

/**
 * Created by PhpStorm.
 * User: janiel
 * Date: 11/7/2019
 * Time: 10:12 AM
 */

namespace App\Http\Controllers\Backend;

use App\Address;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\PersonalTrainerApplicant;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonalTrainerController extends Controller
{
    public function index(): View
    {
        $applicants = PersonalTrainerApplicant::paginate(25);

        return view('backend.personal-trainer.index', compact('applicants'));
    }

    public function show($id): View
    {
        $applicant = PersonalTrainerApplicant::find($id);

        return view('backend.personal-trainer.show', compact('applicant'));
    }

    public function create(): View
    {
        return view('backend.personal-trainer.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'reason_for_applying.required' => 'Hva er årsaken til at du søker dette kurset (kort begrunnelse) field is required.',
            'need_in_course.required' => 'Hva skal til for at du fullfører dette kurset field is required.',
            'expectations.required' => 'Hvilke forventninger har du til deg selv – og oss field is required.',
        ];
        $request->validate([
            'email' => 'required',
            'first_name' => 'required|alpha_spaces',
            'last_name' => 'required|alpha_spaces',
            'phone' => 'required',
            'reason_for_applying' => 'required',
            'need_in_course' => 'required',
            'expectations' => 'required',
            'how_ready' => 'required',
        ], $messages);

        $user = new User;

        // check if there's a selected user
        if ($request->user_id) {
            $user = $user->find($request->user_id);
        } else {
            // if not find the email if it exists
            $searchUser = $user->where('email', $request->email)->first();

            // if the email don't exists yet then create a new user
            if (! $searchUser) {
                $user->email = $request->email;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->password = bcrypt($request->password);
                $user->save();
                $user = $user->find($user->id);
            } else {
                $user = $searchUser;
            }
        }

        $address = Address::firstOrNew(['user_id' => $user->id]);
        $address->phone = $request->phone;
        $address->save();

        $user->personalTrainerApplication()->create($request->all());

        return redirect()->route('admin.personal-trainer.index')->with([
            'errors' => AdminHelpers::createMessageBag('Record added successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function export()
    {
        $applicants = PersonalTrainerApplicant::all();
        $applicantList = [];
        $applicantList[] = ['ID', 'Learner ID', 'First Name', 'Last Name', 'Email', 'Date'];

        foreach ($applicants as $applicant) {
            $applicantList[] = [
                $applicant->id,
                $applicant->user_id,
                $applicant->user->first_name,
                $applicant->user->last_name,
                $applicant->user->email,
                $applicant->created_at,
            ];
        }

        $excel = \App::make('excel');
        $excel->create('Personal Trainer Applicants', function ($excel) use ($applicantList) {
            $excel->sheet('Sheetname', function ($sheet) use ($applicantList) {
                $sheet->fromArray($applicantList);
            });
        })->export('xls');
    }
}
