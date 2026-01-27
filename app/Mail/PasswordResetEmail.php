<?php

namespace App\Mail;

use App\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $actionText;

    public $actionUrl;

    public $level = 'default';

    public function __construct(PasswordReset $passwordReset)
    {
        $this->actionText = 'Tilbakestille Passord';
        $this->actionUrl = url('/auth/passwordreset').'/'.$passwordReset->token;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', 'postmail@forfatterskolen.no');
        $fromName = config('mail.from.name', 'Forfatterskolen');
        $replyToAddress = config('mail.reply_to.address', $fromAddress);
        $replyToName = config('mail.reply_to.name', $fromName);

        return $this->from($fromAddress, $fromName)
            ->replyTo($replyToAddress, $replyToName)
            ->subject('Passord Tilbakestilling Forespørsel')
            ->view('emails.passwordreset')
            ->text('emails.passwordreset_plain');
    }
}
