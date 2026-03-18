<?php

namespace App\Console\Commands;

use App\FreeWebinar;
use App\Mail\WebinarReminderEmail;
use App\WebinarRegistration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebinarReminderCommand extends Command
{
    protected $signature = 'webinar:send-reminders';
    protected $description = 'Send webinar påminnelser (dagen før kl 18 og 1 time før)';

    private $monthsNo = ['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember'];
    private $daysNo = ['søndag', 'mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag'];

    public function handle(): void
    {
        $now = Carbon::now();

        // Get all upcoming webinars
        $webinars = FreeWebinar::where('start_date', '>', $now)
            ->where('start_date', '<', $now->copy()->addDays(2))
            ->get();

        foreach ($webinars as $webinar) {
            $startDate = Carbon::parse($webinar->start_date);

            // Påminnelse dagen før kl 18:00
            $dayBeforeAt18 = $startDate->copy()->subDay()->setTime(18, 0);
            if ($now->between($dayBeforeAt18, $dayBeforeAt18->copy()->addMinutes(15))) {
                $this->sendReminders($webinar, 'reminder_day_before_sent', 'Webinaret er i morgen!');
            }

            // Påminnelse 1 time før
            $oneHourBefore = $startDate->copy()->subHour();
            if ($now->between($oneHourBefore, $oneHourBefore->copy()->addMinutes(15))) {
                $this->sendReminders($webinar, 'reminder_hour_before_sent', 'Webinaret starter om 1 time!');
            }
        }
    }

    private function sendReminders(FreeWebinar $webinar, string $sentField, string $reminderText): void
    {
        $registrations = WebinarRegistration::where('free_webinar_id', $webinar->id)
            ->where($sentField, false)
            ->get();

        $startDate = Carbon::parse($webinar->start_date);
        $sent = 0;

        foreach ($registrations as $reg) {
            try {
                $data = [
                    'webinarTitle' => $webinar->title,
                    'webinarDay' => $startDate->format('d'),
                    'webinarMonth' => $this->monthsNo[$startDate->month - 1],
                    'webinarTime' => $startDate->format('H:i'),
                    'webinarDayName' => $this->daysNo[$startDate->dayOfWeek],
                    'joinUrl' => $reg->join_url ?: '#',
                    'reminderText' => $reminderText,
                ];

                Mail::to($reg->email)->queue(new WebinarReminderEmail($data));

                $reg->update([$sentField => true]);
                $sent++;
            } catch (\Exception $e) {
                Log::error("Webinar påminnelse feilet for {$reg->email}: {$e->getMessage()}");
            }
        }

        if ($sent > 0) {
            $this->info("Sendt {$sent} «{$reminderText}» for {$webinar->title}");
            Log::info("Webinar påminnelse: {$sent} e-poster sendt for {$webinar->title} ({$sentField})");
        }
    }
}
