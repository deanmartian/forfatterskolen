<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CourseExpiresInAMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courseexpiresinamonth:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for learners that have a webinar pakke course that would expire in a month.';

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
        CronLog::create(['activity' => 'CourseExpiresInAMonth CRON running.']);
        $monthDate = Carbon::now()->addDays(30)->format('Y-m-d');

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
            ->whereDate('started_at', $monthDate)
            ->get();

        // merge the collections
        $coursesTaken = $coursesTaken->merge($coursesTakenByStartDate);

        foreach ($coursesTaken->all() as $courseTaken) {

            $user_email = $courseTaken->user->email;
            $automation_id = 71;
            $user_name = $courseTaken->user->first_name;
            // add to automation
            // check if auto renew courses is not set
            if (! $courseTaken->user->auto_renew_courses && !$courseTaken->user->is_disabled) {
                AdminHelpers::addToAutomation($user_email, $automation_id, $user_name);
                CronLog::create(['activity' => 'CourseExpiresInAMonth CRON added '.$user_name.' to automation '.$automation_id]);
            }
        }

        CronLog::create(['activity' => 'CourseExpiresInAMonth CRON done running.']);
    }
}
