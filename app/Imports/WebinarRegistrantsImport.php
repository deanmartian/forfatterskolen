<?php

namespace App\Imports;

use App\User;
use App\Webinar;
use App\WebinarRegistrant;
use Illuminate\Support\Collection;
use Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class WebinarRegistrantsImport implements ToCollection
{
    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function collection(Collection $rows)
    {
        Log::info('------------ link ---------------');
        Log::info($this->link);
        // Handle the imported data
        $count = 0;
        $webinar = Webinar::where('link', $this->link)->first();

        if ($webinar) {
            $registrant['webinar_id'] = $webinar->id;
            foreach ($rows as $row) {
                Log::info('---------------- row ------------------');
                $user = User::where('email', $row[2])->first();
                if ($user && isset($row[6])) {
                    $registrant['user_id'] = $user->id;

                    $webRegister = WebinarRegistrant::firstOrNew($registrant);
                    $webRegister->join_url = $row[6];
                    $webRegister->save();
                    $count++;
                }
            }
        }

        Log::info($count);
    }
}
