<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $actionText;

    public $actionUrl;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->actionText = 'Se Alle Kurs';
        $this->actionUrl = 'https://www.forfatterskolen.no/course';
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', 'support@forfatterskolen.no');
        $fromName = config('mail.from.name', 'Forfatterskolen');

        return $this->from($fromAddress, $fromName)
            ->subject('Welcome to Forfatterskolen')
            ->view('emails.registration');
    }
}
