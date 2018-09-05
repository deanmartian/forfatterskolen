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
                'alert_type' => 'success',
                'not-former-courses' => true]);
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
            $suggested_dates = $data['suggested_date_admin'];
            // format the sent suggested dates
            foreach ($suggested_dates as $k => $suggested_date) {
                $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
            }

            $data['suggested_date_admin'] = json_encode($suggested_dates);

            $coachingTimer->update($data);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Suggested date saved successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
        }

        return redirect()->back();
    }

    /**
     * Set replay for coaching timer
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setReplay($id, Request $request)
    {
        if ($coachingTimer = CoachingTimerManuscript::find($id)) {
            $data = $request->except('_token');

            $coachingTimer->update($data);
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Replay saved successfully.'),
                'alert_type' => 'success',
                'not-former-courses' => true]);
        }

        return redirect()->back();
    }

    /**
     * Update the status of particular service
     * @param $service_id int Id of the service
     * @param $service_type int service type identifier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($service_id, $service_type)
    {
        if ($service_type == 1 || $service_type == 2 || $service_type == 3) {
            $service = '';
            if ($service_type == 1) {
                $copyEditing = CopyEditingManuscript::find($service_id);
                $copyEditing->status = $copyEditing->status+1;
                $copyEditing->save();
                $service = 'Språkvask';
            }

            if ($service_type == 2){
                $correction = CorrectionManuscript::find($service_id);
                $correction->status = $correction->status+1;
                $correction->save();
                $service = 'Korrektur';
            }

            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag($service.' status updated successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
        }

        return redirect()->back();
    }

    /**
     * Update the expected finish date
     * @param $service_id int Id of the service
     * @param $service_type int service type identifier
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateExpectedFinish($service_id, $service_type, Request $request)
    {
        if ($service_type == 1 || $service_type == 2 || $service_type == 3) {
            $service = '';
            if ($service_type == 1) {
                $copyEditing = CopyEditingManuscript::find($service_id);
                $copyEditing->expected_finish = $request->expected_finish;
                $copyEditing->save();
                $service = 'Språkvask';
            }

            if ($service_type == 2){
                $correction = CorrectionManuscript::find($service_id);
                $correction->expected_finish = $request->expected_finish;
                $correction->save();
                $service = 'Korrektur';
            }

            return redirect()->back()->with([
                'errors'                => AdminHelpers::createMessageBag($service.' expected finish date updated successfully.'),
                'alert_type'            => 'success',
                'not-former-courses'    => true
            ]);
        }

        return redirect()->back();
    }

    /**
     * Download file
     * @param $service_id
     * @param $service_type
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadOtherServiceDoc($service_id, $service_type)
    {
        if ($service_type == 1 || $service_type == 2) {
            $filename = '';
            if ($service_type == 1 && $copyEditing = CopyEditingManuscript::find($service_id)) {
                $filename = $copyEditing->file;
            }

            if ($service_type == 2 && $correction = CorrectionManuscript::find($service_id)){
                $filename = $correction->file;
            }

            return response()->download(public_path($filename));
        }

        return redirect()->route('admin.learner.index');
    }

}