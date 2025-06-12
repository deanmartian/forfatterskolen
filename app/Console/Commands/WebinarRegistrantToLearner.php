<?php

namespace App\Console\Commands;

use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\EmailAttachment;
use App\Http\AdminHelpers;
use App\Mail\SubjectBodyEmail;
use App\User;
use Illuminate\Console\Command;

class WebinarRegistrantToLearner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webinarregistranttolearner:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert webinar registrants to learner';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $access_token = AdminHelpers::generateWebinarGTAccessToken();
        $base_url = 'https://api.getgo.com/G2W/rest/v2';
        $org_key = '5169031040578858252';
        $webinar_key = '3548686272214906891';
        $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$webinar_key.'/registrants';

        // get the registrants of the webinar
        $header = [];
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
        CronLog::create(['activity' => 'WebinarRegistrantToLearner CRON running.']);
        foreach ($decoded_response as $registrant) {
            if ($registrant->status === 'APPROVED') {
                $firstName = $registrant->firstName;
                $lastName = $registrant->lastName;
                $user_email = $registrant->email;

                $course_id = 40;
                $course = Course::find($course_id);
                $package = $course->packages()->first();
                $user = User::where('email', $user_email)->first();

                if (! $user) {
                    $user = User::create([
                        'email' => $user_email,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'password' => bcrypt('Z5C5E5M2jv'),
                        'need_pass_update' => 1,
                    ]);
                }

                $alreadyAdded = CoursesTaken::where([
                    'package_id' => $package->id,
                    'user_id' => $user->id,
                ])->first();

                CoursesTaken::firstOrCreate([
                    'package_id' => $package->id,
                    'user_id' => $user->id,
                    'is_free' => 1,
                ]);

                $emailOut = $course->emailOut()->where('for_free_course', 1)->first();
                $subject = $emailOut->subject;
                $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
                $attachmentText = '';
                if ($emailAttachment) {
                    $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                        .AdminHelpers::extractFileName($emailAttachment->filename).'</a></p>';
                }

                $search_string = [
                    '[login_link]', '[username]', '[password]',
                ];
                $encode_email = encrypt($user_email);
                $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';
                $replace_string = [
                    $loginLink, $user_email, $password,
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message).$attachmentText;
                // check if already added
                if (! $alreadyAdded) {
                    // AdminHelpers::send_email($subject,'post@forfatterskolen.no', $user_email, $message);
                    $emailData['email_subject'] = $subject;
                    $emailData['email_message'] = $message;
                    $emailData['from_name'] = null;
                    $emailData['from_email'] = 'postmail@forfatterskolen.no';
                    $emailData['attach_file'] = null;

                    \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
                    CronLog::create(['activity' => 'WebinarRegistrantToLearner CRON send email to '.$user_email]);
                    echo $user_email."\n";
                }
            }
        }

        CronLog::create(['activity' => 'WebinarRegistrantToLearner CRON done running.']);
    }
}
