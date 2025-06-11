<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function updateEmail(Request $request)
    {
        Settings::updateOrCreate(['setting_name' => 'welcome_email'], ['setting_value' => $request->welcome_email]);

        return redirect()->back();
    }
}
