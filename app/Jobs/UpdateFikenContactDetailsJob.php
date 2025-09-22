<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateFikenContactDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $learner;

    protected $headers = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($learner)
    {
        $this->learner = $learner;
        $this->headers[] = 'Accept: application/json';
        $this->headers[] = 'Authorization: Bearer '.config('services.fiken.personal_api_key');
        $this->headers[] = 'Content-Type: Application/json';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $address = $this->learner->address;
        Log::info('------------------------- Update fiken contact details job here -------------------------');

        $fields = [
            'name' => $this->learner->full_name,
            'email' => $this->learner->email,
            'address' => [
                'streetAddress' => $address->street,
                'city' => $address->city,
                'postCode' => $address->zip,
                'country' => 'Norge',
            ],
        ];

        // add phone if present
        if (!empty($address->phone)) {
            $fields['phoneNumber'] = $address->phone;
        }

        $field_string = json_encode($fields, true);

        $fikenUrl = 'https://api.fiken.no/api/v2/companies/forfatterskolen-as/contacts/'.$this->learner->fiken_contact_id;
        $ch = curl_init($fikenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        Log::info('after request');
        Log::info($data);

        // get the http code response
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        Log::info('http code = '.$http_code);
        if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
            Log::info('error ================= '.$http_code);
        }
        curl_close($ch);
    }
}
