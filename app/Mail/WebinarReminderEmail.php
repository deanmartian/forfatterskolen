<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebinarReminderEmail extends Mailable
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
            ->subject($this->data['reminderText'] . ': ' . $this->data['webinarTitle'])
            ->view('emails.branded.webinar-reminder')
            ->with($this->data);
    }
}
