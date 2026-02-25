<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentManuscriptEmailToList extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $email_subject;

    public function __construct($email_data)
    {
        $this->data = $email_data['data'];
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', 'support@forfatterskolen.no');
        $fromName = config('mail.from.name', 'Forfatterskolen');

        return $this->from($fromAddress, $fromName)
            ->subject($this->data['subject'])
            ->view('emails.assignment_manuscript_email_to_list');
    }
}
