<?php

namespace App\Jobs;

use App\UserAutoRegisterToCourseWebinar;
use App\WebinarRegistrant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
     */
    public function handle(): void
    {
        $schedule = $this->schedule;
        $webinar = $schedule->webinar;
        $isWebinarPakke = false;

        if ($webinar->course->isWebinarPakke) {
            $learners = UserAutoRegisterToCourseWebinar::where('course_id', $schedule->webinar->course->id)
                ->get();
            $isWebinarPakke = true;
        } else {
            $learners = $webinar->course->webinarLearners->get();
        }

        $header[] = 'API-KEY: '.config('services.big_marker.api_key');

        Log::info('----------- inside webinar schedule registration job ----------------');
        foreach ($learners as $learner) {
            $user = $learner->user;

            if ($user && ! $isWebinarPakke || ($user && $isWebinarPakke && $user->coursesTakenNotOld2->count() > 0)) {
                $data = [
                    'id' => $webinar->link,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                ];
                $ch = curl_init();
                $url = config('services.big_marker.register_link');

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                $response = curl_exec($ch);
                $decoded_response = json_decode($response);

                Log::info(json_encode($decoded_response));
                if (property_exists($decoded_response, 'conference_url')) {
                    $registrant['user_id'] = $user->id;
                    $registrant['webinar_id'] = $webinar->id;
                    $webRegister = WebinarRegistrant::firstOrNew($registrant);
                    $webRegister->join_url = $decoded_response->conference_url;
                    $webRegister->save();
                }
            }
        }

        Log::info('----------- after foreach webinar schedule registration job ----------------');
    }
}
