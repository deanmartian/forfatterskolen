<?php

namespace App\Http\Controllers\backend;

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
        })
        ->get();
        $corrections = CorrectionManuscript::where('status', 3)->get();
        $copyEditings = CopyEditingManuscript::where('status', 3)->get();
        return view('backend.head-editor.index', compact('assignedAssignmentManuscripts', 'assigned_shop_manuscripts', 'assignedAssignments','corrections', 'copyEditings'));
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
}
