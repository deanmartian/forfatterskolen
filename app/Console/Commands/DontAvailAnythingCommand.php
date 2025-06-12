<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Jobs\AddMailToQueueJob;
use App\User;
use Illuminate\Console\Command;

class DontAvailAnythingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dontavailanything:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for learners that don\'t avail anything and send email';

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
        CronLog::create(['activity' => 'DontAvailAnything CRON running.']);
        $yesterday = date('Y-m-d', strtotime('-1 days')); // get the date yesterday
        $users = User::whereDate('created_at', $yesterday)
            ->where('role', 2)->get(); // get users created yesterday
        foreach ($users as $user) {
            // check if the user don't have workshop, manuscript and courses taken
            if ($user->workshopsTaken->count() == 0 && $user->shopManuscriptsTaken->count() == 0 && count($user->coursesTaken) == 0
            && $user->comeptitionApplication->count() === 0 && $user->giftPurchases->count() === 0) {

                $to = $user->email;

                $emailTemplate = AdminHelpers::emailTemplate('Do not avail anything');
                $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $to,
                    $user->first_name, '');
                dispatch(new AddMailToQueueJob($to, $emailTemplate->subject, $emailContent,
                    $emailTemplate->from_email, '', null, 'learner', $user->id));

                CronLog::create(['activity' => 'DontAvailAnything CRON sent email to '.$user->email]);
            }
        }

        CronLog::create(['activity' => 'DontAvailAnything CRON done running.']);
    }
}
