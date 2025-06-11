<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentSubmittedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_message;

    public function __construct($email_data)
    {
        $this->email_message = $email_data['email_message'];
    }

    public function build()
    {
        return $this->from('post@forfatterskolen.no', 'Forfatterskolen')
            ->subject('New manuscript submitted for assignment')
            ->view('emails.assignment_submitted');
    }
}
