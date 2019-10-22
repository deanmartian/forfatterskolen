<?php
namespace App\Http\Controllers\Backend;

use App\EmailTemplate;
use App\FreeManuscriptFeedbackHistory;
use App\Http\AdminHelpers;
use App\Manuscript;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptTakenFeedback;
use App\FreeManuscript;
use Illuminate\Support\Facades\Input;
use Validator;
use Illuminate\Support\Str;
use Mail;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport;

class FreeManuscriptController extends Controller
{

    /**
     * FreeManuscriptController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:7');
    }

    public function index(Request $request)
    {
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=',0)->orderBy('created_at', 'desc')->get();
        $archiveManuscripts = FreeManuscript::with('latestFeedbackHistory')
            ->where('is_feedback_sent', '=',1)->orderBy('created_at', 'desc')->paginate(20);

        if( $request->search && !empty($request->search) ) :
            $archiveManuscripts = FreeManuscript::with('latestFeedbackHistory')->where('email', 'LIKE', '%' . $request->search  . '%')->where('is_feedback_sent', '=',1)->orderBy('created_at', 'desc')->paginate(20);
        endif;
        $emailTemplate = EmailTemplate::where('page_name', 'Free Manuscript')->first();
        $emailTemplateRoute = 'admin.manuscript.add_email_template';
        $isUpdate = 0;
        if ($emailTemplate->count()) {
            $emailTemplateRoute = 'admin.manuscript.edit_email_template';
            $isUpdate = 1;
        }

        /*appends is used to append the parameters and to not be ignored by pagination render link*/
        return view('backend.shop-manuscript.free-manuscripts',
            compact('freeManuscripts','emailTemplate', 'emailTemplateRoute', 'isUpdate'),
            ['archiveManuscripts' => $archiveManuscripts->appends(Input::except('page'))]
        );
    }

    /**
     * Delete Free Manuscript
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFreeManuscript($id)
    {
        $freeManuscripts = FreeManuscript::findOrFail($id);
        $freeManuscripts->forceDelete();
        return redirect()->back();
    }

    /**
     * Edit the content from New tab
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editContent($id, Request $request)
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
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignEditor($id, Request $request)
    {
        $freeManuscripts = FreeManuscript::findOrFail($id);
        $freeManuscripts->editor_id = $request->editor_id;
        $freeManuscripts->save();
        return redirect()->back();
    }

    /**
     * Display the feedback history
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function feedbackHistory($id)
    {
        $freeManuscriptFeedbackHistory = FreeManuscriptFeedbackHistory::where('free_manuscript_id',$id)->get();
        if (!$freeManuscriptFeedbackHistory->count()) {
            return response()->json(['data' => 'No feedback history found', 'success' => false]);
        }
        return response()->json(['data' => $freeManuscriptFeedbackHistory, 'success' => true]);
    }

    /**
     * Resend feedback
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendFeedback($id)
    {
        $freeManuscripts    = FreeManuscript::find($id);
        if ($freeManuscripts) {
            $editor             = User::find($freeManuscripts->editor);
            $to                 = $freeManuscripts->email;
            $email_content      = $freeManuscripts->feedback_content;

            ob_start();
            include base_path().'/resources/views/emails/free-manuscript-feedback.blade.php';
            $message = ob_get_clean();

            $subject = 'Tilbakemelding på din tekst';
            $from = "postmail@forfatterskolen.no";

            /*AdminHelpers::send_mail($to, $subject, $message, $from );*/
            AdminHelpers::send_email($subject,
                'postmail@forfatterskolen.no', $to, $message);

            $newFeedbackHistory = new FreeManuscriptFeedbackHistory();
            $newFeedbackHistory->free_manuscript_id = $id;
            $newFeedbackHistory->date_sent = Carbon::now();
            $newFeedbackHistory->save();
        }

        return redirect()->back();
    }

    /**
     * Send Feedback
     * @param $id
     * @param Request $requests
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendFeedback($id, Request $requests)
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $freeManuscripts    = FreeManuscript::findOrFail($id);

        $freeManuscripts->is_feedback_sent = 1;
        $freeManuscripts->feedback_content = $requests->email_content;
        $freeManuscripts->save();

        $editor             = User::find($freeManuscripts->editor);
        $to                 = $freeManuscripts->email;
        //$from               = $editor->email;

        $params = array(
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

            // this is the action that adds a contact
            'api_action'   => 'contact_add',

            // define the type of output you wish to get back
            // possible values:
            // - 'xml'  :      you have to write your own XML parser
            // - 'json' :      data is returned in JSON format and can be decoded with
            //                 json_decode() function (included in PHP since 5.2.0)
            // - 'serialize' : data is returned in a serialized format and can be decoded with
            //                 a native unserialize() function
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

        $result = unserialize($response);

        $email_content = $requests->email_content;

        ob_start();
        include base_path().'/resources/views/emails/free-manuscript-feedback.blade.php';
        $message = ob_get_clean();

        $headers = "From: Forfatterskolen<postmail@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        //$headers .= 'Reply-To: '. $from . "\r\n";

        $subject = 'Tilbakemelding på din tekst';
        $from = "postmail@forfatterskolen.no";

        //AdminHelpers::send_mail($to, $subject, $message, $from );
        AdminHelpers::send_email($subject,
            'post@forfatterskolen.no', $to, $message);
        //mail($to, 'Subject', $message, $headers);

        $newFeedbackHistory = new FreeManuscriptFeedbackHistory();
        $newFeedbackHistory->free_manuscript_id = $id;
        $newFeedbackHistory->date_sent = Carbon::now();
        $newFeedbackHistory->save();

        return redirect()->back();
    }
}