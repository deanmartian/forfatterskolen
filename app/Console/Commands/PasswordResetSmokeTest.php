<?php

namespace App\Console\Commands;

use App\Mail\SubjectBodyEmail;
use App\PasswordReset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetSmokeTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwordreset:smoke-test {email : Recipient email address} {--queue : Queue the email instead of sending immediately} {--mailer= : Override the mailer to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a password reset token and send a reset email for smoke testing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $useQueue = (bool) $this->option('queue');
        $mailer = $this->option('mailer') ?: config('mail.default', 'smtp');
        $correlationId = (string) Str::uuid();

        $token = $this->generateToken();
        $passwordReset = new PasswordReset();
        $passwordReset->email = $email;
        $passwordReset->token = $token;
        $passwordReset->save();

        $actionText = 'Tilbakestille Passord';
        $actionUrl = url('/auth/passwordreset').'/'.$passwordReset->token;
        $level = 'default';

        $emailData = [
            'email_subject' => 'Forespørsel om å tilbakestille passordet ditt',
            'email_message' => view('emails.passwordreset', compact('actionText', 'actionUrl', 'level'))->render(),
            'from_name' => '',
            'from_email' => config('mail.from.address', 'postmail@forfatterskolen.no'),
            'attach_file' => null,
            'text_view' => 'emails.passwordreset_plain',
            'view_data' => compact('actionUrl'),
        ];

        $mailerInstance = Mail::mailer($mailer)->to($email);

        if ($useQueue) {
            $mailerInstance->queue(new SubjectBodyEmail($emailData));
        } else {
            $mailerInstance->send(new SubjectBodyEmail($emailData));
        }

        $logContext = [
            'correlation_id' => $correlationId,
            'mailer' => $mailer,
            'queued' => $useQueue,
            'recipient' => $email,
        ];

        Log::info('Password reset smoke test dispatched.', $logContext);

        $status = $useQueue ? 'queued' : 'sent';
        $this->info("Password reset smoke test {$status}.");
        $this->line('Correlation ID: '.$correlationId);
        $this->line('Mailer: '.$mailer);
        $this->line('Queue: '.($useQueue ? 'queued' : 'sent'));

        return Command::SUCCESS;
    }

    private function generateToken(): string
    {
        do {
            $token = Str::random(60);
        } while (PasswordReset::where('token', $token)->exists());

        return $token;
    }
}
