<?php

namespace App\Console\Commands;

use App\CourseExpiryReminder;
use App\CoursesTaken;
use App\CronLog;
use App\Http\AdminHelpers;
use App\UserRenewedCourse;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class CourseExpirationReminder extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courseexpirationreminder:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check courses taken that would be expired in 28, 7 or 1 day and not auto renew courses.';

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
        CronLog::create(['activity' => 'CourseExpirationReminder CRON running.']);
        $days_28 = Carbon::now()->addDays(28)->format('Y-m-d');
        $days_7 = Carbon::now()->addDays(7)->format('Y-m-d');
        $tomorrow = Carbon::now()->addDays(1)->format('Y-m-d');

        // get courses taken by end date
        $coursesTaken = CoursesTaken::whereHas('package', function($query){
            $query->where('course_id', 17);
        })->whereHas('user', function($query){
            $query->where('auto_renew_courses',0);
        })
        ->whereNotNull('end_date')
        ->where(function($query) use ($days_28, $days_7, $tomorrow){
            $query->whereIn('end_date',[$days_28, $days_7, $tomorrow]);
        })->get();

        // get courses taken by started at field
        $coursesTakenByStartDate = CoursesTaken::whereHas('package', function($query){
            $query->where('course_id', 17);
        })->whereHas('user', function($query){
            $query->where('auto_renew_courses',0);
        })
        ->whereNotNull('started_at')
        ->whereNull('end_date')
        ->where(function($query) use ($days_28, $days_7, $tomorrow){
            //$query->whereDate('started_at', $tomorrow);
            $query->whereRaw(DB::raw("DATE(started_at) = '".$days_28."' 
            OR DATE(started_at) = '".$days_7."' OR DATE(started_at) = '".$tomorrow."'"));
        })
        ->get();

        // merge the collections
        $coursesTaken = $coursesTaken->merge($coursesTakenByStartDate);
        foreach ($coursesTaken->all() as $courseTaken) {
            $userRenewedCourse = UserRenewedCourse::where([
                'user_id' => $courseTaken->user_id,
                'course_id' => $courseTaken->package->course->id])
                ->first();

            if ($userRenewedCourse) {

                $user_email = $courseTaken->user->email;
                $user_name  = $courseTaken->user->first_name;

                $expires_in = Carbon::parse($courseTaken->started_at)->diffInDays(Carbon::now());

                if ($courseTaken->end_date) {
                    $expires_in = Carbon::parse($courseTaken->end_date)->diffInDays(Carbon::now());
                }

                $expires_in = $expires_in+1;
                $day_text = $expires_in > 1 ? 'days':'day';
                $course = $courseTaken->package->course;
                $expiryReminder = CourseExpiryReminder::where('course_id', $course->id)->first();

                $subject = 'Course '.$course->title.' expires in '.$expires_in.' '.$day_text;
                $from = 'post@forfatterskolen.no';
                $content = $expiryReminder->message;

                AdminHelpers::send_email($subject, $from, $user_email, $content);
                CronLog::create(['activity' => 'CourseExpirationReminder CRON sent email to '.$user_name.'.']);
            }
        }

        CronLog::create(['activity' => 'CourseExpirationReminder CRON done running.']);
    }
}