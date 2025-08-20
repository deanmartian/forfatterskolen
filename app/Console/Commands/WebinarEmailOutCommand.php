<?php

namespace App\Console\Commands;

use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\WebinarEmailOut;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WebinarEmailOutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webinaremailout:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email to users with webinar link from course webinar.';

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
        $today = Carbon::today();
        $emailOutList = WebinarEmailOut::whereDate('send_date', $today)->get();

        /*$access_token = '';
        if ($emailOutList->count()) {
            $access_token = AdminHelpers::generateWebinarGTAccessToken();
        }*/
        CronLog::create(['activity' => 'WebinarEmailOutCommand CRON running.']);

        foreach ($emailOutList as $emailOut) {
            $course_id = $emailOut->course_id;
            $webinar = $emailOut->webinar;

            // get courses taken that is active
            $coursesTaken = CoursesTaken::where(function ($query) use ($course_id) {
                $query->whereIn('package_id', Course::find($course_id)->packages()->pluck('id'));
            })
                ->where(function ($query) {
                    $query->where('end_date', '>=', Carbon::now())
                        ->orWhereNull('end_date');
                })
                ->where('can_receive_email', 1)
                ->get();

            $webinarDetails = AdminHelpers::getBigMarkerDetails($webinar->link);
            $presenterList = AdminHelpers::getBigMarkerPanelist($webinarDetails->conference->presenters);
            $startDate = AdminHelpers::convertTZNoFixedTZFormat($webinarDetails->conference->start_time, 'Europe/Madrid')->format('d, M Y');
            $startTime = AdminHelpers::convertTZNoFixedTZFormat($webinarDetails->conference->start_time, 'Europe/Madrid')->format('H:i');
            $webinarDate = $startDate.' klokken '.$startTime;
            $subject = $emailOut->subject; // "Webinar ".$webinarDate." med ".$presenterList;
            foreach ($coursesTaken as $courseTaken) {
                $user_email = $courseTaken->user->email;
                $register_link = "<a href='".route('front.goto-webinar.registration.email',
                    [encrypt($webinar->link), encrypt($user_email)])."'>Registrer meg</a>";

                $extractLink = FrontendHelpers::getTextBetween($emailOut->message, '[redirect]',
                    '[/redirect]');
                $redirectLabel = FrontendHelpers::getTextBetween($emailOut->message, '[redirect_label]',
                    '[/redirect_label]');
                $encode_email = encrypt($user_email);
                $formatRedirectLink = route('auth.login.emailRedirect', [$encode_email, encrypt($extractLink)]);
                $redirectLink = "<a href='".$formatRedirectLink."'>".$redirectLabel.'</a>';
                $search_string = [
                    '[redirect]'.$extractLink.'[/redirect]', '[redirect_label]'.$redirectLabel.'[/redirect_label]',
                    '[register_link]',
                ];
                $replace_string = [
                    $redirectLink, '', $register_link,
                ];
                $message = str_replace($search_string, $replace_string, $emailOut->message);

                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $message;
                $emailData['from_name'] = null;
                $emailData['from_email'] = null;
                $emailData['attach_file'] = null;

                // add email to queue
                if (!$courseTaken->user->is_disabled) {
                    // \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
                    dispatch(new AddMailToQueueJob($user_email, $subject, $message, 'postmail@forfatterskolen.no',
                        null, null, 'courses-taken', $courseTaken->id));
                    CronLog::create(['activity' => 'WebinarEmailOutCommand CRON send email to '.$user_email]);
                }
            }
        }
        CronLog::create(['activity' => 'WebinarEmailOutCommand CRON done running.']);

        /*foreach($emailOutList as $emailOut) {
            $course_id = $emailOut->course_id;
            $webinar = $emailOut->webinar;

            // get courses taken that is active
            $coursesTaken = CoursesTaken::where(function ($query) use ($course_id) {
                $query->whereIn('package_id', Course::find($course_id)->packages()->pluck('id'));
                })
                ->where(function($query) {
                    $query->where('end_date','>=', Carbon::now())
                        ->orWhereNull('end_date');
                })
                ->get();

            // check if the link is gotowebinar
            if (strpos($webinar->link,'attendee.gotowebinar.com')) {
                $web_key = FrontendHelpers::extractWebinarKeyFromLink($webinar->link); // id of the webinar from gotowebinar
                $webinarDetails = AdminHelpers::getGotoWebinarDetails($web_key, $access_token);

                // check if webinar don't have error or is valid webinar
                if (isset($webinarDetails->webinarKey)) {
                    $presenterList = AdminHelpers::getGotoWebinarPanelist($web_key, $access_token);
                    $times          = $webinarDetails->times[0];
                    $timezone       = $webinarDetails->timeZone;
                    $startDate      = AdminHelpers::convertTZNoFormat($times->startTime, $timezone)->format('d, M Y');
                    $startTime      = AdminHelpers::convertTZNoFormat($times->startTime, $timezone)->format('H:i');
                    $endTime        = AdminHelpers::convertTZNoFormat($times->endTime, $timezone)->format('H:i');
                    $formattedTZ    = AdminHelpers::convertTZNoFormat($times->startTime, $timezone)->format('T');
                    $webinarDate    = $startDate.' klokken '.$startTime;

                    $subject = "Webinar ".$webinarDate." med ".$presenterList;

                    // loop courses taken to get the users that avail the course
                    // this pass the checking that the course is not expired
                    foreach ($coursesTaken as $courseTaken) {
                        $user_email = $courseTaken->user->email;
                        $register_link = "<a href='".route('front.goto-webinar.registration.email',
                                [encrypt($web_key), encrypt($user_email)])."'>Registrer meg</a>";

                        $emailData['email_subject'] = $subject;
                        $emailData['email_message'] = str_replace('[register_link]', $register_link, $emailOut->message);
                        $emailData['from_name'] = NULL;
                        $emailData['from_email'] = NULL;
                        $emailData['attach_file'] = NULL;

                        // add email to queue
                        \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
                        CronLog::create(['activity' => 'WebinarEmailOutCommand CRON send email to '.$user_email]);
                    }
                }
            }
        }*/
    }
}
