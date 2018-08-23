<?php

namespace App\Http\Controllers\Backend;

use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $copyEditing = CopyEditingManuscript::paginate(10);
        $corrections = CorrectionManuscript::paginate(10);
        $coachingTimers = CoachingTimerManuscript::paginate(10);
        return view('backend.other-service.index', compact('copyEditing', 'corrections', 'coachingTimers'));
    }

    /**
     * Approve a coaching timer date
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveDate($id, Request $request)
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $data = $request->except('_token');
            $coachingTimer->update($data);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Date approved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Suggest new coaching timer session date
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suggestDate($id, Request $request)
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $data = $request->except('_token');
            $suggested_dates = $data['suggested_date'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            }

            $data['suggested_date'] = json_encode($suggested_dates);
            $data['is_suggested_by_admin'] = 1;

            $coachingTimer->update($data);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Suggested date saved successfully.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

}