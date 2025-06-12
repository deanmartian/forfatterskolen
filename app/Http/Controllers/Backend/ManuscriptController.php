<?php

namespace App\Http\Controllers\Backend;

use App\CoursesTaken;
use App\EmailTemplate;
use App\Feedback;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackCreateRequest;
use App\Mail\SubjectBodyEmail;
use App\Manuscript;
use App\User;
use File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class ManuscriptController extends Controller
{
    public function index(): View
    {
        $manuscripts = Manuscript::orderBy('created_at', 'desc')->paginate(15);

        // check if editor then display only assigned manuscript
        // or manuscript that don't have an owner/assigned admin
        if (Auth::user()->role == 3) {
            $manuscripts = Manuscript::where('feedback_user_id', Auth::user()->id)
                ->orWhereNull('feedback_user_id')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        $emailTemplate = EmailTemplate::where('page_name', 'Manuscript')->first();
        $emailTemplateRoute = 'admin.manuscript.add_email_template';
        $isUpdate = 0;
        if (count($emailTemplate)) {
            $emailTemplateRoute = 'admin.manuscript.edit_email_template';
            $isUpdate = 1;
        }

        return view('backend.manuscript.index', compact('manuscripts', 'emailTemplate', 'emailTemplateRoute', 'isUpdate'));
    }

    public function show($id): View
    {
        $manuscript = Manuscript::findOrFail($id);
        $emailTemplate = EmailTemplate::where('page_name', '=', 'Manuscript')->first();

        return view('backend.manuscript.show', compact('manuscript', 'emailTemplate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $courseTaken = CoursesTaken::findOrFail($request->coursetaken_id);
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $time = time();
            $destinationPath = 'storage/manuscripts/'; // upload path
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); // getting document extension
            $fileName = $time.'.'.$extension; // rename document
            $request->file->move($destinationPath, $fileName);
            // count words
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.$fileName);
                $pdf_content = $pdf->Text;
                $word_count = AdminHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText = $docObj->convertToText();
                $word_count = AdminHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.$fileName);
                $word_count = AdminHelpers::get_num_of_words($doc);
            }

            Manuscript::create([
                'coursetaken_id' => $courseTaken->id,
                'filename' => '/'.$destinationPath.$fileName,
                'word_count' => $word_count,
            ]);
        }

        return redirect()->back();
    }

    public function update($id, Request $request): RedirectResponse
    {
        $manuscript = Manuscript::findOrFail($id);
        $manuscript->grade = $request->grade;
        $manuscript->feedback_user_id = $request->feedback_user_id;
        $manuscript->expected_finish = $request->expected_finish;
        $manuscript->save();

        return redirect()->back();
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $manuscript = Manuscript::findOrFail($id);
        $file = substr($manuscript->filename, 1);
        if (File::exists($file)) {
            File::delete($file);
        }
        $manuscript->forceDelete();

        return redirect('/shop-manuscript?tab=manuscripts');
    }

    public function addFeedback($manuscript_id, FeedbackCreateRequest $request): RedirectResponse
    {
        $manuscript = Manuscript::findOrFail($manuscript_id);
        if ($request->hasFile('files') && $manuscript->feedbacks->count() == 0) {
            $files = [];
            foreach ($request->file('files') as $file) {
                $time = Str::random(10).'-'.time();
                $destinationPath = 'storage/feedbacks/'; // upload path
                $extension = $file->getClientOriginalExtension(); // getting document extension
                $fileName = $time.'.'.$extension; // rename document
                $file->move($destinationPath, $fileName);
                $files[] = '/'.$destinationPath.$fileName;
            }

            Feedback::create([
                'manuscript_id' => $manuscript->id,
                'filename' => json_encode($files),
                'notes' => $request->notes,
            ]);
        }

        return redirect()->back();
    }

    public function destroyFeedback($id): RedirectResponse
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->forceDelete();

        return redirect()->back();
    }

    public function sendEmail($id, Request $request): RedirectResponse
    {
        $manuscript = Manuscript::findOrFail($id);

        $to = $manuscript->user->email;
        $from = $request->from_email;
        $message = nl2br($request->message);
        $subject = $request->subject;

        // AdminHelpers::send_mail( $to, $subject, $message, $from);
        // AdminHelpers::send_email($subject, $from, $to, $message);
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = $from;
        $emailData['attach_file'] = null;

        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        return redirect()->back();

        // Send welcome email
        /*$headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($manuscript->user->email, 'Welcome to Forfatterskolen', view('emails.registration', compact('actionText', 'actionUrl', 'user')), $headers);*/
    }
}
