<?php

namespace App\Jobs;

use App\UserAutoRegisterToCourseWebinar;
use App\WebinarRegistrant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WebinarScheduleRegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // webinarScheduleWebinar model
    protected $schedule;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $schedule = $this->schedule;
        $webinar = $schedule->webinar;
        $isWebinarPakke = false;

        if ($webinar->course->isWebinarPakke) {
            $learners = UserAutoRegisterToCourseWebinar::where('course_id', $schedule->webinar->course->id)
                ->get();
            $isWebinarPakke = true;
        } else {
            $learners = $webinar->course->learners->get();
        }

        $header[] = 'API-KEY: '.config('services.big_marker.api_key');

        foreach ( $learners as $learner ) {
            $user = $learner->user;

            if ($user && !$isWebinarPakke || ($user && $isWebinarPakke && $user->coursesTakenNotOld2->count() > 0)) {
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
                }
            }
        }
    }
}
