<?php

namespace App\Http\Controllers\Backend;

use App\DelayedEmail;
use App\EmailTemplate;
use App\FreeManuscript;
use App\FreeManuscriptFeedbackHistory;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\Manuscript;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;
use Mail;

class FreeManuscriptController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkPageAccess:7');
    }

    public function index(Request $request): View
    {
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=', 0)->orderBy('created_at', 'desc')->get();
        $archiveManuscripts = FreeManuscript::with('latestFeedbackHistory')
            ->where('is_feedback_sent', '=', 1)->orderBy('created_at', 'desc')->paginate(20);

        if ($request->search && ! empty($request->search)) {
            $archiveManuscripts = FreeManuscript::with('latestFeedbackHistory')->where('email', 'LIKE', '%'.$request->search.'%')->where('is_feedback_sent', '=', 1)->orderBy('created_at', 'desc')->paginate(20);
        }
        $emailTemplate = EmailTemplate::where('page_name', 'Free Manuscript')->first();
        $emailTemplate2 = EmailTemplate::where('page_name', 'Free Manuscript 2')->first();
        $emailTemplateRoute = 'admin.manuscript.add_email_template';
        $isUpdate = 0;
        if ($emailTemplate->count()) {
            $emailTemplateRoute = 'admin.manuscript.edit_email_template';
            $isUpdate = 1;
        }

        /* appends is used to append the parameters and to not be ignored by pagination render link */
        return view('backend.shop-manuscript.free-manuscripts',
            compact('freeManuscripts', 'emailTemplate', 'emailTemplate2', 'emailTemplateRoute', 'isUpdate'),
            ['archiveManuscripts' => $archiveManuscripts->appends($request->except('page'))]
        );
    }

    /**
     * Delete Free Manuscript
     */
    public function deleteFreeManuscript($id): RedirectResponse
    {
        $freeManuscripts = FreeManuscript::findOrFail($id);
        $freeManuscripts->forceDelete();

        return redirect()->back();
    }

    /**
     * Edit the content from New tab
     */
    public function editContent($id, Request $request): RedirectResponse
    {
        $freeManuscript = FreeManuscript::find($id);
        if ($freeManuscript) {
            $freeManuscript->content = $request->manu_content;
            $freeManuscript->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Free manuscript content updated.'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Assign Editor
     */
    public function assignEditor($id, Request $request): RedirectResponse
    {
        $freeManuscripts = FreeManuscript::findOrFail($id);
        $freeManuscripts->editor_id = $request->editor_id;
        $freeManuscripts->save();

        $emailTemplate = EmailTemplate::where('page_name', 'Free Manuscript to Editor')->first();
        $to = $freeManuscripts->editor->email;
        $emailData = [
            'email_subject' => $emailTemplate->subject,
            'email_message' => $emailTemplate->email_content,
            'from_name' => '',
            'from_email' => $emailTemplate->from_email,
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Editor assigned successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * Display the feedback history
     */
    public function feedbackHistory($id): JsonResponse
    {
        $freeManuscriptFeedbackHistory = FreeManuscriptFeedbackHistory::where('free_manuscript_id', $id)->get();
        if (! $freeManuscriptFeedbackHistory->count()) {
            return response()->json(['data' => 'No feedback history found', 'success' => false]);
        }

        return response()->json(['data' => $freeManuscriptFeedbackHistory, 'success' => true]);
    }

    public function downloadContent($id)
    {
        $freeManuscript = FreeManuscript::find($id);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($freeManuscript->content);

        return $pdf->download(time().'.pdf');
    }

    /**
     * Resend feedback
     */
    public function resendFeedback($id): RedirectResponse
    {
        $freeManuscripts = FreeManuscript::find($id);
        if ($freeManuscripts) {
            $editor = User::find($freeManuscripts->editor_id);
            $to = $freeManuscripts->email;
            $email_content = $freeManuscripts->feedback_content;

            ob_start();
            include base_path().'/resources/views/emails/free-manuscript-feedback.blade.php';
            $message = ob_get_clean();

            $emailTemplate = $this->emailTemplate('Free Manuscript');
            $search_string = [
                ':firstname',
            ];
            $replace_string = [
                $freeManuscripts->name,
            ];

            $message = str_replace($search_string, $replace_string, $message);

            $subject = $emailTemplate->subject; // 'Tilbakemelding på din tekst';
            $from = 'postmail@forfatterskolen.no';

            /* AdminHelpers::send_mail($to, $subject, $message, $from ); */
            /*AdminHelpers::send_email($subject,
                'postmail@forfatterskolen.no', $to, $message);*/
            $emailData['email_subject'] = $subject;
            $emailData['email_message'] = $message;
            $emailData['from_name'] = null;
            $emailData['from_email'] = $from;
            $emailData['attach_file'] = null;

            // \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            dispatch(new AddMailToQueueJob($to, $subject, $message, $from, null, null,
                'free-manuscripts', $id));

            $newFeedbackHistory = new FreeManuscriptFeedbackHistory;
            $newFeedbackHistory->free_manuscript_id = $id;
            $newFeedbackHistory->date_sent = Carbon::now();
            $newFeedbackHistory->save();
        }

        return redirect()->back();
    }

    /**
     * This would move the feedback to be approved by head editor
     */
    public function sendFeedback($id, Request $requests): RedirectResponse
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $freeManuscripts = FreeManuscript::findOrFail($id);

        // $freeManuscripts->is_feedback_sent = 1;
        $freeManuscripts->feedback_content = $requests->email_content;
        $freeManuscripts->save();

        return redirect()->back();
    }

    public function approveFeedback($id, Request $requests): RedirectResponse
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $freeManuscripts = FreeManuscript::findOrFail($id);

        $freeManuscripts->is_feedback_sent = 1;
        $freeManuscripts->feedback_content = $requests->email_content;
        $freeManuscripts->save();

        $to = $freeManuscripts->email;

        /*$params = array(
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

            // this is the action that adds a contact
            'api_action'   => 'contact_add',
            'api_output'   => 'serialize',
        );

        // here we define the data we are posting in order to perform an update
        $post = array(
            'email'                    => $freeManuscripts->email,
            'first_name'               => $freeManuscripts->name,
            'tags'                     => 'Tekstvurdering',
            // assign to lists:
            'p[123]'                   => 51, // example list ID (REPLACE '123' WITH ACTUAL LIST ID, IE: p[5] = 5)
            'status[123]'              => 1, // 1: active, 2: unsubscribed (REPLACE '123' WITH ACTUAL LIST ID, IE: status[5] = 1)
            'instantresponders[123]' => 0, // set to 0 to if you don't want to sent instant autoresponders
        );

        // This section takes the input fields and converts them to the proper format
        $query = "";
        foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
        $query = rtrim($query, '& ');

        // This section takes the input data and converts it to the proper format
        $data = "";
        foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
        $data = rtrim($data, '& ');

        // clean up the url
        $url = rtrim($url, '/ ');

        // This sample code uses the CURL library for php to establish a connection,
        // submit your request, and show (print out) the response.
        if ( !function_exists('curl_init') ) die('CURL not supported. (introduced in PHP 4.0.2)');

        // If JSON is used, check if json_decode is present (PHP 5.2.0+)
        if ( $params['api_output'] == 'json' && !function_exists('json_decode') ) {
            die('JSON not supported. (introduced in PHP 5.2.0)');
        }

        // define a final API request - GET
        $api = $url . '/admin/api.php?' . $query;

        $request = curl_init($api); // initiate curl object
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
        //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

        $response = (string)curl_exec($request); // execute curl post and store results in $response

        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object

        if ( !$response ) {
            die('Nothing was returned. Do you have a connection to Email Marketing server?');
        }

        $result = unserialize($response);*/

        $email_content = $requests->email_content;

        ob_start();
        include base_path().'/resources/views/emails/free-manuscript-feedback.blade.php';
        $message = ob_get_clean();

        $headers = "From: Forfatterskolen<postmail@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        // $headers .= 'Reply-To: '. $from . "\r\n";
        $emailTemplate = $freeManuscripts->from === 'Giutbok' ? $this->emailTemplate('Free Manuscript 2')
            : $this->emailTemplate('Free Manuscript');

        $search_string = [
            ':firstname',
        ];
        $replace_string = [
            $freeManuscripts->name,
        ];

        $message = str_replace($search_string, $replace_string, $message);

        $subject = $emailTemplate->subject;
        $from = $emailTemplate->from; // "postmail@forfatterskolen.no";

        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = $from;
        $emailData['attach_file'] = null;

        dispatch(new AddMailToQueueJob($to, $subject, $message, $from, null, null,
            'free-manuscripts', $id));

        $newFeedbackHistory = new FreeManuscriptFeedbackHistory;
        $newFeedbackHistory->free_manuscript_id = $id;
        $newFeedbackHistory->date_sent = Carbon::now();
        $newFeedbackHistory->save();

        if ($requests->has('follow_up_email')) {
            $first_name = $freeManuscripts->name;
            $formattedMailContent = AdminHelpers::formatEmailContent($requests->follow_up_message, $to, $first_name,
                '');
            DelayedEmail::create([
                'subject' => $requests->follow_up_subject,
                'message' => $formattedMailContent,
                'from_email' => $requests->follow_up_from_email,
                'recipient' => $to,
                'send_date' => $requests->send_date,
                'parent' => 'free-manuscript-follow-up',
                'parent_id' => $freeManuscripts->id,
            ]);
        }

        return redirect()->back();
    }
}
