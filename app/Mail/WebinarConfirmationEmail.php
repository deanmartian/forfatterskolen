<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebinarConfirmationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->from(config('mail.from.address', 'post@forfatterskolen.no'), 'Forfatterskolen')
            ->subject('Du er påmeldt: ' . $this->data['webinarTitle'])
            ->view('emails.branded.webinar-confirmation')
            ->with($this->data);
    }
}
