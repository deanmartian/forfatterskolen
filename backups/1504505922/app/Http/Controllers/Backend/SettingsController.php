<?php
namespace App\Http\Controllers\Backend;

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
    
}
