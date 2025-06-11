<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoachingSuggestionDateEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;

    public $suggested_dates;

    public function __construct($email_data)
    {
        $this->sender = $email_data['sender'];
        $this->suggested_dates = $email_data['suggested_dates'];
    }

    public function build()
    {
        return $this->from('post@forfatterskolen.no', 'Forfatterskolen')
            ->subject('New suggestion date')
            ->view('emails.suggestion_date');
    }
}
