<?php

namespace App\Console\Commands;

use App\CronLog;
use App\GTWebinar;
use App\Http\AdminHelpers;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GoToWebinarReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gotowebinarreminderday:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the reminder for goto webinar that would be expired in an hour and tomorrow';

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
        CronLog::create(['activity' => 'GoToWebinarReminderDay CRON running.']);
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $tomHour = Carbon::now()->addDay(1)->format('Y-m-d H:00:00');
        $tomHourPlusOne = Carbon::parse($tomHour)->format('Y-m-d H:59:00');

        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::now()->format('Y-m-d H:00:00');
        $later = Carbon::parse($now)->format('Y-m-d H:59:00');

        $tomorrowWebinars = GTWebinar::whereDate('reminder_date', '=', $tomorrow)
            ->where('send_reminder', '=', 1)
            ->whereBetween('reminder_date', [$tomHour, $tomHourPlusOne])
            ->get();

        $todayWebinars = GTWebinar::whereDate('reminder_date', '=', $today)
            ->where('send_reminder', '=', 1)
            ->whereBetween('reminder_date', [$now, $later])
            ->get();

        // merge the collections
        $webinars = $tomorrowWebinars->merge($todayWebinars);

        $access_token = '';
        if ($webinars->count()) {
            $access_token = AdminHelpers::generateWebinarGTAccessToken();
        }

        foreach ($webinars as $webinar) {
            $base_url = 'https://api.getgo.com/G2W/rest/v2';
            $org_key = '5169031040578858252';
            $web_key = $webinar->gt_webinar_key; // id of the webinar from gotowebinar

            $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$web_key.'/registrants';
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
            $attendants = json_decode(preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', $response));

            $gtWebinar = AdminHelpers::getGotoWebinarDetails($web_key, $access_token); // get webinar details

            $subject = $gtWebinar->subject;
            $organizerEmail = 'postmail@forfatterskolen.no';
            $times = $gtWebinar->times[0];
            $startTime = $times->startTime;
            $endTime = $times->endTime;
            $formattedDate = AdminHelpers::convertTZNoFormat($startTime, $gtWebinar->timeZone)->format('D, M d, H:i').' - '
                .AdminHelpers::convertTZNoFormat($endTime, $gtWebinar->timeZone)->format('H:i');

            if (count($gtWebinar->times) > 1) {
                $formattedDate = Carbon::parse($webinar->webinar_date)->format('D, M d, H:i').' - '
                    .Carbon::parse($webinar->webinar_date)->addHour(1)->format('H:i');
            }

            // loop the attendants for the webinar
            foreach ($attendants as $attendee) {
                if ($attendee->status == 'APPROVED') {
                    $joinURL = $attendee->joinUrl;
                    $explodeJoinURL = explode('/', $joinURL);
                    $user_id = end($explodeJoinURL);
                    $user_email = $attendee->email;

                    $calendar_link = 'https://global.gotowebinar.com/icsCalendar.tmpl?webinar='
                        .$gtWebinar->webinarKey.'&user='.$user_id;
                    $outlook_calendar = "<a href='".$calendar_link."&cal=outlook' style='text-decoration: none'>Outlook<sup>®</sup> Calendar</a>";
                    $google_calendar = "<a href='".$calendar_link."&cal=google' style='text-decoration: none'>Google Calendar™</a>";
                    $i_calendar = "<a href='".$calendar_link."&cal=ical' style='text-decoration: none'>iCal<sup>®</sup></a>";

                    $admin_email = "<a href='mailto:".$organizerEmail."' style='text-decoration: none'>"
                        .$organizerEmail.'</a>';

                    $join_button = "<p style='margin-left: 170px'><a href='".$joinURL."' style='font-size:16px;font-family:Helvetica,Arial,sans-serif;color:#ffffff;
text-decoration:none;border-radius:3px;padding:12px 18px;border:1px solid #114c7f;display:inline-block;background-color:#114c7f'>Bli med på webinar</a></p>";
                    $system_req = "<a href='https://link.gotowebinar.com/email-welcome?role=attendee&source=registrationConfirmationEmail
&language=english&experienceType=CLASSIC' style='text-decoration: none'>Test ditt system før webinaret</a>";

                    // add dash after every 3rd character
                    $webinarID = implode('-', str_split($gtWebinar->webinarID, 3));
                    $cancel_reg = "<a href='https://attendee.gotowebinar.com/cancel/".$web_key.'/'
                        .$attendee->registrantKey."' style='text-decoration: none'>kanselere registreringen</a>";

                    $search_string = [
                        '[first_name]', '[webinar_title]', '[admin_email]', '[webinar_date]', '[outlook_calendar]',
                        '[google_calendar]', '[i_cal]', '[join_button]', '[check_system_requirements]', '[webinar_id]',
                        '[cancel_registration]',
                    ];
                    $replace_string = [
                        $attendee->firstName, $subject, $admin_email, $formattedDate, $outlook_calendar,
                        $google_calendar, $i_calendar, $join_button, $system_req, $webinarID, $cancel_reg,
                    ];

                    $message = str_replace($search_string, $replace_string, $webinar->reminder_email);

                    // add email to queue
                    $emailData['email_subject'] = $subject;
                    $emailData['email_message'] = $message;
                    $emailData['from_name'] = null;
                    $emailData['from_email'] = null;
                    $emailData['attach_file'] = null;

                    \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
                    /*AdminHelpers::send_email($emailData['email_subject'],
                        'postmail@forfatterskolen.no', $user_email, $emailData['email_message']);*/
                    CronLog::create(['activity' => 'GoToWebinarReminder CRON send email to '.$user_email]);
                }
            }
        }
        CronLog::create(['activity' => 'GoToWebinarReminderDay CRON done running.']);
    }
}
