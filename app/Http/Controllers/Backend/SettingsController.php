<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Settings;
use App\User;

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

    public function courseNotStartedReminder(Request $request)
    {
        Settings::updateOrCreate(['setting_name' => 'course_not_started_reminder_subject'], ['setting_value' => $request->subject]);
        Settings::updateOrCreate(['setting_name' => 'course_not_started_reminder'], ['setting_value' => $request->email_content]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Reminder email template updated successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function headEditor( Request $request )
    {
        // Settings::updateOrCreate(['setting_name' => 'head-editor'], ['setting_value' => $request->editor_id]);

        User::where('head_editor', 1)->update(array('head_editor' => '0'));
        User::where('id', $request->editor_id)->update(array('head_editor' => '1')); 

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Head Editor updated successfully.'),
            'alert_type' => 'success']);
    }

    public function create( $name, Request $request )
    {
        Settings::updateOrCreate(['setting_name' => $name], ['setting_value' => $request->setting_value]);
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record updated successfully.'),
            'alert_type' => 'success']);
    }
    
}
