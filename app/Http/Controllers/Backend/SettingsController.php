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
    
}
