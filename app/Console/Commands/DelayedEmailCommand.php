<?php

namespace App\Console\Commands;

use App\CronLog;
use App\DelayedEmail;
use App\Jobs\AddMailToQueueJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DelayedEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delayedemail:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emails sent based on send_date field';

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
        CronLog::create(['activity' => 'DelayedEmailCommand CRON running.']);

        $today = Carbon::today()->format('Y-m-d');
        $delayedEmails = DelayedEmail::whereDate('send_date', $today)->get();
        foreach ($delayedEmails as $delayedEmail) {

            $to = $delayedEmail->recipient;
            $subject = $delayedEmail->subject;
            $message = $delayedEmail->message;
            $from_email = $delayedEmail->from_email;
            $from_name = $delayedEmail->from_name;
            $attachment = $delayedEmail->attachment;
            $parent = $delayedEmail->parent;
            $parent_id = $delayedEmail->parent_id;

            dispatch(new AddMailToQueueJob($to, $subject, $message, $from_email, $from_name, $attachment,
                $parent, $parent_id));

            CronLog::create(['activity' => 'DelayedEmailCommand CRON sent email to '.$to]);
        }

        CronLog::create(['activity' => 'DelayedEmailCommand CRON done running.']);
    }
}
