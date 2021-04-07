<?php
namespace App\Http\Controllers\Backend;

use App\AssignmentManuscript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Course;
use App\Assignment;
use App\AssignmentGroup;
use App\AssignmentFeedback;
use App\AssignmentGroupLearner;
use App\User;
use App\Http\AdminHelpers;
use Carbon\Carbon;
use App\DelayedEmail;
use App\Jobs\AddMailToQueueJob;

class AssignmentGroupController extends Controller
{

    public function show($course_id, $assignment_id, $id)
    {
    	$course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
    	$group = AssignmentGroup::findOrFail($id);
    	$section = 'assignments';
    	if( $assignment->course->id == $course->id ) :
    		return view('backend.assignment.group_show', compact('course', 'assignment', 'section', 'group', 'assignment_id'));
    	endif;
    	return abort('404');
    }

    public function store($course_id, $assignment_id, Request $request)
    {
        $course = Course::findOrFail($course_id);
    	$assignment = Assignment::findOrFail($assignment_id);
    	if( $request->title ) :
    		AssignmentGroup::create([
                'assignment_id' => $assignment->id,
    			'title' => $request->title,
                'submission_date' => $request->submission_date,
                'allow_feedback_download' => isset($request->allow_feedback_download) ? 1 : 0
    		]);
    	endif;
    	return redirect()->back();
    }



    public function update($course_id, $assignment_id, $id, Request $request)
    {
    	$course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
    	$group = AssignmentGroup::findOrFail($id);
    	
    	if( $assignment->course->id == $course->id && $request->title ) :
    		$group->title = $request->title;
            $group->submission_date = $request->submission_date;
            $group->allow_feedback_download = isset($request->allow_feedback_download) ? 1 : 0;
    		$group->save();
    	endif;
    	return redirect()->back();
    }



    public function destroy($course_id, $assignment_id, $id, Request $request)
    {
    	$course = Course::findOrFail($course_id);
    	$assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);
    	
