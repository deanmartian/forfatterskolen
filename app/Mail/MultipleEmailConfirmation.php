<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MultipleEmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public $email;

    public $token;

    public function __construct($email_data)
    {
        $this->name = $email_data['name'];
        $this->email = $email_data['email'];
        $this->token = $email_data['token'];
    }

    public function build()
    {
        return $this->from('postmail@forfatterskolen.no', 'Forfatterskolen')
            ->subject('Email Confirmation')
            ->view('emails.email_confirmation');
    }
}
