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
        $emailOutList = EmailOut::all();
        foreach($emailOutList as $emailOut) {

            if (FrontendHelpers::isDate($emailOut->delay)) {
                $emailDate = $emailOut->delay;
            } else {
                $emailDate = Carbon::now()->subDays($emailOut->delay)->format('Y-m-d');
            }

            $package = $emailOut->course->packages->first();
            $coursesTaken = CoursesTaken::where('package_id', $package->id)
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
