<?php

namespace App\Http\Controllers\backend;

use App\FreeManuscript;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AssignmentManuscript;
use App\ShopManuscriptsTaken;
use Illuminate\Support\Facades\Auth;
use App\CorrectionManuscript;
use App\CopyEditingManuscript;
use App\User;
use App\Jobs\AddMailToQueueJob;
use App\Http\AdminHelpers;

class HeadEditorController extends Controller
{
    public function index(){
        $assignedAssignmentManuscripts = AssignmentManuscript::where('status', 0) //pending
        ->where('has_feedback', 1)
        ->whereHas('assignment', function($query) {
            $query->where('parent', 'users');
        })
        ->get();
        $assigned_shop_manuscripts = ShopManuscriptsTaken::get();
        $assigned_shop_manuscripts = $assigned_shop_manuscripts->filter(function($model){
            return $model->status == 'Pending';
        });
        $assignedAssignments = AssignmentManuscript::where('status', 0) //pending
        ->where('has_feedback', 1)
        ->whereHas('assignment', function($query){
            $query->whereNull('parent');
            $query->orWhere('parent', 'assignment');
        })
        ->get();
        $corrections = CorrectionManuscript::where('status', 3)->get();
        $copyEditings = CopyEditingManuscript::where('status', 3)->get();
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=',0)
            ->whereNotNull('feedback_content')
            ->orderBy('created_at', 'desc')->get();

        $selfPublishingList = SelfPublishing::whereHas('feedback', function ($query) {
            $query->where('is_approved', 0);
        })->get();

        return view('backend.head-editor.index', compact('assignedAssignmentManuscripts',
            'assigned_shop_manuscripts', 'assignedAssignments','corrections', 'copyEditings', 'freeManuscripts', 'selfPublishingList'));
    }

    public function sendEmail($editor_id, $type, $title, $learner, Request $request)
    {
        // send email
        $to = User::find($editor_id);
        $search_string = [
            ':type', ':title', ':learner'
        ];
        $replace_string = [
            $type, $title, $learner
        ];
        $message = str_replace($search_string, $replace_string, $request->message);

        dispatch(new AddMailToQueueJob($to->email, $request->subject, $message, $request->from_email,
        null, null, 'head-editor-to-editor-email', $editor_id));

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Successfully sent.'),
            'alert_type' => 'success']);

    }

    public function approveSelfPublishingFeedback( $feedback_id, Request $request )
    {
        $feedback = SelfPublishingFeedback::find($feedback_id);
        $feedback->is_approved = 1;

        $filesWithPath = '';
        $destinationPath = 'storage/self-publishing-feedback/'; // upload path

        if ($request->hasFile('manuscript')) {

            foreach ($request->file('manuscript') as $k => $file) {
                $extension = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_EXTENSION); // getting document extension
                $actual_name = pathinfo($_FILES['manuscript']['name'][$k],PATHINFO_FILENAME);
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

                $expFileName = explode('/', $fileName);
                $filePath = "/".$destinationPath.end($expFileName);
                $file->move($destinationPath, end($expFileName));

                $filesWithPath .= $filePath.", ";

            }

            $feedback->manuscript =  trim($filesWithPath,", ");
        }

        $feedback->save();
        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback approved successfully.'),
            'alert_type' => 'success']);
    }
}
