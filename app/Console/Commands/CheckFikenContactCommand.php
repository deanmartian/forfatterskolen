<?php

namespace App\Console\Commands;

use App\Address;
use App\User;
use Illuminate\Console\Command;

class CheckFikenContactCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkfikencontaact:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the contact id from fiken for users';

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
        $users = User::whereNull('fiken_contact_id')->get();
        $company = 'forfatterskolen-as';

        foreach($users as $user){
            $fikenUrl = "https://api.fiken.no/api/v2/companies/" . $company . "/contacts?email=" . $user->email;
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer '.config('services.fiken.personal_api_key'),
                'Content-Type: Application/json'
            ];

            $ch = curl_init($fikenUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);
            $fikenContacts = json_decode($body);

            $user->fill([
                'fiken_contact_id' => $fikenContacts[0]->contactId
            ])->save();

            break;
        }

        echo "Done";
    }
}
