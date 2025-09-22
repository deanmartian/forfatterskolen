<?php

namespace App\Http\Controllers\backend;

use App\AssignmentManuscript;
use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\EmailTemplate;
use App\FreeManuscript;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\AddMailToQueueJob;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\ShopManuscriptsTaken;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HeadEditorController extends Controller
{
    public function index()
    {
        $assignedAssignmentManuscripts = AssignmentManuscript::where('status', 0) // pending
            ->where('has_feedback', 1)
            ->whereHas('assignment', function ($query) {
                $query->where('parent', 'users');
            })
            ->get();
        $assigned_shop_manuscripts = ShopManuscriptsTaken::get();
        $assigned_shop_manuscripts = $assigned_shop_manuscripts->filter(function ($model) {
            return $model->status == 'Pending';
        });
        $assignedAssignments = AssignmentManuscript::where('status', 0) // pending
            ->where('has_feedback', 1)
            ->whereHas('assignment', function ($query) {
                $query->whereNull('parent');
                $query->orWhere('parent', 'assignment');
            })
            ->get();
        
        $coachingTimes = CoachingTimerManuscript::where('status', 0)
            ->where(function($query) {
                $query->whereNotNull('replay_link');
                $query->orWhereNotNull('comment');
                $query->orWhereNotNull('document');
            })->get();
        $corrections = CorrectionManuscript::where('status', 3)->get();
        $copyEditings = CopyEditingManuscript::where('status', 3)->get();
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=', 0)
            ->whereNotNull('feedback_content')
            ->orderBy('created_at', 'desc')->get();

        $selfPublishingList = SelfPublishing::whereHas('feedback', function ($query) {
            $query->where('is_approved', 0);
        })->get();

        $assignmentFeedbackEmailTemplates = EmailTemplate::where('is_assignment_manu_feedback', 1)->get();

        return view('backend.head-editor.index', compact('assignedAssignmentManuscripts',
            'assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimes', 'corrections', 'copyEditings', 
            'freeManuscripts', 'selfPublishingList', 'assignmentFeedbackEmailTemplates'));
    }

    public function sendEmail($editor_id, $type, $title, $learner, Request $request): RedirectResponse
    {
        // send email
        $to = User::find($editor_id);
        $search_string = [
            ':type', ':title', ':learner',
        ];
        $replace_string = [
            $type, $title, $learner,
        ];
        $message = str_replace($search_string, $replace_string, $request->message);

        dispatch(new AddMailToQueueJob($to->email, $request->subject, $message, $request->from_email,
            null, null, 'head-editor-to-editor-email', $editor_id));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Successfully sent.'),
            'alert_type' => 'success']);

    }

    public function approveSelfPublishingFeedback($feedback_id, Request $request): RedirectResponse
    {
        $feedback = SelfPublishingFeedback::find($feedback_id);
        $feedback->is_approved = 1;

        $filesWithPath = '';
        $destinationPath = 'storage/self-publishing-feedback/'; // upload path

        if ($request->hasFile('manuscript')) {

            foreach ($request->file('manuscript') as $k => $file) {
                $extension = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_EXTENSION); // getting document extension
                $actual_name = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

                $expFileName = explode('/', $fileName);
                $filePath = '/'.$destinationPath.end($expFileName);
                $file->move($destinationPath, end($expFileName));

                $filesWithPath .= $filePath.', ';

            }

            $feedback->manuscript = trim($filesWithPath, ', ');
        }

        $feedback->save();

        if ($request->has('send_email')) {
            if ($project = $feedback->selfPublishing->project) {
                $to = $project->user;
                // $emailTemplate = AdminHelpers::emailTemplate('Self Publishing Feedback');
                $content = AdminHelpers::formatEmailContent($request->email_content, $to->email, $to->first_name, 
                route('learner.project.show', $project->id));
                
                $email = $to->email;
                dispatch(new AddMailToQueueJob($email, $request->subject, $content, $request->from_email,
                    null, null, 'learner', $to->id));
            }
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback approved successfully.'),
            'alert_type' => 'success']);
    }
}
