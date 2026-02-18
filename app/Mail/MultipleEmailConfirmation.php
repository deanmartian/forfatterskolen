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
        $fromAddress = config('mail.from.address', 'support@forfatterskolen.no');
        $fromName = config('mail.from.name', 'Forfatterskolen');

        return $this->from($fromAddress, $fromName)
            ->subject('Email Confirmation')
            ->view('emails.email_confirmation');
    }
}
