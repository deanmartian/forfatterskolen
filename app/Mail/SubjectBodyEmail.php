<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubjectBodyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_message;

    public $email_subject;

    public $from_name;

    public $from_email;

    public $attach_file;

    public $email_view;

    public function __construct($email_data)
    {
        $this->email_message = $email_data['email_message'];
        $this->email_subject = $email_data['email_subject'];
        $this->from_name = $email_data['from_name'] ? $email_data['from_name'] : 'Forfatterskolen';
        $this->from_email = $email_data['from_email'] ? $email_data['from_email'] : 'postmail@forfatterskolen.no';
        $this->attach_file = $email_data['attach_file'] ?: null;
        $this->email_view = isset($email_data['view']) ? $email_data['view'] : 'emails.subject_body';
    }

    public function build()
    {
        $email = $this->from($this->from_email, $this->from_name)
            ->subject($this->email_subject)
            ->view($this->email_view)
            ->text('emails.subject_body_plain');

        // check if there's an attachment to prevent error
        if ($this->attach_file) {
            $email->attach(asset($this->attach_file));
        }

        return $email;
    }
}
