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
use Illuminate\Support\Facades\Auth;
use App\Jobs\AddMailToQueueJob;
use App\user;

class OtherServiceController extends Controller
{

    /**
     * OtherServiceController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:13', ['except' => 'editorSetReplay']);
    }

    public function index()
    {
        $copyEditing = CopyEditingManuscript::paginate(10);
        $corrections = CorrectionManuscript::paginate(10);
        $coachingTimers = CoachingTimerManuscript::paginate(10);
        $coachingFeedbackTemplate = AdminHelpers::emailTemplate('Coaching Feedback');
        $correctionFeedbackTemplate = AdminHelpers::emailTemplate('Correction Feedback');
        $copyEditingFeedbackTemplate = AdminHelpers::emailTemplate('Copy Editing Feedback');
        return view('backend.other-service.index', compact('copyEditing', 'corrections',
            'coachingTimers', 'coachingFeedbackTemplate', 'correctionFeedbackTemplate', 'copyEditingFeedbackTemplate'));
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
     * @param $coaching_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setCoachingApproveDate( $coaching_id, Request $request )
    {
        if ($coachingTimer = CoachingTimerManuscript::find($coaching_id)) {
            $approvedDate = Carbon::parse($request->approved_date)->format('Y-m-d H:i:s');
            $coachingTimer->update([
                'approved_date' => $approvedDate
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

    public function editorSetReplay(CoachingTimerManuscript $id, Request $request)
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

        $data['status'] = 0;
        $id->update($data);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Replay saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true]);
    }

    public function deleteCoaching(CoachingTimerManuscript $id)
    {
        $id->delete();
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Coaching session deleted successfully.'),
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

            if (intval($service_type) === 1) {
                $copyEditing = CopyEditingManuscript::find($service_id);
                $copyEditing->expected_finish = $request->expected_finish;
                $copyEditing->save();
                $service = 'Språkvask';
            }

            if (intval($service_type) === 2){
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

    public function getFiles($request){
        if ($request->hasFile('manuscript')) :
            // new 
            $time = time();
            $destinationPath = 'storage/other-service-feedback'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];
            $filesWithPath = '';
            // loop through all the uploaded files
            foreach ($request->file('manuscript') as $k => $file) {
                $extension = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_EXTENSION);
                $original_filename = $file->getClientOriginalName();
                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $filename, $extension);
                $filesWithPath .= "/".AdminHelpers::checkFileName($destinationPath, $filename, $extension).", ";

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $file->move($destinationPath, $fileName);
            }

            return $filesWithPath = trim($filesWithPath,", ");
        endif;
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
        $filesWithPath = $this->getFiles($request);

        if($request->feedback_id){

            if ($service_type == 1 || $service_type == 2) {
                
                    $otherServiceFeedback = OtherServiceFeedback::find($request->feedback_id);
                    if($filesWithPath){
                        if($request->replaceFiles){
                            $otherServiceFeedback->manuscript = $filesWithPath;
                        }else{
                            $otherServiceFeedback->manuscript = $otherServiceFeedback->manuscript.', '.$filesWithPath;
                        }
                    }
                    $otherServiceFeedback->hours_worked = $request->hours_worked;
                    $otherServiceFeedback->notes_to_head_editor = $request->notes_to_head_editor;
                    $otherServiceFeedback->save();
    
                    return redirect()->back()->with([
                        'errors'                => AdminHelpers::createMessageBag('Feedback updated successfully.'),
                        'alert_type'            => 'success',
                        'not-former-courses'    => true
                    ]);
            }

        }else{
            
            if ($service_type == 1 || $service_type == 2) {
                if ($request->hasFile('manuscript')) :
                    
                    $data['manuscript'] = $filesWithPath;
                    $service = '';
                    $data['service_id'] = $service_id;
                    $data['service_type'] = $service_type;
                    $data['hours_worked'] = $request->hours_worked;
                    $data['notes_to_head_editor'] = $request->notes_to_head_editor;
                    $otherServiceFeedback = OtherServiceFeedback::create($data);
    
                    //update status
                    if ($service_type == 1) {
                        $copyEditing = CopyEditingManuscript::find($service_id);
                        $copyEditing->status = 3; // set status to pending
                        $copyEditing->save();
                        $service = 'Språkvask';
                    }
        
                    if ($service_type == 2){
                        $correction = CorrectionManuscript::find($service_id);
                        $correction->status = 3; // set status to pending
                        $correction->save();
                        $service = 'Korrektur';
                    }

                    // send email to head editor
                    $emailTemplate = AdminHelpers::emailTemplate('New Pending Feedback');
                    $to = User::where('role', 1)->where('head_editor', 1)->first();

                    dispatch(new AddMailToQueueJob($to->email, $emailTemplate->subject, $emailTemplate->email_content, $emailTemplate->from_email,
                    null, null, 'new-pending-'.$service.'-feedback', $otherServiceFeedback->id));
    
                    return redirect()->back()->with([
                        'errors'                => AdminHelpers::createMessageBag($service.' Feedback added successfully.'),
                        'alert_type'            => 'success',
                        'not-former-courses'    => true
                    ]);
                else: 
                    return redirect()->back()->with([
                        'errors'                => AdminHelpers::createMessageBag('Please provide a file.'),
                        'alert_type'            => 'warning',
                        'not-former-courses'    => true
                    ]);
                endif;
            }

        }
        
    }

    public function approveFeedback($service_id, $service_type, Request $request){
        
        // replace feedback file
        $filesWithPath = $this->getFiles($request);
        $otherServiceFeedback = OtherServiceFeedback::find($request->feedback_id);
        if ($filesWithPath && $otherServiceFeedback){
            $otherServiceFeedback->manuscript = $filesWithPath;
            $otherServiceFeedback->save();
        }

        // Update status
        $user_email = '';
        if($service_type==1){
            $copyEditingManuscript = CopyEditingManuscript::find($service_id);
            $copyEditingManuscript->status = 2;
            $copyEditingManuscript->save();
            $user_email = User::find($copyEditingManuscript->user_id)->email;
        }else{
            $correctionManuscript = CorrectionManuscript::find($service_id);
            $correctionManuscript->status = 2;
            $correctionManuscript->save();
            $user_email = User::find($correctionManuscript->user_id)->email;
        }

        // send email 
        $from = $request->from_email;
        $parent = null;
        $emailContent = $request->message;
        $emailSubject = $request->subject;

        if ($service_type == 1) {
            $parent = 'copy-editing-feedback';
        }else{
            $parent = 'correction-feedback';
        }

        $emailTemplate = AdminHelpers::emailTemplate('Other Services Feedback');

        $emailContent = AdminHelpers::formatEmailContent($emailContent, $from,
        Auth::user()->first_name, '');

        if ($request->has('send_email')) {
            dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
                $emailTemplate->from_email, null, null, $parent, $service_id));
        }

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Feedback approved successfully.'),
            'alert_type'            => 'success'
        ]);
    }
}