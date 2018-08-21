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
        $this->actionText = "Reset Password";
        $this->actionUrl = url('/auth/passwordreset'). '/' . $passwordReset->token;
    }

    public function build()
    {
        return $this->from('post@forfatterskolen.no', 'Forfatterskolen')
                    ->subject('Password Reset Request')
                    ->view('emails.passwordreset');
    }
}
