<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;

class CoachingTimeController extends Controller {

    public function index()
    {
        return view('editor.coaching-time.index');
    }

}