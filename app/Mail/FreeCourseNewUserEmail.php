<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FreeCourseNewUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_message;

    public $email_subject;

    public function __construct($email_data)
    {
        $this->email_message = $email_data['email_message'];
        $this->email_subject = $email_data['email_subject'];
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', 'support@forfatterskolen.no');
        $fromName = config('mail.from.name', 'Forfatterskolen');

        return $this->from($fromAddress, $fromName)
            ->subject($this->email_subject)
            ->view('emails.free_course_new_user')
            ->text('emails.subject_body_plain');
    }
}
