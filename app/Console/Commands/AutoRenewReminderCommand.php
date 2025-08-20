<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoRenewReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autorenewreminder:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto renew course reminder.';

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

        CronLog::create(['activity' => 'AutoRenewReminder CRON running.']);
        $dateAddDays = Carbon::now()->addDays(17);
        $monthDate = $dateAddDays->format('Y-m-d');
        $yearDate = $dateAddDays->subYear(1)->format('Y-m-d'); // subYear to get the correct started_at

        // get courses taken by end date
        $coursesTaken = CoursesTaken::whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })->whereNotNull('end_date')->where('end_date', $monthDate)->get();

        // get courses taken by started at field
        $coursesTakenByStartDate = CoursesTaken::whereHas('package', function ($query) {
            $query->where('course_id', 17);
        })
            ->whereNotNull('started_at')
            ->whereNull('end_date')
            ->whereDate('started_at', $yearDate)
            ->get();

        // merge the collections
        $coursesTaken = $coursesTaken->merge($coursesTakenByStartDate);

        foreach ($coursesTaken->all() as $courseTaken) {

            // check if auto renew courses is set
            if ($courseTaken->user->auto_renew_courses) {
                $to = $courseTaken->user->email;
                $emailTemplate = AdminHelpers::emailTemplate('Auto Renew Reminder');

                $emailData['email_subject'] = $emailTemplate->subject;
                $emailData['email_message'] = $emailTemplate->email_content;
                $emailData['from_name'] = null;
                $emailData['from_email'] = $emailTemplate->from_email;
                $emailData['attach_file'] = null;

                // \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                if(!$courseTaken->user->is_disabled) {
                    dispatch(new AddMailToQueueJob($to, $emailTemplate->subject, $emailTemplate->email_content,
                        $emailTemplate->from_email, null, null,
                        'courses-taken', $courseTaken->id));

                    CronLog::create(['activity' => 'AutoRenewReminder CRON sent email to '.$to]);
                }

            }
        }

        CronLog::create(['activity' => 'AutoRenewReminder CRON done running.']);

    }
}
