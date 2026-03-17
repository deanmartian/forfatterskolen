<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\Mail\WeeklyDigestMail;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    protected $signature = 'emails:weekly-digest';

    protected $description = 'Send weekly digest email to all active course learners';

    public function handle()
    {
        CronLog::create(['activity' => 'WeeklyDigest CRON running.']);

        $now = Carbon::now();

        // Get all unique user IDs with active CoursesTaken
        $userIds = CoursesTaken::where('can_receive_email', 1)
            ->where(fn($q) => $q->where('end_date', '>=', $now->format('Y-m-d'))->orWhereNull('end_date'))
            ->pluck('user_id')
            ->unique();

        $sent = 0;
        $skipped = 0;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user || ($user->is_disabled ?? false)) {
                $skipped++;
                continue;
            }

            $mailable = new WeeklyDigestMail($user);
            $data = $mailable->buildDigestData();

            if (empty($data)) {
                $skipped++;
                continue;
            }

            Mail::to($user->email)->queue($mailable);
            $sent++;

            CronLog::create(['activity' => 'WeeklyDigest sent to ' . $user->email]);
        }

        CronLog::create(['activity' => "WeeklyDigest CRON done. Sent: {$sent}, Skipped: {$skipped}."]);

        $this->info("Weekly digest sent to {$sent} users, skipped {$skipped}.");

        return 'done';
    }
}
