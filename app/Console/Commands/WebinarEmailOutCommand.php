<?php

namespace App\Console\Commands;

use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
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
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today();
        $emailOutList = WebinarEmailOut::whereDate('send_date', $today)->get();

        CronLog::create(['activity' => 'WebinarEmailOutCommand CRON running.']);
        foreach($emailOutList as $emailOut) {
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
                $webinarDetails = AdminHelpers::getGotoWebinarDetails($web_key);

                // check if webinar don't have error or is valid webinar
                if (isset($webinarDetails->webinarKey)) {
                    $presenterList = AdminHelpers::getGotoWebinarPanelist($web_key);
                    $times          = $webinarDetails->times[0];
                    $timezone       = $webinarDetails->timeZone;
                    $startDate      = AdminHelpers::convertTZNoFormat($times->startTime, $timezone)->format('d, M Y');
                    $startTime      = AdminHelpers::convertTZNoFormat($times->startTime, $timezone)->format('H:i');
                    $endTime        = AdminHelpers::convertTZNoFormat($times->endTime, $timezone)->format('H:i');
                    $formattedTZ    = AdminHelpers::convertTZNoFormat($times->startTime, $timezone)->format('T');
                    $webinarDate    = $startDate.' klokken '.$startTime/*.' - '.$endTime.' '.$formattedTZ*/;

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
                        //\Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
                        AdminHelpers::send_email($subject,'post@forfatterskolen.no', $user_email,
                            str_replace('[register_link]', $register_link, $emailOut->message));
                        CronLog::create(['activity' => 'WebinarEmailOutCommand CRON send email to '.$user_email]);
                    }
                }
            }
        }
        CronLog::create(['activity' => 'WebinarEmailOutCommand CRON done running.']);
    }
}
