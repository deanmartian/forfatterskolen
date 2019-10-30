<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Mail\SubjectBodyEmail;
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
     *
     * @return mixed
     */
    public function handle()
    {
        CronLog::create(['activity' => 'DontAvailAnything CRON running.']);
        $yesterday = date("Y-m-d", strtotime( '-1 days' ) ); // get the date yesterday
        $users = User::whereDate('created_at', $yesterday )->get(); // get users created yesterday
        foreach($users as $user) {
            // check if the user don't have workshop, manuscript and courses taken
            if ($user->workshopsTaken->count() == 0 && $user->shopManuscriptsTaken->count() == 0 && count($user->coursesTaken) == 0) {
                $from     = 'postmail@forfatterskolen.no';
                $headers = "From: Forfatterskolen<".$from.">\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $subject = "Vi ser at du har registrert deg på Forfatterskolen";
                $message = "Hei, <br/><br/> Vi ser at du har registrert deg på nettsiden vår (www.forfatterskolen.no). Ønsker du et kurs, 
                eller en annen tjeneste, og trenger vår hjelp? Gi i så fall en lyd, så vil vi gi deg den assistansen du trenger. <br/><br/>
                Vi ønsker deg en god dag! <br/><br/> Hilsen oss i Forfatterskolen";

                $to = $user->email;
                $emailData = [
                    'email_subject' => $subject,
                    'email_message' => $message,
                    'from_name'     => NULL,
                    'from_email'    => $from,
                    'attach_file'   => NULL
                ];
                \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

                //mail($user->email, $subject, $message, $headers);
                CronLog::create(['activity' => 'DontAvailAnything CRON sent email to '.$user->email]);
            }
        }

        CronLog::create(['activity' => 'DontAvailAnything CRON done running.']);
    }
}
