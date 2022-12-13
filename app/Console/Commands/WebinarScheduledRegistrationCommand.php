<?php

namespace App\Console\Commands;

use App\CronLog;
use App\UserAutoRegisterToCourseWebinar;
use App\WebinarRegistrant;
use App\WebinarScheduledRegistration;
use Carbon\Carbon;
use Illuminate\Console\Command;

class WebinarScheduledRegistrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webinarscheduledregistration:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled registration for webinars';

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
        CronLog::create(['activity' => 'WebinarScheduledRegistration CRON running.']);

        $today = Carbon::today()->format('Y-m-d');
        $schedules = WebinarScheduledRegistration::with('webinar')->whereDate('date', $today)->get();

        $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        $counter = 1;

        foreach ( $schedules as $schedule ) {

            $webinar = $schedule->webinar;

            if ($webinar->course->isWebinarPakke) {
                $learners = UserAutoRegisterToCourseWebinar::where('course_id', $schedule->webinar->course->id)
                    ->get();
            } else {
                $learners = $webinar->course->learners->get();
            }

            foreach ( $learners as $learner ) {
                $user = $learner->user;
                if ($user) {
                    $data = [
                        'id'            => $webinar->link,
                        'email'         => $user->email,
                        'first_name'    => $user->first_name,
                        'last_name'     => $user->last_name,
                    ];
                    $ch = curl_init();
                    $url = config('services.big_marker.register_link');

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    $response = curl_exec($ch);
                    $decoded_response = json_decode($response);

                    if (array_key_exists('conference_url', $decoded_response)) {
                        $registrant['user_id'] = $user->id;
                        $registrant['webinar_id'] = $webinar->id;
                        $webRegister = WebinarRegistrant::firstOrNew($registrant);
                        $webRegister->join_url = $decoded_response->conference_url;
                        $webRegister->save();

                        CronLog::create(['activity' => 'WebinarScheduledRegistration added ' . $user->email .
                            ' to bigmarker webinar ' . $webinar->link . '.']);
                    }
                }
            }

        }


        CronLog::create(['activity' => 'WebinarScheduledRegistration CRON done running.']);
    }
}
