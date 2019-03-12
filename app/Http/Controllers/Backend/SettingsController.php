<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Settings;

class SettingsController extends Controller
{
   
    public function updateEmail( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'welcome_email'], ['setting_value' => $request->welcome_email]);
    	return redirect()->back();
    }

    public function updateTerms( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'terms'], ['setting_value' => $request->terms]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Terms updated successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * Update different terms
     * @param Request $request terms_type Terms for certain page
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOtherTerms( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => $request->terms_type.'-terms'], ['setting_value' => $request->terms]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Terms updated successfully.'),
            'alert_type' => 'success',
            'terms_tab' => $request->terms_type]);
    }

    public function updateOptInTerms( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'opt_in_terms'], ['setting_value' => $request->opt_in_terms]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Terms updated successfully.'),
            'alert_type' => 'success']);
    }

    public function updateOptInDescription( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'opt_in_description'], ['setting_value' => $request->opt_in_description]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Description updated successfully.'),
            'alert_type' => 'success']);
    }

    public function updateOptInRektorDescription( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'opt_in_rektor_description'], ['setting_value' => $request->opt_in_description]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Description for Rektor Tips updated successfully.'),
            'alert_type' => 'success']);
    }

    public function gtConfirmationEmail( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'gt_confirmation_email'], ['setting_value' => $request->gt_confirmation_email]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Confirmation email template updated successfully.'),
            'alert_type' => 'success']);
    }

    public function webinarEmailTemplate( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'webinar_email_template'], ['setting_value' => $request->webinar_email_template]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Webinar email template updated successfully.'),
            'alert_type' => 'success']);
    }

    public function gtReminderEmail( Request $request )
    {
        Settings::updateOrCreate(['setting_name' => 'gt_reminder_email_template'], ['setting_value' => $request->gt_reminder_email_template]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Reminder email template updated successfully.'),
            'alert_type' => 'success']);
    }
    
}
