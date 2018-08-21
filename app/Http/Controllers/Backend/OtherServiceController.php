<?php

namespace App\Http\Controllers\Backend;

use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\Controllers\Controller;

class OtherServiceController extends Controller
{

    /**
     * OtherServiceController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:13');
    }

    public function index()
    {
        $copyEditing = CopyEditingManuscript::all();
        $corrections = CorrectionManuscript::all();
        $coachingTimers = CoachingTimerManuscript::whereNotNull('file')->get();
        return view('backend.other-service.index', compact('copyEditing', 'corrections', 'coachingTimers'));
    }

}