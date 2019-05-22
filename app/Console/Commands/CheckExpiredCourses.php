<?php
namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\FormerCourse;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredCourses extends Command {

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
     *
     * @return mixed
     */
    public function handle()
    {
        CronLog::create(['activity' => 'CheckExpiredCourse CRON running.']);
        $date2monthsAgo = Carbon::today()->subMonth(2)->format('Y-m-d');
        $expiredCoursesTaken = CoursesTaken::whereDate('end_date', '<=', $date2monthsAgo)
            ->orWhereNull('end_date')->get();

        foreach($expiredCoursesTaken as $courseTaken) {
            if ($courseTaken->end_date && $courseTaken->user_id === 889) {
                $formerCourse['user_id'] = $courseTaken->user_id;
                $formerCourse['package_id'] = $courseTaken->package_id;
                $formerCourse['date_ended'] = Carbon::parse($courseTaken->end_date)->format('Y-m-d');
                $formerCourse['course_created_at'] = $courseTaken->created_at_value;
                FormerCourse::create($formerCourse);

                CronLog::create(['activity' => 'CheckExpiredCourse CRON added course taken #'
                    .$courseTaken->id.' as former course.']);

                $courseTaken->delete(); // delete course taken after inserted on the former course
            } else {
                $end_date = Carbon::parse($courseTaken->started_at)->addYear(1)->format('Y-m-d');
                if (Carbon::parse($date2monthsAgo)->gte($end_date)) {
                    $formerCourse['user_id'] = $courseTaken->user_id;
                    $formerCourse['package_id'] = $courseTaken->package_id;
                    $formerCourse['date_ended'] = $end_date;
                    $formerCourse['course_created_at'] = $courseTaken->created_at_value;
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