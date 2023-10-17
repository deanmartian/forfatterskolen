<?php

namespace App\Http;

use App\Assignment;
use App\AssignmentDisabledLearner;
use App\AssignmentFeedback;
use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\EmailTemplate;
use App\Genre;
use App\Mail\SubjectBodyEmail;
use App\Notification;
use App\Order;
use App\Package;
use App\PaymentPlan;
use App\ShopManuscript;
use App\User;
use App\WebinarEmailOut;
use App\Workshop;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Log;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport;

class AdminHelpers
{
	public static function newButtonMenu()
	{
	?>
	<ul class="newButtonMenu">
		<li><a href="">Course</a></li>
		<li><a href="">Learner</a></li>
		<li><a href="">Assignment</a></li>
		<li><a href="">Manuscript</a></li>
		<li><a href="">Webinar</a></li>
	</ul>
	<?php
	}

	public static function courseSubpages()
	{
		$subpages = ['overview', 'lessons', 'manuscripts', 'videos', 'assignments', 'webinars', 'workshops', 'dripping',
            'packages', 'learners', 'email-out', 'reward-coupons', 'surveys', 'certificate'];
		return $subpages;
	}

	public static function validateCourseSubpage($section)
	{
		if( in_array($section, self::courseSubpages()) ) :
			return true;
		else :
			return abort('404');
			/*die();*/
		endif;
	}

	public static function courseAddLearners($courseLearners)
	{
		$users = \App\User::where('role', 2)->whereNotIn('id', $courseLearners)->get();
		return $users;
	}

    public static function courseList($id = NULL)
    {
        $course = new Course();
        if ($id) {
            return $course->find($id);
        }

        return $course->all();
    }

