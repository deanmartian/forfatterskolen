<?php

namespace App\Console\Commands;

use App\CronLog;
use App\FreeCourseDelayedEmail;
use App\Http\AdminHelpers;
use App\Mail\FreeCourseNewUserEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FreeCourseDelayedEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freecoursedelayedemail:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email after 10 mins to new users that register to free course';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $from = Carbon::now()->format('Y-m-d H:i:00');
        $to = Carbon::now()->format('Y-m-d H:i:59');

        $delayedEmails = FreeCourseDelayedEmail::whereBetween('send_at', [$from, $to])->get();

        foreach($delayedEmails as $delayedEmail) {
            $course = $delayedEmail->course;
            $user = $delayedEmail->user;
            $encode_email = encrypt($user->email);

            $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
            $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

            $search_string = [
                '[login_link]', '[username]', '[password]'
            ];
            $replace_string = [
                $loginLink, $user->email, $password
            ];
            $message = str_replace($search_string, $replace_string, $course->email);

            $email_data['email_message'] = $message;
            $email_data['email_subject'] = $course->title;
            $toEmail = $user->email;

            //\Mail::to($toEmail)->queue(new FreeCourseNewUserEmail($email_data));
            AdminHelpers::send_email($email_data['email_subject'],
                $email_data['from_email'], $toEmail, $email_data['email_message']);
            CronLog::create(['activity' => 'FreeCourseDelayedEmailCommand sent email to user '.$user->id]);
            $delayedEmail->delete(); //delete the record after adding it to queue
        }
    }
}
