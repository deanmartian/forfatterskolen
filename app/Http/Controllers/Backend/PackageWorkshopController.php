<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use App\Package;
use App\PackageWorkshop;
use App\User;
use App\Workshop;
use App\WorkshopsTaken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackageWorkshopController extends Controller
{
    public function store($package_id, Request $request): RedirectResponse
    {
        $package = Package::findOrFail($package_id);
        $workshop = Workshop::findOrFail($request->workshop_id);

        if (! in_array($workshop->id, $package->workshops()->pluck('workshop_id')->toArray())) {
            $packageWorkshop = new PackageWorkshop;
            $packageWorkshop->package_id = $package->id;
            $packageWorkshop->workshop_id = $workshop->id;
            $packageWorkshop->save();
        }

        return redirect()->back();
    }

    public function delete($workshop_id): RedirectResponse
    {
        $workshop = PackageWorkshop::findOrFail($workshop_id);
        $workshop->forceDelete();

        return redirect()->back();
    }

    public function approve($workshop_taken_id, Request $request): RedirectResponse
    {
        $workshopTaken = WorkshopsTaken::findOrFail($workshop_taken_id);
        $workshopTaken->is_active = true;

        $user = User::find($request->workshop_user_id);
        $workshop = Workshop::find($request->workshop_id);
        if ($user && $workshop && $workshop->email_title && $workshop->email_body) {
            $to = $user->email;
            $headers = "From: Forfatterskolen<postmail@forfatterskolen.no>\r\n";
            $emailData['email_subject'] = $workshop->email_title;
            $emailData['email_message'] = nl2br($workshop->email_body);
            $emailData['from_name'] = null;
            $emailData['from_email'] = 'elin@forfatterskolen.no';
            $emailData['attach_file'] = null;

            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            // AdminHelpers::send_email($workshop->email_title, 'elin@forfatterskolen.no', $to, nl2br($workshop->email_body));
        }

        $workshopTaken->save();

        return redirect()->back();
    }

    public function disapprove($workshop_id): RedirectResponse
    {
        $workshopTaken = WorkshopsTaken::findOrFail($workshop_id);
        $workshopTaken->forceDelete();

        return redirect()->back();
    }
}
