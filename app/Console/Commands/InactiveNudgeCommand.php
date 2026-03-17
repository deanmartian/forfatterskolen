<?php

namespace App\Console\Commands;

use App\CoursesTaken;
use App\CronLog;
use App\EmailHistory;
use App\LearnerLogin;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class InactiveNudgeCommand extends Command
{
    protected $signature = 'emails:inactive-nudge';

    protected $description = 'Send nudge email to active learners who have not logged in for 14+ days';

    protected static $quotes = [
        ['text' => 'Skriv det du vil lese.', 'author' => 'Toni Morrison'],
        ['text' => 'Det finnes ingen regler. Det er slik det er mulig.', 'author' => 'Virginia Woolf'],
        ['text' => 'Du trenger ikke se hele trappen, bare ta det første steget.', 'author' => 'Martin Luther King Jr.'],
        ['text' => 'Start der du er. Bruk det du har. Gjør det du kan.', 'author' => 'Arthur Ashe'],
        ['text' => 'En forfatter er en som skriver.', 'author' => 'Anne Enright'],
        ['text' => 'Skriv hardt og klart om det som gjør vondt.', 'author' => 'Ernest Hemingway'],
        ['text' => 'Du kan alltid redigere en dårlig side. Du kan ikke redigere en blank side.', 'author' => 'Jodi Picoult'],
        ['text' => 'Inspirasjonen finnes, men den må finne deg i arbeid.', 'author' => 'Pablo Picasso'],
    ];

    public function handle()
    {
        CronLog::create(['activity' => 'InactiveNudge CRON running.']);

        $now = Carbon::now();
        $cutoff = $now->copy()->subDays(14);
        $nudgeCooldown = $now->copy()->subDays(30);

        // Get all unique user IDs with active CoursesTaken
        $activeUserIds = CoursesTaken::where('can_receive_email', 1)
            ->where(fn($q) => $q->where('end_date', '>=', $now->format('Y-m-d'))->orWhereNull('end_date'))
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($activeUserIds->isEmpty()) {
            $this->info("No active users found.");
            return 'done';
        }

        // Bulk: get last login per user (only users who logged in before cutoff)
        $lastLogins = LearnerLogin::whereIn('user_id', $activeUserIds->toArray())
            ->selectRaw('user_id, MAX(created_at) as last_login')
            ->groupBy('user_id')
            ->havingRaw('MAX(created_at) < ?', [$cutoff->format('Y-m-d H:i:s')])
            ->pluck('last_login', 'user_id');

        $inactiveUserIds = $lastLogins->keys();

        if ($inactiveUserIds->isEmpty()) {
            CronLog::create(['activity' => 'InactiveNudge CRON done. No inactive users found.']);
            $this->info("No inactive users found.");
            return 'done';
        }

        // Bulk: get emails that already received nudge in last 30 days (raw query to avoid accessor issues)
        $recentNudgeEmails = \Illuminate\Support\Facades\DB::table('email_history')
            ->where('subject', 'LIKE', '%inactive nudge%')
            ->where('created_at', '>=', $nudgeCooldown->format('Y-m-d H:i:s'))
            ->whereNull('deleted_at')
            ->pluck('recipient')
            ->unique()
            ->flip();

        // Load users in one query
        $users = User::whereIn('id', $inactiveUserIds->toArray())->get()->keyBy('id');

        $sent = 0;
        $skipped = 0;
        $quote = self::$quotes[$now->dayOfYear % count(self::$quotes)];

        foreach ($inactiveUserIds as $userId) {
            $user = $users->get($userId);
            if (!$user || ($user->is_disabled ?? false)) {
                $skipped++;
                continue;
            }

            // Skip if already nudged recently
            if ($recentNudgeEmails->has($user->email)) {
                $skipped++;
                continue;
            }

            $subject = 'Vi savner deg, ' . $user->first_name . '!';

            // Send the nudge email
            Mail::send('emails.branded.inactive-nudge', [
                'firstName' => $user->first_name,
                'quote' => $quote,
                'portalUrl' => config('app.url') . '/learner/dashboard',
            ], function ($message) use ($user, $subject) {
                $message->from('post@forfatterskolen.no', 'Kristine S. Henningsen')
                    ->to($user->email)
                    ->subject($subject);
            });

            // Log to email_history for cooldown tracking (ASCII-safe subject to avoid latin1 collation issues)
            try {
                \Illuminate\Support\Facades\DB::table('email_history')->insert([
                    'subject' => 'Vi savner deg - inactive nudge',
                    'from_email' => 'post@forfatterskolen.no',
                    'message' => 'Inactive nudge',
                    'recipient' => $user->email,
                    'parent' => 'learner',
                    'parent_id' => $user->id,
                    'created_at' => $now->format('Y-m-d H:i:s'),
                    'updated_at' => $now->format('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                // Log error but don't stop the batch
            }

            $sent++;
        }

        CronLog::create(['activity' => "InactiveNudge CRON done. Sent: {$sent}, Skipped: {$skipped}."]);

        $this->info("Inactive nudge sent to {$sent} users, skipped {$skipped}.");

        return 'done';
    }
}
