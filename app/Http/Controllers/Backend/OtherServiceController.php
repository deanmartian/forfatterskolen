<?php

namespace App\Http\Controllers\Backend;

use App\CoachingTimerManuscript;
use App\CoachingTimerTaken;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\CoursesTaken;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\OtherServiceFeedback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use File;

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
        $coachingTimers = CoachingTimerManuscript::where('status',0)->paginate(10);
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

    public function setApprovedDate(Request $request)
    {
        $user_id = $request->user_id;
        $course_taken_id = $request->course_taken_id;
        if ($request->isMethod('post') && $courseTaken = CoursesTaken::find($course_taken_id)) {
            CoachingTimerManuscript::create([
                'user_id'           => $user_id,
                'approved_date'     => $request->approved_date
            ]);

            CoachingTimerTaken::create([
                'user_id'           => $user_id,
                'course_taken_id'   => $course_taken_id
            ]);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Approved date saved successfully.'),
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
    public function setReplay(CoachingTimerManuscript $id, Request $request)
    {
        $data = $request->except('_token');

        if (!$request->replay_link && !$request->document && ! $request->comment) {
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Please fill up at least one field.'),
                'not-former-courses' => true]);
        }

        if ($request->hasFile('document') && $request->file('document')->isValid()) {

            $destinationPath = 'storage/coaching-timer-manuscripts'; // upload path
            $extensions = ['doc', 'docx', 'pdf'];

            $extension = pathinfo($_FILES['document']['name'],PATHINFO_EXTENSION); // getting document extension

            if (!in_array($extension, $extensions)) {
                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Invalid file type.'),
                    'not-former-courses' => true]);
            }

            $actual_name = pathinfo($request->document->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);

            $request->document->move($destinationPath, end($expFileName));
            $data['document'] = $fileName;
        }

        $data['status'] = 1;
        $id->update($data);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Replay saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true]);
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

    /**
     * Add feedback for other services
     * @param $service_id int ID of the service
     * @param $service_type int Which service it belongs
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFeedback($service_id, $service_type, Request $request)
    {
        $data = $request->except('_token');
        $extensions = ['pdf', 'docx'];

        if ($service_type == 1 || $service_type == 2) {
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (!in_array($extension, $extensions)) :
                    return redirect()->back()->with([
                        'alert_type'            => 'danger',
                        'errors'                => AdminHelpers::createMessageBag('File type not allowed.'),
                        'not-former-courses'    => true
                    ]);
                endif;

                $destinationPath = 'storage/other-service-feedback'; // upload path

                // check if path not exists then create it
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                }

                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                // check the file name and add/increment number if the filename already exists
                $file = AdminHelpers::checkFileName($destinationPath, $filename, $extension);

                $request->manuscript->move($destinationPath, $file);

                $data['manuscript'] = $file;

                $service = 'Språkvask';

                if ($service_type == 2) {
                    $service = 'Korrektur';
                }

                $data['service_id'] = $service_id;
                $data['service_type'] = $service_type;

                OtherServiceFeedback::create($data);

                return redirect()->back()->with([
                    'errors'                => AdminHelpers::createMessageBag($service.' Feedback added successfully.'),
                    'alert_type'            => 'success',
                    'not-former-courses'    => true
                ]);
            endif;
        }

        return redirect()->back();
    }

}