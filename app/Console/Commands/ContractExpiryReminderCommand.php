<?php

namespace App\Console\Commands;

use App\Contract;
use App\Mail\SubjectBodyEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ContractExpiryReminderCommand extends Command
{
    protected $signature = 'contract:expiry-reminder';

    protected $description = 'Send reminders for contracts expiring in 60 and 30 days';

    public function handle(): void
    {
        $this->sendReminders(60, 'reminder_sent_60');
        $this->sendReminders(30, 'reminder_sent_30');
    }

    private function sendReminders(int $days, string $flag): void
    {
        $contracts = Contract::whereNotNull('end_date')
            ->whereNotNull('signature')
            ->whereNotNull('receiver_email')
            ->where($flag, false)
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->whereDate('end_date', '>=', now())
            ->get();

        foreach ($contracts as $contract) {
            $daysLeft = now()->diffInDays($contract->end_date);

            $message = "Hei {$contract->receiver_name},<br/><br/>"
                ."Din kontrakt \"{$contract->title}\" utl&oslash;per om {$daysLeft} dager ({$contract->end_date->format('d.m.Y')}).<br/><br/>"
                ."Ta kontakt med oss for &aring; fornye kontrakten.<br/><br/>"
                ."<a href='".route('front.contract-view', $contract->code)."'>Se kontrakt</a>";

            $emailData = [
                'email_subject' => "Kontrakt utl&oslash;per snart: {$contract->title}",
                'email_message' => $message,
                'from_name' => null,
                'from_email' => null,
                'attach_file' => null,
                'view' => 'emails.contract',
            ];

            Mail::to($contract->receiver_email)->queue(new SubjectBodyEmail($emailData));

            $contract->update([$flag => true]);

            $this->info("Sent {$days}-day reminder for contract #{$contract->id} to {$contract->receiver_email}");
        }

        $this->info("Processed {$contracts->count()} contracts for {$days}-day reminders.");
    }
}
