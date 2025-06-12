<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\FormerCourse;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkexpiredcourse:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check courses that expires more than 2 months.';

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
        CronLog::create(['activity' => 'CheckExpiredCourse CRON running.']);
        $date2monthsAgo = Carbon::today()->subMonth(2)->format('Y-m-d');
        $expiredCoursesTaken = CoursesTaken::whereDate('end_date', '<=', $date2monthsAgo)
            ->orWhereNull('end_date')->get();

        foreach ($expiredCoursesTaken as $courseTaken) {
            if ($courseTaken->end_date) {
                $formerCourse['user_id'] = $courseTaken->user_id;
                $formerCourse['package_id'] = $courseTaken->package_id;
                $formerCourse['is_active'] = $courseTaken->is_active;
                $formerCourse['started_at'] = $courseTaken->started_at_value;
                $formerCourse['start_date'] = $courseTaken->start_date_value;
                $formerCourse['end_date'] = $courseTaken->end_date ? $courseTaken->end_date_value :
                    Carbon::parse($courseTaken->started_at_value)->addYear(1)->format('Y-m-d');
                $formerCourse['access_lessons'] = json_encode($courseTaken->access_lessons);
                $formerCourse['years'] = $courseTaken->years;
                $formerCourse['sent_renew_email'] = $courseTaken->sent_renew_email;
                $formerCourse['is_free'] = $courseTaken->is_free;
                $formerCourse['created_at'] = $courseTaken->created_at_value;
                $formerCourse['updated_at'] = $courseTaken->updated_at;

                FormerCourse::create($formerCourse);
                CronLog::create(['activity' => 'CheckExpiredCourse CRON added course taken #'
                    .$courseTaken->id.' as former course.']);

                $courseTaken->delete(); // delete course taken after inserted on the former course
            } else {
                $end_date = Carbon::parse($courseTaken->started_at)->addYear(1)->format('Y-m-d');
                if (Carbon::parse($date2monthsAgo)->gte($end_date)) {
                    $formerCourse['user_id'] = $courseTaken->user_id;
                    $formerCourse['package_id'] = $courseTaken->package_id;
                    $formerCourse['is_active'] = $courseTaken->is_active;
                    $formerCourse['started_at'] = $courseTaken->started_at_value;
                    $formerCourse['start_date'] = $courseTaken->start_date_value;
                    $formerCourse['end_date'] = $courseTaken->end_date ? $courseTaken->end_date_value :
                        Carbon::parse($courseTaken->started_at_value)->addYear(1)->format('Y-m-d');
                    $formerCourse['access_lessons'] = json_encode($courseTaken->access_lessons);
                    $formerCourse['years'] = $courseTaken->years;
                    $formerCourse['sent_renew_email'] = $courseTaken->sent_renew_email;
                    $formerCourse['is_free'] = $courseTaken->is_free;
                    $formerCourse['created_at'] = $courseTaken->created_at_value;
                    $formerCourse['updated_at'] = $courseTaken->updated_at;

                    FormerCourse::create($formerCourse);
                    CronLog::create(['activity' => 'CheckExpiredCourse CRON added course taken #'
                        .$courseTaken->id.' as former course.']);

                    $courseTaken->delete(); // delete course taken after inserted on the former course
                }
            }
        }
        CronLog::create(['activity' => 'CheckExpiredCourse CRON done running.']);
    }
}
