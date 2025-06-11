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
        $this->actionUrl = 'http://www.forfatterskolen.no/course';
    }

    public function build()
    {
        return $this->from('postmail@forfatterskolen.no', 'Forfatterskolen')
            ->subject('Welcome to Forfatterskolen')
            ->view('emails.registration');
    }
}
