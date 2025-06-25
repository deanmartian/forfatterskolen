<?php

namespace App\Http\Controllers\Backend;

use App\DelayedEmail;
use App\EmailTemplate;
use App\FreeManuscript;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\Manuscript;
use App\Repositories\Services\SaleService;
use App\RequestToEditor;
use App\Settings;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptTakenFeedback;
use App\ShopManuscriptUpgrade;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Mail;
use Validator;

class ShopManuscriptController extends Controller
{
    protected $saleService;

    /**
     * ShopManuscriptController constructor.
     */
    public function __construct(SaleService $saleService)
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:9')->except('addFeedback');
        $this->saleService = $saleService;
    }

    /* public static function middleware(): array
    {
        return [
            ['middleware' => 'checkPageAccess:9', 'except' => ['addFeedback']],
        ];
    } */

    public function index(Request $request): View
    {
        if ($request->tab == 'sold') {
            $shopManuscripts = ShopManuscriptsTaken::orderBy('created_at', 'desc')->paginate(15);
            if ($request->exists('search')) {
                $users = User::where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')->pluck('id');

                $shopManuscripts = ShopManuscriptsTaken::whereIn('user_id', $users)
                    ->orWhere(function ($query) use ($users) {
                        $query->whereIn('feedback_user_id', $users);
                    })
                    ->paginate(15);
            }
        } elseif ($request->tab == 'manuscripts') {
            $shopManuscripts = Manuscript::orderBy('created_at', 'desc')->paginate(15);

            // check if editor then display only assigned manuscript
            // or manuscript that don't have an owner/assigned admin
            if (Auth::user()->role == 3) {
                $shopManuscripts = Manuscript::where('feedback_user_id', Auth::user()->id)
                    ->orWhereNull('feedback_user_id')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
            }

        } else {
            $shopManuscripts = ShopManuscript::orderBy('created_at', 'desc')->paginate(15);
        }
        $emailTemplate = EmailTemplate::where('page_name', 'Manuscript')->first();
        $emailTemplateRoute = 'admin.manuscript.add_email_template';
        $isUpdate = 0;
        $excessPerWordAmount = Settings::getDetailsByName('manuscript-excess-per-word-amount');

        if ($emailTemplate->count()) {
            $emailTemplateRoute = 'admin.manuscript.edit_email_template';
            $isUpdate = 1;
        }

        return view('backend.shop-manuscript.index', compact('shopManuscripts', 'emailTemplate', 'emailTemplateRoute', 'isUpdate',
            'excessPerWordAmount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $shopManuscript = new ShopManuscript;
        $shopManuscript->title = $request->title;
        $shopManuscript->description = $request->description;
        $shopManuscript->max_words = $request->max_words;
        $shopManuscript->full_payment_price = $request->full_payment_price;
        $shopManuscript->months_3_price = $request->months_3_price;
        $shopManuscript->months_6_price = $request->months_6_price ? $request->months_6_price : 0;
        $shopManuscript->full_price_product = $request->full_price_product;
        $shopManuscript->months_3_product = $request->months_3_product;
        $shopManuscript->months_6_due_date = $request->months_6_due_date ? $request->months_6_due_date : 0;
        $shopManuscript->full_price_due_date = $request->full_price_due_date;
        $shopManuscript->months_3_due_date = $request->months_3_due_date;
        $shopManuscript->upgrade_price = $request->upgrade_price;
        $shopManuscript->fiken_product = $request->full_price_product;
        /*$shopManuscript->title = $request->title;
        $shopManuscript->description = $request->description;
        $shopManuscript->max_words = $request->max_words;
        $shopManuscript->price = $request->price;
        $shopManuscript->split_payment_price = $request->split_payment_price;
        $shopManuscript->fiken_product = $request->fiken_product;*/
        $shopManuscript->save();

        return redirect()->back();
    }

    public function update($id, Request $request): RedirectResponse
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $shopManuscript = ShopManuscript::findOrFail($id);
        $shopManuscript->title = $request->title;
        $shopManuscript->description = $request->description;
        $shopManuscript->max_words = $request->max_words;
        $shopManuscript->full_payment_price = $request->full_payment_price;
        $shopManuscript->months_3_price = $request->months_3_price;
        $shopManuscript->full_price_product = $request->full_price_product;
        $shopManuscript->months_3_product = $request->months_3_product;
        $shopManuscript->full_price_due_date = $request->full_price_due_date;
        $shopManuscript->months_3_due_date = $request->months_3_due_date;
        $shopManuscript->upgrade_price = $request->upgrade_price;

        foreach ($request->except('_token') as $key => $value) {
            // check for the upgrade price set
            if (substr($key, 0, 14) == 'upgrade_price_') {
                // get the number only
                $upgrade_shop_manuscript_id = substr(substr($key, strrpos($key, '_')), 1, 2);
                $price = $request->$key;
                $shopManuscriptUpgrade = ShopManuscriptUpgrade::firstOrNew(['upgrade_shop_manuscript_id' => $upgrade_shop_manuscript_id,
                    'shop_manuscript_id' => $id]);
                $shopManuscriptUpgrade->price = $price;
                $shopManuscriptUpgrade->save();
            }
        }

        $shopManuscript->save();

        return redirect()->back();
    }

    public function destroy($id): RedirectResponse
    {
        $shopManuscript = ShopManuscript::findOrFail($id);
        $shopManuscript->forceDelete();

        return redirect()->back();
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required|string',
            'max_words' => 'required|integer',
            'months_3_price' => 'required|numeric',
            'full_price_product' => 'required|string',
            'months_3_product' => 'required|string',
            'full_price_due_date' => 'required|string',
            'months_3_due_date' => 'required|string',
        ]);
    }

    public function getFiles($request)
    {
        $files = [];

        if ($request->hasFile('files')) {

            foreach ($request->file('files') as $file) {
                $time = Str::random(10).'-'.time();
                $destinationPath = 'storage/shop-manuscript-taken-feedbacks/'; // upload path
                $extension = $file->getClientOriginalExtension(); // getting document extension
                $fileName = $time.'.'.$extension; // rename document
                $file->move($destinationPath, $fileName);
                $files[] = '/'.$destinationPath.$fileName;
            }

        }

        return $files;
    }

    public function addFeedback($shopManuscriptTakenID, Request $request): RedirectResponse
    {
        $files = $this->getFiles($request);

        if ($request->feedback_id) {

            $shopManuscriptTakenFeedback = ShopManuscriptTakenFeedback::find($request->feedback_id);
            if ($files) {
                if ($request->replaceFiles) {
                    $shopManuscriptTakenFeedback->filename = json_encode($files);
                } else {
                    $oldfiles = $shopManuscriptTakenFeedback->filename;
                    $shopManuscriptTakenFeedback->filename = json_encode(array_merge($oldfiles, $files));
                }
            }
            $shopManuscriptTakenFeedback->notes = $request->notes;
            $shopManuscriptTakenFeedback->hours_worked = $request->hours;
            $shopManuscriptTakenFeedback->notes_to_head_editor = $request->notes_to_head_editor;
            $shopManuscriptTakenFeedback->save();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback updated successfully.'),
                'alert_type' => 'success']);

        } else {

            $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);

            if ($request->hasFile('files') && $shopManuscriptTaken->feedbacks->count() == 0) {

                $shopManuscriptTakenFeedback = ShopManuscriptTakenFeedback::create([
                    'shop_manuscript_taken_id' => $shopManuscriptTaken->id,
                    'filename' => json_encode($files),
                    'notes' => $request->notes,
                    'hours_worked' => $request->hours,
                    'notes_to_head_editor' => $request->notes_to_head_editor,
                ]);

                // send email to head editor
                $emailTemplate = AdminHelpers::emailTemplate('New Pending Feedback');
                $to = User::where('role', 1)->where('head_editor', 1)->first();

                dispatch(new AddMailToQueueJob($to->email, $emailTemplate->subject, $emailTemplate->email_content, $emailTemplate->from_email,
                    null, null, 'new-pending-shop-manuscript-taken-feedback', $shopManuscriptTakenFeedback->id));

                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Feedback saved successfully.'),
                    'alert_type' => 'success']);

            } else {

                return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Please provide a file.'),
                    'alert_type' => 'warning']);

            }

        }

    }

    public function approveFeedback($id, $learner_id, $feedback_id, Request $request): RedirectResponse
    {
        $files = $this->getFiles($request);
        // update feedback
        $shopManuscriptTakenFeedback = ShopManuscriptTakenFeedback::find($feedback_id);
        $shopManuscriptTakenFeedback->approved = 1;
        $shopManuscriptTakenFeedback->notes = $request->notes;
        if ($files) {
            $shopManuscriptTakenFeedback->filename = json_encode($files);
        }
        $shopManuscriptTakenFeedback->save();

        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($id);

        // send email
        $to = $shopManuscriptTakenFeedback->shop_manuscript_taken->user->email;
        $email_content = $request->message;
        $encode_email = encrypt($to);
        $redirectLink = encrypt(route('learner.shop-manuscript.show', $id));
        $search_string = [
            ':firstname',
            ':redirect_link',
            ':end_redirect_link',
        ];
        $replace_string = [
            $shopManuscriptTaken->user->first_name,
            "<a href='".route('auth.login.emailRedirect', [$encode_email, $redirectLink])."'>",
            '</a>',
        ];

        $format_content = str_replace($search_string, $replace_string, $email_content);

        /*\Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        $this->saleService->createEmailHistory($request->subject, $request->from_email, $format_content,
            'shop-manuscripts-taken-admin-feedback', $shopManuscriptTakenID);*/
        if ($request->has('send_email')) {
            dispatch(new AddMailToQueueJob($to, $request->subject, $format_content, $request->from_email,
                null, null,
                'shop-manuscripts-taken-admin-feedback', $id));
        }

        if ($request->has('follow_up_email')) {
            $first_name = $shopManuscriptTakenFeedback->shop_manuscript_taken->user->first_name;
            $formattedMailContent = AdminHelpers::formatEmailContent($request->follow_up_message, $to, $first_name,
                '');
            DelayedEmail::create([
                'subject' => $request->follow_up_subject,
                'message' => $formattedMailContent,
                'from_email' => $request->follow_up_from_email,
                'recipient' => $to,
                'send_date' => $request->send_date,
                'parent' => 'shop-manuscripts-taken-feedback',
                'parent_id' => $shopManuscriptTakenFeedback->id,
            ]);
        }

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Successfully approved feedback.'),
        ]);

    }

    public function destroyFeedback($id): RedirectResponse
    {
        $feedback = ShopManuscriptTakenFeedback::findOrFail($id);
        $feedback->forceDelete();

        return redirect()->back();
    }

    // made a fix here
    // search me to update
    public function updateTaken($shopManuscriptTakenID, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);
        $shopManuscriptTaken->feedback_user_id = $request->feedback_user_id;
        $shopManuscriptTaken->expected_finish = $request->expected_finish;
        $shopManuscriptTaken->editor_expected_finish = $request->editor_expected_finish;
        $shopManuscriptTaken->grade = $request->grade;
        $shopManuscriptTaken->save();

        $updatedManuscript = ShopManuscriptsTaken::find($shopManuscriptTakenID);
        if ($updatedManuscript && $updatedManuscript->expected_finish != null) {
            $emailTemplate = EmailTemplate::where('page_name', 'Manuscript')->first();

            $user = User::find($updatedManuscript->user_id);

            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $to = $user->email;

            $replace_string = Carbon::parse($request->expected_finish)->format('d.m.Y');
            $replace_content = str_replace('_date_', $replace_string, $emailTemplate->email_content);
            $email_body = $replace_content;

            $subject = $emailTemplate->subject;
            $emailData['email_subject'] = $subject;
            $emailData['email_message'] = $email_body;
            $emailData['from_name'] = null;
            $emailData['from_email'] = 'postmail@forfatterskolen.no';
            $emailData['attach_file'] = null;

            /*\Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            $this->saleService->createEmailHistory($subject, 'postmail@forfatterskolen.no', $email_body,
                'shop-manuscripts-taken-expected-finish', $shopManuscriptTakenID);*/

            dispatch(new AddMailToQueueJob($to, $subject, $email_body, 'postmail@forfatterskolen.no', null, null,
                'shop-manuscripts-taken-expected-finish', $shopManuscriptTakenID));

            // mail($to, 'Forventet dato for tilbakemelding', $email_body, $headers);
            // AdminHelpers::send_email('Forventet dato for tilbakemelding', 'post@forfatterskolen.no', $to, $email_body);
        }

        return redirect()->back();
    }

    public function editorAcceptRequest($taken_id, $accepted, $request_id)
    {
        if ($accepted) {

            $requestToEditor = RequestToEditor::find($request_id);
            $requestToEditor->answer = 'yes';
            $requestToEditor->save();

            // assign the editor
            $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($taken_id);
            $shopManuscriptTaken->feedback_user_id = Auth::user()->id;
            $shopManuscriptTaken->save();

            // send an email to the learner
            if ($shopManuscriptTaken->expected_finish) {
                $emailTemplate = EmailTemplate::where('page_name', 'Manuscript')->first();

                $user = User::find($shopManuscriptTaken->user_id);
                $to = $user->email;

                $replace_string = Carbon::parse($shopManuscriptTaken->expected_finish)->format('d.m.Y');
                $replace_content = str_replace('_date_', $replace_string, $emailTemplate->email_content);
                $email_body = $replace_content;

                $subject = $emailTemplate->subject;

                dispatch(new AddMailToQueueJob($to, $subject, $email_body, $emailTemplate->from_email, null, null,
                    'shop-manuscripts-taken-expected-finish', $taken_id));

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Shop Manuscript accepted.'),
                    'alert_type' => 'success',
                ]);
            }

        } else {
            $requestToEditor = RequestToEditor::find($request_id);
            $requestToEditor->answer = 'no';
            $requestToEditor->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Shop Manuscript rejected.'),
                'alert_type' => 'success',
            ]);
        }
    }

    public function freeManuscriptIndex(): View
    {
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=', 0)->orderBy('created_at', 'desc')->get();
        $archiveManuscripts = FreeManuscript::where('is_feedback_sent', '=', 1)->orderBy('created_at', 'desc')->get();

        $emailTemplate = EmailTemplate::where('page_name', 'Free Manuscript')->first();
        $emailTemplateRoute = 'admin.manuscript.add_email_template';
        $isUpdate = 0;
        if (count($emailTemplate)) {
            $emailTemplateRoute = 'admin.manuscript.edit_email_template';
            $isUpdate = 1;
        }

        return view('backend.shop-manuscript.free-manuscripts',
            compact('freeManuscripts', 'archiveManuscripts', 'emailTemplate', 'emailTemplateRoute', 'isUpdate')
        );
    }

    public function deleteFreeManuscript($id): RedirectResponse
    {
        $freeManuscripts = FreeManuscript::findOrFail($id);
        $freeManuscripts->forceDelete();

        return redirect()->back();
    }

    public function assignEditor($id, Request $request): RedirectResponse
    {
        $freeManuscripts = FreeManuscript::findOrFail($id);
        $freeManuscripts->editor_id = $request->editor_id;
        $freeManuscripts->save();

        return redirect()->back();
    }

    /**
     * Update the genre of the shop manuscript taken
     */
    public function updateGenre($shopManuscriptTakenID, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);
        if ($shopManuscriptTaken) {
            $shopManuscriptTaken->genre = $request->genre;
            $shopManuscriptTaken->save();
        }

        return redirect()->back();
    }

    public function updateCoachingTimeLater($shopManuscriptTakenID, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);
        if ($shopManuscriptTaken) {
            $shopManuscriptTaken->coaching_time_later = $request->coaching_time_later;
            $shopManuscriptTaken->save();
        }

        return redirect()->back();
    }

    public function updateDescription($shopManuscriptTakenID, Request $request): RedirectResponse
    {
        $shopManuscriptTaken = ShopManuscriptsTaken::findOrFail($shopManuscriptTakenID);
        if ($shopManuscriptTaken) {
            $shopManuscriptTaken->description = $request->description;
            $shopManuscriptTaken->save();
        }

        return redirect()->back();
    }

    public function sendFeedback($id, Request $requests): RedirectResponse
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $freeManuscripts = FreeManuscript::findOrFail($id);

        $freeManuscripts->is_feedback_sent = 1;
        $freeManuscripts->feedback_content = $requests->email_content;
        $freeManuscripts->save();

        $editor = User::find($freeManuscripts->editor);
        $to = $freeManuscripts->email;
        // $from               = $editor->email;

        $params = [
            'api_key' => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

            // this is the action that adds a contact
            'api_action' => 'contact_add',

            // define the type of output you wish to get back
            // possible values:
            // - 'xml'  :      you have to write your own XML parser
            // - 'json' :      data is returned in JSON format and can be decoded with
            //                 json_decode() function (included in PHP since 5.2.0)
            // - 'serialize' : data is returned in a serialized format and can be decoded with
            //                 a native unserialize() function
            'api_output' => 'serialize',
        ];

        // here we define the data we are posting in order to perform an update
        $post = [
            'email' => $freeManuscripts->email,
            'first_name' => $freeManuscripts->name,
            'tags' => 'Tekstvurdering',
            // assign to lists:
            'p[123]' => 51, // example list ID (REPLACE '123' WITH ACTUAL LIST ID, IE: p[5] = 5)
            'status[123]' => 1, // 1: active, 2: unsubscribed (REPLACE '123' WITH ACTUAL LIST ID, IE: status[5] = 1)
            'instantresponders[123]' => 0, // set to 0 to if you don't want to sent instant autoresponders
        ];

        // This section takes the input fields and converts them to the proper format
        $query = '';
        foreach ($params as $key => $value) {
            $query .= urlencode($key).'='.urlencode($value).'&';
        }
        $query = rtrim($query, '& ');

        // This section takes the input data and converts it to the proper format
        $data = '';
        foreach ($post as $key => $value) {
            $data .= urlencode($key).'='.urlencode($value).'&';
        }
        $data = rtrim($data, '& ');

        // clean up the url
        $url = rtrim($url, '/ ');

        // This sample code uses the CURL library for php to establish a connection,
        // submit your request, and show (print out) the response.
        if (! function_exists('curl_init')) {
            exit('CURL not supported. (introduced in PHP 4.0.2)');
        }

        // If JSON is used, check if json_decode is present (PHP 5.2.0+)
        if ($params['api_output'] == 'json' && ! function_exists('json_decode')) {
            exit('JSON not supported. (introduced in PHP 5.2.0)');
        }

        // define a final API request - GET
        $api = $url.'/admin/api.php?'.$query;

        $request = curl_init($api); // initiate curl object
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
        // curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

        $response = (string) curl_exec($request); // execute curl post and store results in $response

        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object

        if (! $response) {
            exit('Nothing was returned. Do you have a connection to Email Marketing server?');
        }

        $result = unserialize($response);

        $email_content = $requests->email_content;

        ob_start();
        include base_path().'/resources/views/emails/free-manuscript-feedback.blade.php';
        $message = ob_get_clean();

        $headers = "From: Forfatterskolen<post@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        // $headers .= 'Reply-To: '. $from . "\r\n";

        $subject = 'Tilbakemelding på din tekst';
        $from = 'postmail@forfatterskolen.no';

        // AdminHelpers::send_mail($to, $subject, $message, $from );
        // AdminHelpers::send_email($subject, $from, $to, $message);
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = $from;
        $emailData['attach_file'] = null;

        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        // mail($to, 'Subject', $message, $headers);

        return redirect()->back();
    }

    public function testEmail()
    {
        /*AdminHelpers::send_email('Subject','post@forfatterskolen.no','elybutabara@yahoo.com','this is a test only');
        echo "<br/>sent";*/

        $message = 'Inquiry Message'.PHP_EOL;
        $message .= 'Name: Ely'.PHP_EOL;
        $message .= 'Email: elybutabara@gmail.com'.PHP_EOL;
        $message .= 'Message: this is my message';

        $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail('elybutabara@yahoo.com', 'Inquiry Message', $message, $headers);
        echo 'sent';
    }
}
