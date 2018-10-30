<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\EmailOut;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CourseEmailOut extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courseemailout:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Course email out';

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
        $today = Carbon::today()->format('Y-m-d');
        $emailOutList = EmailOut::whereDate('delay', '=', $today)->get();
        foreach($emailOutList as $emailOut) {
            $packages = $emailOut->course->packages->pluck('id')->toArray();
            $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                ->get();

            // loop the result and send email
            foreach ($coursesTaken as $courseTaken) {
                $toMail = $courseTaken->user->email;
                $emailData['email_subject'] = $emailOut->subject;
                $emailData['email_message'] = $emailOut->message;

                // add email to queue
                \Mail::to($toMail)->queue(new SubjectBodyEmail($emailData));
            }
        }

        $emailOutListDay = EmailOut::where('delay', 'NOT LIKE', '%-%')->get();
        foreach ($emailOutListDay as $emailOut) {
            $emailDate = Carbon::now()->subDays($emailOut->delay)->format('Y-m-d');
            $packages = $emailOut->course->packages->pluck('id')->toArray();
            $coursesTaken = CoursesTaken::whereIn('package_id', $packages)
                ->where(function($query) use ($emailDate) {
                    $query->whereDate('started_at', '=', $emailDate);
                    $query->orWhereDate('start_date', '=', $emailDate);
                })
                ->get();

            // loop the result and send email
            foreach ($coursesTaken as $courseTaken) {
                $toMail = $courseTaken->user->email;
                $emailData['email_subject'] = $emailOut->subject;
                $emailData['email_message'] = $emailOut->message;

                // add email to queue
                \Mail::to($toMail)->queue(new SubjectBodyEmail($emailData));
            }
        }
    }
}
