<?php

namespace App\Http\Controllers\Editor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Webinars;
use Illuminate\Support\Facades\Auth;

class AssignedWebinarController extends Controller
{
    public function show(){
        $assignedWebinar = Auth::user()->assignedWebinars;
        return view('editor.assigned-webinars', compact('assignedWebinar'));
    }
}
