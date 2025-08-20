<?php

namespace App\Console\Commands;

use App\CourseExpiryReminder;
use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\UserRenewedCourse;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class CourseExpirationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courseexpirationreminder:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check courses taken that would be expired in 28, 7 or 1 day and not auto renew courses.';

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
        CronLog::create(['activity' => 'CourseExpirationReminder CRON running.']);
        $days_28 = Carbon::now()->addDays(28)->format('Y-m-d');
        $days_7 = Carbon::now()->addDays(7)->format('Y-m-d');
        $tomorrow = Carbon::now()->addDays(1)->format('Y-m-d');

        // get courses taken by end date
        $coursesTaken = CoursesTaken::whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })->whereHas('user', function ($query) {
            $query->where('auto_renew_courses', 0);
        })
            ->whereNotNull('end_date')
            ->where('send_expiry_reminder', 1)
            ->where(function ($query) use ($days_28, $days_7, $tomorrow) {
                $query->whereIn('end_date', [$days_28, $days_7, $tomorrow]);
            })->get();

        // get courses taken by started at field
        $coursesTakenByStartDate = CoursesTaken::whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })->whereHas('user', function ($query) {
            $query->where('auto_renew_courses', 0);
        })
            ->whereNotNull('started_at')
            ->whereNull('end_date')
            ->where('send_expiry_reminder', 1)
            ->where(function ($query) use ($days_28, $days_7, $tomorrow) {
                // $query->whereDate('started_at', $tomorrow);
                $query->whereRaw(DB::raw("DATE(started_at) = '".$days_28."' 
            OR DATE(started_at) = '".$days_7."' OR DATE(started_at) = '".$tomorrow."'"));
            })
            ->get();

        // merge the collections
        $coursesTaken = $coursesTaken->merge($coursesTakenByStartDate);
        foreach ($coursesTaken->all() as $courseTaken) {
            $userRenewedCourse = UserRenewedCourse::where([
                'user_id' => $courseTaken->user_id,
                'course_id' => $courseTaken->package->course->id])
                ->first();

            // if ($userRenewedCourse) {

            $user_email = $courseTaken->user->email;
            $user_name = $courseTaken->user->first_name;

            $expires_in = Carbon::now()->diffInDays(Carbon::parse($courseTaken->started_at), false);

            if ($courseTaken->end_date) {
                $expires_in = Carbon::now()->diffInDays(Carbon::parse($courseTaken->end_date), false);
            }

            $expires_in = (int) ($expires_in + 1);

            $subject = '';
            $content = '';

            $course = $courseTaken->package->course;
            $expiryReminder = CourseExpiryReminder::where('course_id', $course->id)->first();

            switch ($expires_in) {
                case 28:
                    $subject = $expiryReminder->subject_28_days;
                    $content = $expiryReminder->message_28_days;
                    break;

                case 7:
                    $subject = $expiryReminder->subject_1_week;
                    $content = $expiryReminder->message_1_week;
                    break;

                case 1:
                    $subject = $expiryReminder->subject_1_day;
                    $content = $expiryReminder->message_1_day;
            }

            $from = 'postmail@forfatterskolen.no';
            $encode_email = encrypt($user_email);
            $extractLink = FrontendHelpers::getTextBetween($content, '[redirect]', '[/redirect]');
            $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
            $redirectLabel = FrontendHelpers::getTextBetween($content, '[redirect_label]', '[/redirect_label]');
            $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
            $search_string = [
                '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
            ];
            $replace_string = [
                $redirectLink, '',
            ];
            $content = str_replace($search_string, $replace_string, $content);

            $encode_email = encrypt($user_email);
            $loginLink = "<a href='".route('auth.login.email', $encode_email)
                ."?redirect=upgrade'>Ja, jeg vil være med ett år til?</a>";
            $message = str_replace('[login_link]', $loginLink, $content);

            $emailData = [
                'email_subject' => $subject,
                'email_message' => $message,
                'from_name' => null,
                'from_email' => $from,
                'attach_file' => null,
            ];

            if(!$courseTaken->user->is_disabled) {
                // \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
                dispatch(new AddMailToQueueJob($user_email, $subject, $message, $from, null, null,
                    'courses-taken', $courseTaken->id));

                // AdminHelpers::send_email($subject, $from, $user_email, $message);
                CronLog::create(['activity' => 'CourseExpirationReminder CRON sent email to '.$user_name.'.']);
            }
            
            // }
        }

        CronLog::create(['activity' => 'CourseExpirationReminder CRON done running.']);
    }
}
