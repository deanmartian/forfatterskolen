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

    public function __construct($email_data)
    {
        $this->email_message = $email_data['email_message'];
        $this->email_subject = $email_data['email_subject'];
        $this->from_name = $email_data['from_name'] ? $email_data['from_name'] : 'Forfatterskolen';
        $this->from_email = $email_data['from_email'] ? $email_data['from_email'] : 'postmail@forfatterskolen.no';
        $this->attach_file = $email_data['attach_file'] ?: NULL;
    }

    public function build()
    {
        $email =  $this->from($this->from_email, $this->from_name)
            ->subject($this->email_subject)
            ->view('emails.subject_body');

        // check if there's an attachment to prevent error
        if ($this->attach_file) {
            $email->attach(asset($this->attach_file));
        }

        return $email;
    }

}