    public static function editorList()
    {
        return \App\User::where(function($query){
            $query->whereIn('role', [3])
                ->orWhere('admin_with_editor_access', 1);
        })
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function copyEditingEditors()
    {
        return \App\User::where(function($query){
            $query->whereIn('role', [3])
                ->orWhere('admin_with_editor_access', 1);
        })
            ->where('is_copy_editing_admin', 1)
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function correctionEditors()
    {
        return \App\User::where(function($query){
            $query->whereIn('role', [3])
                ->orWhere('admin_with_editor_access', 1);
        })
            ->where('is_correction_admin', 1)
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function editorAndAdminList()
    {
        return \App\User::where(function($query){
            $query->whereIn('role', [1,3])
                ->orWhere('admin_with_editor_access', 1);
        })
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function editorByAdminQuery($field)
    {
        return \App\User::where(function($query){
            $query->whereIn('role', [1, 3])
                ->orWhere('admin_with_editor_access', 1);
        })
            ->where($field, 1)
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();
    }
	
	public static function currencyFormat($value)
	{
		return 'Kr ' . number_format($value, 2, ",", ".");
	}


	public static function isDate($string)
	{
		$d = \DateTime::createFromFormat('Y-m-d', $string);
    	return $d && $d->format('Y-m-d') === $string;
	}

    public static function isDateWithFormat($format, $string)
    {
        $d = \DateTime::createFromFormat($format, $string);
        return $d && $d->format($format) === $string;
    }


	public static function get_num_of_words($string) {
	    $string = preg_replace('/\s+/', ' ', trim($string));
	    $words = explode(" ", strip_tags($string));
	    return count($words);
	}

    /**
     * Create a notification
     * @param $data array
     */
    public static function createNotification($data)
    {
        Notification::create($data);
	}

    /**
     * Send email using Swift Mailer
     * @param $subject
     * @param $from
     * @param $to
     * @param $content
     * @param string $from_name Not required field with default value
     * @return bool
     */
    public static function send_email($subject, $from, $to, $content, $from_name='Forfatterskolen', $attachment = null)
    {
        $from = $from ?: 'postmail@forfatterskolen.no';
        $host = env('MAIL_HOST_SITE');
        $port = env('MAIL_PORT_SITE');
        $email_sender = config('mail.username');//env('MAIL_USERNAME');
        $email_pass = config('mail.password');//env('MAIL_PASSWORD');

        // set mailer
        $transport = \Swift_SmtpTransport::newInstance($host, $port, 'ssl');
        $transport->setUsername($email_sender);
        $transport->setPassword($email_pass);

        //set message
        $message = Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setFrom($from, $from_name);
        $message->setTo($to);
        $message->setBody($content, 'text/html');

        if ($attachment) {
            if (is_array($attachment)) {
                foreach ($attachment as $attach) {
                    $message->attach(\Swift_Attachment::fromPath(asset($attach)));
                }
            } else {
                $message->attach(\Swift_Attachment::fromPath(asset($attachment)));
            }
        }

        //send message
        $mailer = new Swift_Mailer($transport);
        if ($mailer->send($message)) {
            return true;
        }
        return false;
	}

    public static function send_mail( $subject, $from, $to, $content, $from_name='Forfatterskolen')
    {
        $headers = "From: ".$from_name."<".$from.">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        /*if ($from) {
            $headers .= 'Reply-To: '. $from . "\r\n";
        }*/

        mail($to, $subject, $content, $headers);
	}

    /**
     * @param $to
     * @param $subject
     * @param $email_message
     * @param $from_email
     * @param null $from_name
     * @param null $attachment
     */
    public static function queue_mail($to, $subject, $email_message, $from_email, $from_name = NULL, $attachment = NULL)
    {
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $email_message;
        $emailData['from_name'] = $from_name;
        $emailData['from_email'] = $from_email;
        $emailData['attach_file'] = $attachment;

        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
	}

    /**
     * @param $email_content
     * @param $to
     * @param $first_name
     * @param $redirect_link
     * @return mixed
     */
    public static function formatEmailContent($email_content, $to, $first_name, $redirect_link)
    {
        $encode_email = encrypt($to);
        $redirectLink = encrypt($redirect_link);
        $search_string = [
            ':firstname',
            ':redirect_link',
            ':end_redirect_link'
        ];
        $replace_string = [
            $first_name,
            "<a href='" . route('auth.login.emailRedirect',[$encode_email, $redirectLink]) . "'>" ,
            "</a>"
        ];

        return str_replace($search_string, $replace_string, $email_content);
	}

    public static function checkNearlyExpiredCourses()
    {
        $url = 'https://forfatterskolen.api-us1.com';

        //$courses_taken = CoursesTaken::where('user_id', 899)->get();
        $courses_taken = CoursesTaken::all();
        $now = Carbon::now();

        foreach ($courses_taken as $course) {
            $end =  Carbon::parse($course->end_date);
            $length = $end->diffInDays($now);

            if ($length <= 30) {
                $updateCourse = CoursesTaken::find($course->id);
                $updateCourse->sent_renew_email = 1;
                $updateCourse->save();

                $user = User::find($course->user_id);

                $params = array(
                    'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

                    // this is the action that adds a contact
                    'api_action'   => 'automation_contact_add',
                    'api_output'   => 'serialize'
                );

                // here we define the data we are posting in order to perform an update
                $post = array(
                    'contact_email' => $user->email,
                    'automation' => 71,
                    'full_name' => $user->firstname
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
            }
        }
	}

    public static function checkNearlyExpiredCoursesCount()
    {

        $courses_taken = CoursesTaken::all();
        $now = Carbon::now();
        $nearlyExpireCount = 0;

        foreach ($courses_taken as $course) {
            $end =  Carbon::parse($course->end_date);
            $length = $end->diffInDays($now);

            if ($length <= 30) {
                $nearlyExpireCount++;
            }
        }

        return $nearlyExpireCount;
    }

    /**
     * Get the group where the learner is assigned
     * @param $assignment_id
     * @param $learner_id
     * @return null
     */
    public static function getLearnerAssignmentGroup($assignment_id, $learner_id)
    {
        $assignmentGroups = \App\AssignmentGroup::where('assignment_id', $assignment_id)->pluck('id')->toArray();
        if ($assignmentGroups) {
            $groupLearner = \App\AssignmentGroupLearner::whereIn('assignment_group_id', $assignmentGroups)
                ->where('user_id', $learner_id)->first();
            if ($groupLearner) {
                return [ 'id' => $groupLearner->group->id, 'title' => $groupLearner->group->title,
                 'group_learner_id' => $groupLearner->id];
            }
        }

        return NULL;
    }

    public static function getAssignmentFeedbackByGroupLearnerIdAndEditorId($groupLearnerId, $editorId)
    {
        return AssignmentFeedback::where([
            'assignment_group_learner_id' => $groupLearnerId,
            'user_id' => $editorId
        ])->first();
    }

    /**
     * Get learner list
     * @param null $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public static function getLearnerList($id = NULL)
    {
        if ($id) {
            return User::find($id);
        }
        return User::where('role', 2)->get();
    }

    /**
     * Get order details
     * @param $order Order
     * @return string
     */
    public static function getOrderDetails($order)
    {
        $orderDetails = '';

        if (in_array($order->type, [1, 6])) {
            $package = Package::find($order->package_id);
            $paymentPlan = PaymentPlan::find($order->plan_id);
            $orderDetails = "<a href='".route('admin.course.show', $order->item_id)."?section=packages'>"
                .$package->variation."</a>"." - ".$paymentPlan->plan;
        }

        if (in_array($order->type, [2, 7])) {
            $shopManuscript = ShopManuscript::find($order->item_id);
            $orderDetails = "<a href='".route('admin.shop-manuscript.index')."'>"
                .$shopManuscript->title."</a>";
        }

        switch ($order->type) {
            case 3:
                $workshop = Workshop::find($order->item_id);
                $orderDetails = "<a href='".route('admin.workshop.show', $workshop->id)."'>"
                    .$workshop->title."</a>";
                break;
            case 4:
                $orderDetails = trans('site.front.correction.title');
                break;
            case 5:
                $orderDetails = trans('site.front.copy-editing.title');
                break;
            case 8:
                $assignment = Assignment::find(($order->item_id));
                $orderDetails = "<a href='".route('admin.assignment.show',
                        ['course_id' => $assignment->course->id, 'assignment' => $assignment->id])."'>"
                    .$assignment->title."</a>";
                break;
            case 10:
                $orderDetails = "Editing Service";
                break;
        }

        return $orderDetails;
    }

    public static function emailTemplate($page_name)
    {
        return EmailTemplate::where('page_name', $page_name)->first();
    }

    public static function isWebinarPakkeActive( $user_id )
    {
        $user = User::find($user_id);
        $courseTaken = $user->coursesTaken->where('package_id', 29)->first();
        if ($courseTaken) {
            $end_date = $courseTaken->end_date ?: Carbon::parse($courseTaken->started_at)->addYear(1);

            if (Carbon::parse($end_date)->gt(Carbon::today())) {
                return true;
            }
        }

        return false;
    }

    public static function addToAutomation($email, $automation_id, $name)
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $params = array(
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

            // this is the action that adds a contact
            'api_action'   => 'automation_contact_add',
            'api_output'   => 'serialize'
        );

        // here we define the data we are posting in order to perform an update
        $post = array(
            'contact_email' => $email,
            'automation' => $automation_id,
            'full_name' => $name
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
	}

    /**
     * Add/edit user to active campaign list
     * @param $list_id int
     * @param $data array
     * @return bool
     */
    public static function addToActiveCampaignList($list_id, $data)
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $params = array(
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',
            'api_output'   => 'serialize'
        );

        // CHECK IF SUBSCRIBER EXISTS
        $params["api_action"] = "contact_view_email";
        $params["email"] = $data['email'];
        $exists = AdminHelpers::curl($url, $params, array());

        if ($exists['result_code']) {
            // SUBSCRIBER IS FOUND IN THE SYSTEM - EDIT THEM
            $params["api_action"] = "contact_edit";

            // ARRAY OF VALUES TO BE POSTED
            $contact_id = $exists['id'];
            $post = array(
                "email" => $exists["email"],
                "first_name" => $data['name'],
                "id" => $contact_id
            );
            foreach ($exists["lists"] as $list)
            {
                // RETAIN THEIR EXISTING LISTS
                $post["p[" . $list["listid"] . "]"] = $list["listid"];

                // RETAIN THEIR EXISTING STATUSES
                $post["status[" . $list["listid"] . "]"] = $list["status"];
            }

            // ADD ANY NEW LISTS?
            $post["p[".$list_id."]"] = $list_id; // $list_id IS THE LIST ID
            $post["status[".$list_id."]"] = 1; // $list_id IS THE LIST ID, 1 = ACTIVE STATUS
            $post["first_name_list[".$list_id."]"] = $data['name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            if (isset($data['last_name'])) {
                $post["last_name"] = $data['last_name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
                $post["last_name_list[".$list_id."]"] = $data['last_name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            }
            $edit = AdminHelpers::curl($url, $params, $post);
            return true;

        } else {
            // SUBSCRIBER IS NOT FOUND - ADD THEM

            $params["api_action"] = "subscriber_add";

            // ARRAY OF VALUES TO BE POSTED
            $post = array(
                "email" => $data['email'],
                "first_name" => $data['name']
            );

            // ADD TO LIST
            $post["p[".$list_id."]"] = $list_id; // $list_id IS THE LIST ID
            $post["status[".$list_id."]"] = 1; // $list_id IS THE LIST ID, 1 = ACTIVE STATUS
            $post["first_name_list[".$list_id."]"] = $data['name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            if (isset($data['last_name'])) {
                $post['last_name'] = $data['last_name'];
                $post["last_name_list[".$list_id."]"] = $data['last_name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            }
            $add =  AdminHelpers::curl($url, $params, $post);
            return true;
        }
	}

    public static function addToZagomailList($list_id, $data)
    {
        $curl = curl_init();
        $data['publicKey'] = '2e4e9e238d2d08a31827c0e930b4294a01887b0a';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.zagomail.com/lists/subscriber-create?list_uid=' . $list_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);

        // Check for cURL errors
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            Log::info( "cURL Error: " . $error);
        }
        
        curl_close($curl);
        
        $decoded_response = json_decode($response);

        if ($decoded_response->status === 'success') {
            Log::info("Email " . $data['email'] . ' is added to zagolist = ' . $list_id);
        } else {
            Log::info("----------- error for zagolist ------------");
            Log::info("Email " . $data['email'] . ' is not added to zagolist = ' . $list_id);
            Log::info($response);
        }
        
    }

    public static function addToActiveCampaignListTest($list_id, $data)
    {
        $url = 'https://forfatterskolen.api-us1.com';

        $params = array(
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',
            'api_output'   => 'serialize'
        );

        // CHECK IF SUBSCRIBER EXISTS
        $params["api_action"] = "contact_view_email";
        $params["email"] = $data['email'];
        $exists = AdminHelpers::curl($url, $params, array());

        if ($exists['result_code']) {
            // SUBSCRIBER IS FOUND IN THE SYSTEM - EDIT THEM
            $params["api_action"] = "contact_edit";

            // ARRAY OF VALUES TO BE POSTED
            $contact_id = $exists['id'];
            $post = array(
                "email" => $exists["email"],
                "first_name" => $data['name'],
                "id" => $contact_id
            );
            foreach ($exists["lists"] as $list)
            {
                // RETAIN THEIR EXISTING LISTS
                $post["p[" . $list["listid"] . "]"] = $list["listid"];

                // RETAIN THEIR EXISTING STATUSES
                $post["status[" . $list["listid"] . "]"] = $list["status"];
            }

            // ADD ANY NEW LISTS?
            $post["p[".$list_id."]"] = $list_id; // $list_id IS THE LIST ID
            $post["status[".$list_id."]"] = 1; // $list_id IS THE LIST ID, 1 = ACTIVE STATUS
            $post["first_name_list[".$list_id."]"] = $data['name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            if (isset($data['last_name'])) {
                $post["last_name"] = $data['last_name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
                $post["last_name_list[".$list_id."]"] = $data['last_name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            }
            $edit = AdminHelpers::curl($url, $params, $post);
            return true;

        } else {
            // SUBSCRIBER IS NOT FOUND - ADD THEM

            $params["api_action"] = "contact_add";

            // ARRAY OF VALUES TO BE POSTED
            $post = array(
                "email" => $data['email'],
                "first_name" => $data['name']
            );

            // ADD TO LIST
            $post["p[".$list_id."]"] = $list_id; // $list_id IS THE LIST ID
            $post["status[".$list_id."]"] = 1; // $list_id IS THE LIST ID, 1 = ACTIVE STATUS
            //$post["first_name_list[".$list_id."]"] = $data['name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            if (isset($data['last_name'])) {
                $post['last_name'] = $data['last_name'];
                //$post["last_name_list[".$list_id."]"] = $data['last_name']; // (OPTIONAL) CHANGE FIRST NAME FOR ONLY THIS NEW LIST
            }
            $add =  AdminHelpers::curl($url, $params, $post);
            return "add".$post;
            return true;
        }
    }

    /**
     * Get active campaign data by searching email
     * @param $email
     * @return mixed
     */
    public static function getActiveCampaignDataByEmail($email)
    {
        // By default, this sample code is designed to get the result from your ActiveCampaign installation and print out the result
        $url = 'https://forfatterskolen.api-us1.com';


        $params = array(

            // the API Key can be found on the "Your Settings" page under the "API" tab.
            // replace this with your API Key
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

            // this is the action that fetches a contact info based on the ID you provide
            'api_action'   => 'contact_view_email',
            //'api_action' => 'contact_view', // this one also works

            // define the type of output you wish to get back
            // possible values:
            // - 'xml'  :      you have to write your own XML parser
            // - 'json' :      data is returned in JSON format and can be decoded with
            //                 json_decode() function (included in PHP since 5.2.0)
            // - 'serialize' : data is returned in a serialized format and can be decoded with
            //                 a native unserialize() function
            'api_output'   => 'serialize',

            'email'        => $email,
        );

        // This section takes the input fields and converts them to the proper format
        $query = "";
        foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
        $query = rtrim($query, '& ');

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
        //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);

        $response = (string)curl_exec($request); // execute curl fetch and store results in $response

        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object

        if ( !$response ) {
            die('Nothing was returned. Do you have a connection to Email Marketing server?');
        }

        // This line takes the response and breaks it into an array using:
        // JSON decoder
        //$result = json_decode($response);
        // unserializer
        $result = unserialize($response);
        return $result;
	}

    /**
     * Update active campaign contact email for a list
     * @param $user_id int subscriber id
     * @param $email string new email to be used
     * @param $list_id int id of the list
     * @return mixed
     */
    public static function updateActiveCampaignContactEmailForList($user_id, $email, $list_id)
    {
        // By default, this sample code is designed to get the result from your ActiveCampaign installation and print out the result
        $url = 'https://forfatterskolen.api-us1.com';


        $params = array(

            // the API Key can be found on the "Your Settings" page under the "API" tab.
            // replace this with your API Key
            'api_key'      => 'ee9f1cb27fe33c7197d722f434493d4440cf5da6be8114933fd0fdae40fc03a197388b99',

            // this is the action that modifies contact info based on the ID you provide
            'api_action'   => 'contact_edit',

            // define the type of output you wish to get back
            // possible values:
            // - 'xml'  :      you have to write your own XML parser
            // - 'json' :      data is returned in JSON format and can be decoded with
            //                 json_decode() function (included in PHP since 5.2.0)
            // - 'serialize' : data is returned in a serialized format and can be decoded with
            //                 a native unserialize() function
            'api_output'   => 'serialize',

            // by default, it overwrites all contact data. set to 0 to only update supplied post parameters
            //'overwrite'    =>  0,
        );

        // here we define the data we are posting in order to perform an update
        $post = array(
            'id'                       => $user_id, // example contact ID to modify
            'email'                    => $email,

            // any custom fields
            //'field[345,DATAID]'      => 'field value', // where 345 is the field ID, and DATAID is the ID of the contact's data row
            //'field[%PERS_1%,0]'      => 'field value', // using the personalization tag instead (make sure to encode the key)

            // assign to lists:
            'p['.$list_id.']'                   => $list_id, // example list ID (REPLACE '123' WITH ACTUAL LIST ID, IE: p[5] = 5)
            // WARNING: if overwrite = 1 (which is the default) this call will silently UNSUBSCRIBE this contact from any lists not included in this parameter.
            'status['.$list_id.']'              => 1, // 1: active, 2: unsubscribed (REPLACE '123' WITH ACTUAL LIST ID, IE: status[5] = 0)
            //'first_name_list[123]'   => 'FirstName', // overwrite global first name with list-specific first name
            //'last_name_list[123]'    => 'LastName', // overwrite global last name with list-specific last name
            //'noresponders[123]'      => 1, // uncomment to set "do not send any future responders"
            // use the folowing only if status=1
            'instantresponders[123]' => 0, // set to 0 to if you don't want to sent instant autoresponders
            //'lastmessage[123]'       => 1, // uncomment to set "send the last broadcast campaign"
            // use the folowing only if status=2
            //'sendoptout[123]'        => 1, // uncomment to send opt-out confirmation email
            //'unsubreason[1]'         => 'Reason for unsubscribing',

            //'p[345]'                 => 345, // some additional lists?
            //'status[345]'            => 1, // some additional lists?
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

        $response = (string)curl_exec($request); // execute curl fetch and store results in $response

        // additional options may be required depending upon your server configuration
        // you can find documentation on curl options at http://www.php.net/curl_setopt
        curl_close($request); // close curl object

        if ( !$response ) {
            die('Nothing was returned. Do you have a connection to Email Marketing server?');
        }

        // This line takes the response and breaks it into an array using:
        // JSON decoder
        //$result = json_decode($response);
        // unserializer
        $result = unserialize($response);
        return $result;
	}

    /**
     * @param $url
     * @param $params
     * @param null $post_data
     * @return mixed
     */
    public static function curl($url, $params, $post_data = NULL)
    {
        // This section takes the input fields and converts them to the proper format
        $query = "";
        foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
        $query = rtrim($query, '& ');

        $data = "";

            // This section takes the input data and converts it to the proper format
            foreach( $post_data as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
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
        return $result;
	}

    public static function formatBytes($bytes) {
        $base = log($bytes) / log(1024);
        $suffix = array("", "KB", "MB", "GB", "TB");
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }

    /**
     * Allow duplicate filename and just add an increment to it
     * @param $path
     * @param $filename
     * @param $extension
     * @return string
     */
    public static function checkFileName($path, $filename, $extension)
    {
        $i = 1;

        // check first if the filename without the increment exists
        if (file_exists("$path/$filename.$extension")) {
            while(file_exists("$path/$filename ($i).$extension")) $i++;
            $newName = "$path/$filename ($i).$extension";
        } else {
            $newName = "$path/$filename.$extension";
        }
        return $newName;
    }

    /**
     * Get the file name from the whole file with path
     * @param $file
     * @return mixed
     */
    public static function extractFileName($file)
    {
        $file = explode('/', $file);
        return end($file);
    }

    /**
     * Set flash message
     * @param $level
     * @param $message
     */
    public static function addFlashMessage($level, $message)
    {
        session()->flash('message.level', $level);
        session()->flash('message.content', $message);
    }

    /**
     * Type of assignment uploaded
     * @param null $id
     * @return mixed
     */
    public static function assignmentType($id = NULL)
    {

        $genre = Genre::all();

        if ($id >= 0 && !is_null($id)) {
            $genre = 'None';
            $findGenre = Genre::find($id);

            if ($id > 0 && $findGenre) {
                $genre = $findGenre->name;
            }
        }

        return $genre;
        /*$types = array(
            array( 'id' => 1, 'option' => 'Barnebok'),
            array( 'id' => 2, 'option' => 'Fantasy'),
            array( 'id' => 3, 'option' => 'Skjønnlitterært'),
            array( 'id' => 4, 'option' => 'Serieroman'),
            array( 'id' => 5, 'option' => 'Sakprosa'),
            array( 'id' => 6, 'option' => 'Selvbiografi'),
            array( 'id' => 7, 'option' => 'Krim'),
            array( 'id' => 8, 'option' => 'Thriller'),
            array( 'id' => 9, 'option' => 'Grøsser'),
            array( 'id' => 10, 'option' => 'Lyrikk'),
            array( 'id' => 11, 'option' => 'Ungdom'),
            array( 'id' => 12, 'option' => 'Dokumentar'),
            array( 'id' => 13, 'option' => 'Sci-fi'),
            array( 'id' => 14, 'option' => 'Dystopi'),
            array( 'id' => 15, 'option' => 'Valgfri'),
            array( 'id' => 16, 'option' => 'Feelgood'),
        );

        if ($id >= 0) {

            if ($id > 0) {
                foreach ($types as $type) {
                    if ($type['id'] == $id) {
                        return $type['option'];
                    }
                }
            }
            return "None";
        }

        return $types;*/
    }

    /**
     * Where could it be found in manuscript
     * Manuscript type for assignment either whole, start, middle or last part of the manuscript
     * @param null $id
     * @return mixed
     */
    public static function manuscriptType($id = NULL)
    {
        $types = array(
            array( 'id' => 1, 'option' => 'Hele manuset'),
            array( 'id' => 2, 'option' => 'Starten av manuset'),
            array( 'id' => 3, 'option' => 'Midten av manuset'),
            array( 'id' => 4, 'option' => 'Slutten av manuset'),
        );

        if ($id >= 0) {

            if ($id > 0) {
                foreach ($types as $type) {
                    if ($type['id'] == $id) {
                        return $type['option'];
                    }
                }
            }
            return "None";
        }

        return $types;
    }

    public static function pageList($id = NULL)
    {
        $pages = array(
            array( 'id' => 1, 'option' => 'Courses', 'route' => 'admin.course.index', 'request_name' => 'course'),
            array( 'id' => 2, 'option' => 'Free Courses', 'route' => 'admin.free-course.index', 'request_name' => 'free-course'),
            array( 'id' => 3, 'option' => 'Workshops', 'route' => 'admin.workshop.index', 'request_name' => 'workshop'),
            array( 'id' => 4, 'option' => 'Learners', 'route' => 'admin.learner.index', 'request_name' => 'learner'),
            array( 'id' => 5, 'option' => 'Assignments', 'route' => 'admin.assignment.index', 'request_name' => 'assignment'),
            array( 'id' => 14, 'option' => 'Project', 'route' => 'admin.project.index', 'request_name' => 'project'),
            array( 'id' => 6, 'option' => 'Support', 'route' => 'admin.publishing.index', 'request_name' => 'publishing'),
            array( 'id' => 7, 'option' => 'Free Manuscripts', 'route' => 'admin.free-manuscript.index', 'request_name' => 'free-manuscript'),
            array( 'id' => 13, 'option' => 'Other Services', 'route' => 'admin.other-service.index', 'request_name' => 'other-service'),
            array( 'id' => 8, 'option' => 'Årshjul', 'route' => 'admin.yearly-calendar.index', 'request_name' => 'yearly_calendar'),
            //array( 'id' => 8, 'option' => 'Invoices', 'route' => 'admin.invoice.index', 'request_name' => 'invoice'),
            array( 'id' => 9, 'option' => 'Shop Manuscripts', 'route' => 'admin.shop-manuscript.index', 'request_name' => 'shop-manuscript'),
            array( 'id' => 10, 'option' => 'FAQs', 'route' => 'admin.faq.index', 'request_name' => 'faq'),
            array( 'id' => 11, 'option' => 'Admins', 'route' => 'admin.admin.index', 'request_name' => 'admin'),
            /*array( 'id' => 12, 'option' => 'Email', 'route' => 'admin.email.index', 'request_name' => 'email'),*/
            array('id'=> 12, 'option' => 'Head Editor', 'route'=> 'admin.head-editor-dashboard', 'request_name'=>'head-editor')
        );

        if ($id > 0) {
            foreach ($pages as $page) {
                if ($page['id'] == $id) {
                    return $page['option'];
                }
            }
        }

        return $pages;
    }

    public static function editorPageList($id = NULL)
    {
        $pages = array(
            array( 'id' => 1, 'option' => 'Pending Assignments', 'route' => 'editor.dashboard', 'request_name' => 'pending-assignments'),
            array( 'id' => 1, 'option' => 'Upcoming Assignment', 'route' => 'editor.upcoming-assignment', 'request_name' => 'upcoming-assignment'),
            array( 'id' => 2, 'option' => 'Assignment Archive', 'route' => 'editor.assignment-archive', 'request_name' => 'assignment-archive'),
            array( 'id' => 4, 'options' => 'Editor Settings', 'route' => 'editor.settings', 'request_name' => 'editor-settings'),
            array( 'id' => 5, 'options' => 'Assigned Webinar', 'route' => 'editor.assigned-webinar', 'request_name' => 'assigned-webinar'),
            //array( 'id' => 8, 'option' => 'Årshjul', 'route' => 'editor.yearly-calendar.index', 'request_name' => 'yearly_calendar')
            array( 'id' => 15, 'option' => 'Redaktørinnstruks', 'route' => 'editor.editors-note', 'request_name' => 'editors-note')
        );

        if ($id > 0) {
            foreach ($pages as $page) {
                if ($page['id'] == $id) {
                    return $page['option'];
                }
            }
        }

        return $pages;
    }

    public static function GAdminPageList($id = NULL)
    {
        $pages = array(
            array( 'id' => 1, 'option' => 'Dashboard', 'route' => 'g-admin.dashboard', 'request_name' => 'dashboard'),
            array( 'id' => 2, 'option' => 'Learners', 'route' => 'g-admin.learner.index', 'request_name' => 'learner'),
            array( 'id' => 14, 'option' => 'Project', 'route' => 'g-admin.project.index', 'request_name' => 'project'),
            array( 'id' => 3, 'option' => 'Self Publishing', 'route' => 'g-admin.self-publishing.index', 'request_name' => 'self-publishing'),
        );

        if ($id > 0) {
            foreach ($pages as $page) {
                if ($page['id'] == $id) {
                    return $page['option'];
                }
            }
        }

        return $pages;
    }

    public static function courseType($id = NULL)
    {
        $types = array(
            array('id' => 1, 'option' => 'Basic Course'),
            array('id' => 2, 'option' => 'Standard Course'),
            array('id' => 3, 'option' => 'Pro Course')
        );

        if ($id > 0) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
    }

    /**
     * @param null $id
     * @return array
     */
    public static function question_type($id = NULL)
    {
        $types = array(
            array('id' => 'text', 'option' => 'Text'),
            array('id' => 'textarea', 'option' => 'Textarea'),
            array('id' => 'checkbox', 'option' => 'Checkbox'),
            array('id' => 'radio', 'option' => 'Radio Buttons')
        );

        if ($id) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
    }

    /**
     * List of publisher book type
     * @param null $id
     * @return array|string
     */
    public static function publisher_book_type($id = NULL)
    {
        $types = array(
            array('id' => 1, 'option' => 'UTGITTE FORFATTERE'),
            array('id' => 2, 'option' => 'UTGITT PÅ VANITY FORLAG'),
            array('id' => 3, 'option' => 'SELVPUBLISERTE FORFATTERE'),
            array('id' => 4, 'option' => 'ANTOLOGI')
        );

        if ($id) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
    }

    public static function zoomWebinarApprovalType($id = NULL)
    {
        $types = array(
            array('id' => 0, 'option' => 'Automatically Approve'),
            array('id' => 1, 'option' => 'Manually Approve'),
            array('id' => 2, 'option' => 'No Registration Required')
        );

        if (is_numeric($id)) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
    }

    public static function zoomWebinarAudioOptions($id = NULL)
    {
        $types = array(
            array('id' => 'both', 'option' => 'Both Telephony and VoIP'),
            array('id' => 'telephony', 'option' => 'Telephony only'),
            array('id' => 'voip', 'option' => 'VoIP only')
        );

        if ($id > 0) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
    }

    public static function convertTZtoDateTime($date, $timezone)
    {
        // use the the appropriate timezone for your stamp
        $timestamp = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $date, new \DateTimeZone('UTC'));

        // set it to whatever you want to convert it
        $timestamp->setTimeZone(new \DateTimeZone($timezone));
        return $timestamp->format('Y-m-d H:i A');
    }

    public static function convertTZNoFormat($date, $timezone)
    {
        // use the the appropriate timezone for your stamp
        $timestamp = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $date, new \DateTimeZone('UTC'));

        // set it to whatever you want to convert it
        return $timestamp->setTimeZone(new \DateTimeZone($timezone));
    }

    public static function convertTZNoFixedTZFormat($date, $timezone) {
        $original = new \DateTime($date, new \DateTimeZone('UTC'));
        $timezoneName = timezone_name_from_abbr("", 1*3600, false);
        $modified = $original->setTimezone(new \DateTimezone($timezoneName));
        return $modified;
    }

    public static function createMessageBag($message = '')
    {
        $messageBag = new MessageBag();
        $messageBag->add('errors', $message);
        return $messageBag;
    }

    public static function getCronLogs()
    {
        return CronLog::orderBy('id', 'desc')->paginate(15);
    }

    /**
     * Get the email out of webinar
     * @param $webinar_id
     * @param $course_id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getWebinarEmailOut($webinar_id, $course_id)
    {
        return WebinarEmailOut::where('webinar_id', $webinar_id)->where('course_id', $course_id)
            ->first();
    }

    public static function learnerEmailTemplate()
    {
        return EmailTemplate::where('page_name', 'like', 'Send Email to Learner%')->get();
    }

    public static function isGiutbokPage()
    {
        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            return true;
        }

        return false;
    }

    public static function assignmentDisabledForLearner($assignment_id, $user_id)
    {
        return AssignmentDisabledLearner::where([
            'assignment_id' => $assignment_id,
            'user_id' => $user_id
        ])->first();
    }

    /**
     * Generate access token, used for every gt webinar request using oauth v2
     * @return mixed
     */
    public static function generateWebinarGTAccessToken()
    {
        $base_url = 'https://api.getgo.com/oauth/v2/token';
        $body = 'grant_type=password&username='.config('services.gotowebinar.user_id')
            .'&password='.config('services.gotowebinar.password');
        $encodedKey = base64_encode(config('services.gotowebinar.consumer_key').':'
            .config('services.gotowebinar.consumer_secret'));

        $header = array();
        $header[] = 'Content-type: application/x-www-form-urlencoded';
        $header[] = 'Accept: application/json';
        $header[] = 'Authorization: Basic '.$encodedKey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);
        return $decoded_response->access_token;
    }

    /**
     * Generate access token, used for every gt webinar request
     * @return mixed
     */
    public static function generateWebinarGTAccessTokenOrig()
    {
        $base_url = 'https://api.getgo.com/oauth/access_token';
        $body = 'grant_type=password&user_id='.config('services.gotowebinar.user_id')
            .'&password='.config('services.gotowebinar.password')
            .'&client_id='.config('services.gotowebinar.consumer_key');
        $encodedKey = base64_encode(config('services.gotowebinar.consumer_key').':'
            .config('services.gotowebinar.consumer_secret'));

        $header = array();
        $header[] = 'Content-type: application/x-www-form-urlencoded';
        $header[] = 'Accept: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);
        return $decoded_response->access_token;
    }

    public static function getGotoWebinarDetails($webinar_key, $access_token)
    {
        $base_url = 'https://api.getgo.com/G2W/rest/v2';
        //$access_token = 'qGtxQ1NfP4tws1cSRGRWJInmN1iU'; // from here http://app.gotowp.com/
        $org_key = '5169031040578858252';

        $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$webinar_key;

        // get the panelists of the webinar
        $header = array();
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/json';
        $header[] = 'Accept: application/vnd.citrix.g2wapi-v1.1+json';
        $header[] = 'Authorization: OAuth oauth_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $long_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        // surround all integer values with quotes
        $decoded_response = json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $response));

        return $decoded_response;
    }

    /**
     * Get the panelist of gotowebinar webinar
     * @param $webinar_key
     * @return mixed|string
     */
    public static function getGotoWebinarPanelist($webinar_key, $access_token)
    {
        $base_url = 'https://api.getgo.com/G2W/rest/v2';
        //$access_token = 'qGtxQ1NfP4tws1cSRGRWJInmN1iU'; // from here http://app.gotowp.com/
        $org_key = '5169031040578858252';

        $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$webinar_key.'/panelists';

        // get the panelists of the webinar
        $header = array();
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/json';
        $header[] = 'Accept: application/vnd.citrix.g2wapi-v1.1+json';
        $header[] = 'Authorization: OAuth oauth_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $long_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        // extract the panelist name
        $panelist = [];
        if (!empty($decoded_response) && is_array($decoded_response)) {
            foreach($decoded_response as $panel) {
                $panelist[] = $panel->name;
            }
        }

        // add comma or and if on the panelist name if necessary
        $last_element = $panelist ? array_pop($panelist) : '';
        $presenterList = $panelist
            ? implode(', ', $panelist).' and '.$last_element
            : $last_element;

        return $presenterList;
    }

    /**
     * Get user information using their ip
     * @param null $ip
     * @param string $purpose
     * @param bool $deep_detect
     * @return array|null|string
     */
    public static function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }

    public static function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);

                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }


        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = json_decode(curl_exec($curl));
        $info = curl_getinfo($curl);

        curl_close($curl);

        $response = array(
                'data' => $result,
                'http_code' => $info['http_code']
        );

        return $response;

    }

    public static function generateHash($length)
    {
        return substr(md5(microtime()), 0, $length);
    }

    public static function createDirectory($name)
    {
        if (!\File::exists($name)) {
            \File::makeDirectory($name);
        }
    }

    /**
     * Curl for vipps
     * @param $method
     * @param $loc_url
     * @param bool $data
     * @param array $header
     * @return array
     */
    public static function vippsAPI($method, $loc_url, $data = false, $header = [])
    {
        $curl = curl_init();
        $url = env('VIPPS_URL').$loc_url;

        $subscription_key = env('VIPPS_SUBSCRIPTION');

        $header[] = 'Ocp-Apim-Subscription-Key: '.$subscription_key;
        $header[] = 'Content-type: application/json';

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);

                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = json_decode(curl_exec($curl));
        $info = curl_getinfo($curl);

        curl_close($curl);

        $response = array(
            'data' => $result,
            'http_code' => $info['http_code']
        );

        return $response;

    }

    public static function getBigMarkerDetails($conference_id)
    {
        $url = config('services.big_marker.show_conference_link').$conference_id;
        $ch = curl_init();
        $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);
        return $decoded_response;
    }

    public static function getBigMarkerPanelist($panelists)
    {
        $panelList = [];
        foreach($panelists as $panelist) {
            $panelList[] = $panelist->first_name.' '.$panelist->last_name;
        }

        // add comma or and if on the panelist name if necessary
        $last_element = $panelList ? array_pop($panelList) : '';
        $presenterList = $panelList
            ? implode(', ', $panelList).' and '.$last_element
            : $last_element;

        return $presenterList;
    }
}



