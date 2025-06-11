<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;

    public $email_content;

    public $email_message;

    public $email_subject;

    public $from_name;

    public $from_email;

    public $attach_file;

    public $track_code;

    public $actionText;

    public $actionUrl;

    public $user;

    public $package_id;

    public function __construct($to, $subject, $message, $from_email, $from_name, $attachment, $track_code,
        $actionText, $actionUrl, $user, $package_id)
    {
        $this->recipient = $to;
        $this->email_subject = $subject;
        $this->email_content = $message;
        $this->email_message = $message;
        $this->from_email = $from_email;
        $this->from_name = $from_name;
        $this->attach_file = $attachment;
        $this->track_code = $track_code;
        $this->actionText = $actionText;
        $this->actionUrl = $actionUrl;
        $this->user = $user;
        $this->package_id = $package_id;
    }

    public function build()
    {
        $email = $this->to($this->recipient)
            ->from($this->from_email, $this->from_name)
            ->subject($this->email_subject)
            ->view('emails.course_order')
            ->text('emails.subject_body_plain');

        // check if there's an attachment to prevent error
        if ($this->attach_file) {
            if (is_array($this->attach_file)) {
                foreach ($this->attach_file as $attachment) {
                    $email->attach($attachment);
                }
            } else {
                $email->attach($this->attach_file);
            }
        }

        return $email;
    }
}
