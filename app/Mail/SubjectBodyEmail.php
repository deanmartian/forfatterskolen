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

    public $text_view;

    public $view_data;

    public $reply_to_email;

    public $reply_to_name;

    public function __construct($email_data)
    {
        $this->email_message = $email_data['email_message'];
        $this->email_subject = $email_data['email_subject'];
        $this->from_name = $email_data['from_name'] ?: config('mail.from.name', 'Forfatterskolen');
        $this->from_email = $email_data['from_email'] ?: config('mail.from.address', 'support@forfatterskolen.no');
        $this->attach_file = $email_data['attach_file'] ?: null;
        $this->email_view = isset($email_data['view']) ? $email_data['view'] : 'emails.subject_body';
        $this->text_view = isset($email_data['text_view']) ? $email_data['text_view'] : 'emails.subject_body_plain';
        $this->view_data = isset($email_data['view_data']) ? $email_data['view_data'] : [];
        $this->reply_to_email = $email_data['reply_to_email'] ?? null;
        $this->reply_to_name = $email_data['reply_to_name'] ?? null;
    }

    public function build()
    {
        $replyToAddress = $this->reply_to_email ?: config('mail.reply_to.address', $this->from_email);
        $replyToName = $this->reply_to_name ?: config('mail.reply_to.name', $this->from_name);

        $email = $this->from($this->from_email, $this->from_name)
            ->replyTo($replyToAddress, $replyToName)
            ->subject($this->email_subject)
            ->view($this->email_view, $this->view_data)
            ->text($this->text_view, $this->view_data);

        // check if there's an attachment to prevent error
        if ($this->attach_file) {
            $email->attach(asset($this->attach_file));
        }

        return $email;
    }
}
