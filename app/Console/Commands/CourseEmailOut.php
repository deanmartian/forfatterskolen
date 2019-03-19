<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\EmailAttachment;
use App\EmailOut;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CourseEmailOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courseemailout:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Course email out';

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
        $today = Carbon::today()->format('Y-m-d');
        CronLog::create(['activity' => 'CourseEmailOut CRON running.']);
        $emailOutList = EmailOut::whereDate('delay', '=', $today)->get();
        foreach($emailOutList as $emailOut) {
            $packages = $emailOut->course->packages->pluck('id')->toArray();
            $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                ->get();

            $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
            $attachmentText = '';
            if ($emailAttachment) {
                $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                    .AdminHelpers::extractFileName($emailAttachment->filename)."</a></p>";
            }

            // loop the result and send email
            foreach ($coursesTaken as $courseTaken) {
                $toMail = $courseTaken->user->email;

                $encode_email = encrypt($courseTaken->user->email);
                $user = $courseTaken->user;
                $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

                $search_string = [
                    '[login_link]', '[username]', '[password]'
                ];
                $replace_string = [
                    $loginLink, $courseTaken->user->email, $password
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);

                $emailData['email_subject'] = $emailOut->subject;
                $emailData['email_message'] = $message.$attachmentText;
                $emailData['from_name'] = $emailOut->from_name;
                $emailData['from_email'] = $emailOut->from_email;
                $emailData['attach_file'] = NULL;

                CronLog::create(['activity' => 'CourseEmailOut adding to email queue '.$toMail]);
                // add email to queue
                \Mail::to($toMail)->queue(new SubjectBodyEmail($emailData));
                CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$toMail]);
            }
        }

        $emailOutListDay = EmailOut::where('delay', 'NOT LIKE', '%-%')->get();
        foreach ($emailOutListDay as $emailOut) {
            $emailDate = Carbon::now()->subDays($emailOut->delay)->format('Y-m-d');
            $packages = $emailOut->course->packages->pluck('id')->toArray();
            $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                ->where(function($query) use ($emailDate) {
                    $query->whereDate('started_at', '=', $emailDate);
                    $query->orWhereDate('start_date', '=', $emailDate);
                })
                ->get();

            $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
            $attachmentText = '';
            if ($emailAttachment) {
                $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                    .AdminHelpers::extractFileName($emailAttachment->filename)."</a></p>";
            }

            // loop the result and send email
            foreach ($coursesTaken as $courseTaken) {
                $toMail = $courseTaken->user->email;

                $encode_email = encrypt($courseTaken->user->email);
                $user = $courseTaken->user;
                $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
                $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';

                $search_string = [
                    '[login_link]', '[username]', '[password]'
                ];
                $replace_string = [
                    $loginLink, $courseTaken->user->email, $password
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);

                $emailData['email_subject'] = $emailOut->subject;
                $emailData['email_message'] = $message.$attachmentText;
                $emailData['from_name'] = $emailOut->from_name;
                $emailData['from_email'] = $emailOut->from_email;
                $emailData['attach_file'] = NULL;

                CronLog::create(['activity' => 'CourseEmailOut adding to email queue '.$toMail]);
                // add email to queue
                \Mail::to($toMail)->queue(new SubjectBodyEmail($emailData));
                CronLog::create(['activity' => 'CourseEmailOut added to email queue '.$toMail]);
            }
        }

        CronLog::create(['activity' => 'CourseEmailOut CRON done running.']);
    }
}