    	if( $assignment->course->id == $course->id ) :
    		$group->forceDelete();
    	endif;
    	return redirect(route('admin.assignment.show', ['course_id' => $course->id, 'id' => $assignment->id]));
    }



    public function add_learner($course_id, $assignment_id, $id, Request $request)
    {
    	$course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
    	$group = AssignmentGroup::findOrFail($id);
    	$user = User::findOrFail($request->user_id);
    	$manuscriptUsers = $assignment->manuscripts->pluck('user_id')->toArray();
        $groupLearners = $group->learners->pluck('user_id')->toArray(); 

    	if( $assignment->course->id == $course->id && in_array($user->id, $manuscriptUsers) && !in_array($user->id, $groupLearners) ) :
    		AssignmentGroupLearner::create([
    			'assignment_group_id' => $group->id,
    			'user_id' => $user->id
    		]);
    	endif;
    	return redirect()->back();
    }




    public function remove_learner($course_id, $assignment_id, $group_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($group_id);
        $assignmentLearner = AssignmentGroupLearner::findOrFail($id);

    	$assignmentLearner->forceDelete();

    	return redirect()->back();
    }

    public function getFiles($request, $learner_id){
        $filesWithPath = '';
        if ( $request->hasFile('filename')) :
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];

            // loop through all the uploaded files
            foreach ($request->file('filename') as $k => $file) {
                $extension = pathinfo($_FILES['filename']['name'][$k],PATHINFO_EXTENSION);
                $actual_name = $learner_id;
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension);
                $filesWithPath .= "/".AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension).", ";

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;
                $file->move($destinationPath, $fileName);
            }
            return $filesWithPath = trim($filesWithPath,", ");
        endif;
    }

    public function submit_feedback($group_id, $id, Request $request)
    {
        $learner_id = AssignmentGroupLearner::find($id)->user_id;
        $filesWithPath = $this->getFiles($request, $learner_id);

        if($request->feedback_id){
            
            $assignmentManuscript = AssignmentManuscript::find($request->manuscript_id);
            if (is_numeric($request->grade)) {
                $assignmentManuscript->grade = $request->grade;
            }
            $assignmentManuscript->save();

            $assignmentFeedback = AssignmentFeedback::where('assignment_group_learner_id', $id)->first();
            if($filesWithPath){
                if($request->replaceFiles){
                    $assignmentFeedback->filename = $filesWithPath;
                }else{
                    $assignmentFeedback->filename = $assignmentFeedback->filename.', '.$filesWithPath;
                }
            }
            $assignmentFeedback->hours_worked = $request->hours;
            $assignmentFeedback->notes_to_head_editor = $request->notes_to_head_editor;
            $assignmentFeedback->save();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback updated successfully.'),
                    'alert_type' => 'success']);

        }else{
    
            if ( $request->hasFile('filename')) :

                $assignmentManuscript = AssignmentManuscript::find($request->manuscript_id);
                $assignmentManuscript->has_feedback = 1;
                $assignmentManuscript->status = 0;
                // set grade
                if (is_numeric($request->grade)) {
                    $assignmentManuscript->grade = $request->grade;
                }
                $assignmentManuscript->save();
    
                $assignmentFeedback = AssignmentFeedback::create([
                    'assignment_group_learner_id' => $id,
                    'user_id' => Auth::user()->id,
                    'filename' => $filesWithPath,
                    'is_admin' => true,
                    'is_active' => true,
                    'hours_worked' => $request->hours,
                    'notes_to_head_editor' => $request->notes_to_head_editor
                ]);

                // send email to head editor
                $emailTemplate = AdminHelpers::emailTemplate('New Pending Feedback');
                $to = User::where('role', 1)->where('head_editor', 1)->first();

                dispatch(new AddMailToQueueJob($to->email, $emailTemplate->subject, $emailTemplate->email_content, $emailTemplate->from_email,
                null, null, 'new-pending-assignment-feedback', $assignmentFeedback->id));

                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback saved successfully.'),
                        'alert_type' => 'success']);
            else:

                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Please provide a file.'),
                        'alert_type' => 'warning']);

            endif;

        }

    }

    public function approveFeedbackCourse($manuscript_id, $learner_id, $feedback_id, Request $request)
    {
        $filesWithPath = $this->getFiles($request, $learner_id);

        $assignmentManuscript = AssignmentManuscript::find($manuscript_id);
        $assignmentManuscript->has_feedback = 1;
        $assignmentManuscript->status = 1;
        $assignmentManuscript->grade = $request->grade;
        $assignmentManuscript->save();

        // group assignment - set availability date on feedback
        $assignmentFeedback = AssignmentFeedback::find($feedback_id);
        $assignmentFeedback->availability = $request->availability;
        if($fileWithPath){
            $assignmentFeedback->filename = $fileWithPath;
        }

        $assignmentFeedback->save();
                                   
        // send email - no sending email for group assignment
        // sending email duplicate from assignment no group 
        $email_content  = $request->message;
        $to             = $assignmentManuscript->user->email;
        $first_name     = $assignmentManuscript->user->first_name;

        if($request->availability && Carbon::parse($request->availability)->gt(Carbon::today())) {
            $redirect_link          = route('learner.assignment');
            $formattedMailContent   = AdminHelpers::formatEmailContent($email_content, $to, $first_name, $redirect_link);

            DelayedEmail::create([
                'subject'       => $request->subject,
                'message'       => $formattedMailContent,
                'from_email'    => $request->from_email,
                'recipient'     => $to,
                'send_date'     => $request->availability,
                'parent'        => 'assignment-manuscripts',
                'parent_id'     => $assignmentManuscript->id
            ]);
        } else {
            $this->sendAssignmentFeedbackMail($email_content, $to, $first_name, $request->subject,
                $request->from_email, $assignmentManuscript->id);
        }
        
        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors'    => AdminHelpers::createMessageBag('Successfully approved feedback.')
        ]);
    }

    public function sendAssignmentFeedbackMail($email_content, $to, $first_name, $subject, $from_email, $manuscript_id)
    {
        $redirect_link          = route('learner.assignment');
        $formattedMailContent   = AdminHelpers::formatEmailContent($email_content, $to, $first_name, $redirect_link);
        dispatch(new AddMailToQueueJob($to, $subject, $formattedMailContent, $from_email, null, null,
            'assignment-manuscripts', $manuscript_id));
    }

    public function submit_feedback_learner($group_id, $id, Request $request)
    {
        $group = AssignmentGroup::where('id', $group_id)->whereHas('learners', function($query) use ($id, $request){
            $query->where('id', $id);
        })->firstOrFail();
        if ( $request->hasFile('filename') && 
            $request->file('filename')->isValid() ) :
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];
            $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension

            $actual_name = AssignmentGroupLearner::find($id)->user_id;
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name."f", $extension);// rename document

            //$fileName = $time.'.'.$extension; // rename document
            $request->filename->move($destinationPath, $fileName);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;
            AssignmentFeedback::where('assignment_group_learner_id', $id)->where('user_id', $request->learner_id)->delete();

            AssignmentFeedback::create([
                'assignment_group_learner_id' => $id,
                'user_id' => $request->learner_id,
                'filename' => "/".$fileName,
                'is_active' => true,
                'availability' => $request->availability,
            ]);
            return redirect()->back();
        endif;
    }




    public function remove_feedback($id)
    {
        $feedback = AssignmentFeedback::findOrFail($id);

        $files = explode(',',$feedback->filename);

        foreach ($files as $file) {
            $filePath = str_replace('public ','public', public_path().$file);
            if (file_exists($filePath)) {
                unlink($filePath); // delete the physical file
            }
        }

        $feedback->forceDelete();
        return redirect()->back();
    }




    public function update_feedback($id, Request $request)
    {
        $feedback = AssignmentFeedback::findOrFail($id);
        if ( $request->hasFile('filename') && 
            $request->file('filename')->isValid() ) :
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks/'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];
            $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
            $fileName = $time.'.'.$extension; // rename document
            $request->filename->move($destinationPath, $fileName);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            $feedback->filename = '/'.$destinationPath.$fileName;
        endif;
        $feedback->availability = $request->availability;
        $feedback->save();
        return redirect()->back();
    }


    public function update_feedback_admin($id, Request $request)
    {
        $feedback = AssignmentFeedback::findOrFail($id);
        $feedback->availability = $request->availability;
        if ( $request->hasFile('filename') && 
            $request->file('filename')->isValid() ) :
            $time = time();
            $destinationPath = 'storage/assignment-feedbacks/'; // upload path
            $extensions = ['pdf', 'docx', 'odt'];
            $extension = pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION); // getting document extension
            $fileName = $time.'.'.$extension; // rename document
            $request->filename->move($destinationPath, $fileName);

            if( !in_array($extension, $extensions) ) :
                return redirect()->back();
            endif;

            $feedback->filename = '/'.$destinationPath.$fileName;
        endif;
        $feedback->save();
        return redirect()->back();
    }


    /**
     * Approve the feedback
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        $feedback = AssignmentFeedback::findOrFail($id);
        $feedback->is_active = true;
        $feedback->save();
        return redirect()->back();
    }

    /**
     * Update the feedback lock status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFeedbackLockStatus(Request $request)
    {
        $feedback = AssignmentFeedback::find($request->feedback_id);
        $success = false;

        if ($feedback) {
            $feedback->locked = $request->locked;
            $feedback->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    /**
     * Download the assignment feedback
     * @param $id int assignment id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFeedback($id)
    {
        $assignmentFeedback = AssignmentFeedback::find($id);

        if ($assignmentFeedback) {
            $files = explode(',', $assignmentFeedback->filename);

            // if more than one file put it to zip before downloading
            if (count($files) > 1) {
                $learnerId  = $assignmentFeedback->assignment_group_learner->user->id;
                $zipName    = $learnerId."f.zip";
                $zipPath    = public_path('storage/assignment-feedbacks/');

                $zip = new \ZipArchive();
                if ($zip->open($zipPath.$zipName, \ZipArchive::OVERWRITE|\ZipArchive::CREATE)) {
                    foreach ($files as $file) {

                        $expFile    = explode('/', $file);
                        $fileName   = end($expFile);
                        // remove space between public path and the file
                        $fileWithPath = str_replace("public\ ","public\\",public_path($file));

                        $zip->addFile($fileWithPath, $fileName);

                    }
                    $zip->close();
                }

                if (file_exists($zipPath.$zipName)) {
                    //delete the zip file created after it's downloaded
                    return response()->download($zipPath.$zipName)->deleteFileAfterSend(true);
                }

            } else {
                return response()->download(public_path($files[0]));
            }
        }
        return redirect()->back();
    }

    /**
     * Download all the manuscript in the group
     * @param $course_id
     * @param $assignment_id
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse;
     */
    public function downloadAll($course_id, $assignment_id, $id)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);

        if( $assignment->course->id == $course->id ) :
            $groupLearners  = $group->learners->pluck('id')->toArray();
            $feedbacks      = AssignmentFeedback::whereIn('assignment_group_learner_id', $groupLearners)->orderBy('created_at', 'desc')->get();

            $zipFileName    = $assignment->title.' '.$group->title.' Feedbacks.zip';
            $public_dir     = public_path('storage');

            $zip            = new \ZipArchive();
            if ($zip->open($public_dir . '/' . $zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== TRUE) {
                die ("An error occurred creating your ZIP file.");
            }

            foreach ($feedbacks as $feedback) {
                $files = explode(',', $feedback->filename);
                // for multiple files in a feedback
                if (count($files) > 1) {
                    foreach($files as $feedFile) {
                        if (file_exists(public_path().'/'.trim($feedFile))) {

                            //get the correct filename
                            $expFileName = explode('/', $feedFile);
                            $file = str_replace('\\', '/', public_path());

                            // physical file location and name of the file
                            $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                        }
                    }
                } else {
                    if (file_exists(public_path().'/'.$feedback->filename)) {
                        //get the correct filename
                        $expFileName = explode('/', $feedback->filename);
                        $file = str_replace('\\', '/', public_path());

                        // physical file location and name of the file
                        $zip->addFile($file.$feedback->filename, end($expFileName));
                    }
                }
            }

            $zip->close();

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            $fileToPath = $public_dir.'/'.$zipFileName;

            if(file_exists($fileToPath)){
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

        endif;

        return redirect()->back();
    }

    /**
     * Set the group feedback availability
     * @param $course_id
     * @param $assignment_id
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setGroupFeedbackAvailability($course_id, $assignment_id, $id, Request $request)
    {
        $course = Course::findOrFail($course_id);
        $assignment = Assignment::findOrFail($assignment_id);
        $group = AssignmentGroup::findOrFail($id);

        if( $assignment->course->id == $course->id ) :
            $groupLearners  = $group->learners->pluck('id')->toArray();
            $feedbacks      = AssignmentFeedback::whereIn('assignment_group_learner_id', $groupLearners)->orderBy('created_at', 'desc')->get();
            // update the availability date of all the feedback
            foreach ($feedbacks as $feedback) {
                $feedback->availability = $request->availability;
                $feedback->save();
            }

            $group->availability = $request->availability;
            $group->save();
        endif;

        return redirect()->back();
    }

}
