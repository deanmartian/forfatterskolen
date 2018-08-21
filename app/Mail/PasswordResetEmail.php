<?php

namespace App\Mail;

use App\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $actionText;
    public $actionUrl;
    public $level = 'default';

    public function __construct(PasswordReset $passwordReset)
    {
        $this->actionText = "Tilbakestille Passord";
        $this->actionUrl = url('/auth/passwordreset'). '/' . $passwordReset->token;
    }

    public function build()
    {
        return $this->from('post@forfatterskolen.no', 'Forfatterskolen')
                    ->subject('Passord Tilbakestilling Forespørsel')
                    ->view('emails.passwordreset');
    }
}
