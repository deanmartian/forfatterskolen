<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddMailToQueueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;

    public $email_message;

    public $email_subject;

    public $from_name;

    public $from_email;

    public $attach_file;

    public $track_code;

    public $emailView;

    public $reply_to_email;

    public $reply_to_name;

    public function __construct($to, $subject, $message, $from_email, $from_name, $attachment, $track_code,
        $emailView = 'emails.mail_to_queue', $reply_to_email = null, $reply_to_name = null)
    {
        $this->recipient = $to;
        $this->email_subject = $subject;
        $this->email_message = $message;
        $this->from_email = $from_email;
        $this->from_name = $from_name;
        $this->attach_file = $attachment;
        $this->track_code = $track_code;
        $this->emailView = $emailView;
        $this->reply_to_email = $reply_to_email;
        $this->reply_to_name = $reply_to_name;
    }

    /** Avmeldingslenke — settes automatisk og er tilgjengelig i email-views */
    public $unsubscribe_url;

    public function build()
    {
        // Generer avmeldingslenke som er tilgjengelig for alle email-templates
        // via $unsubscribe_url. Footer-partialen bruker denne for å vise
        // "Meld deg av nyhetsbrev"-lenken på riktig plass (under adressen).
        $this->unsubscribe_url = url('/avmeld/' . base64_encode($this->recipient));

        // Hvis caller eksplisitt har gitt en reply-to (f.eks. inbox-svar fra en
        // privat inbox), bruk den. Ellers fall tilbake til global config.
        $replyToAddress = $this->reply_to_email ?: config('mail.reply_to.address', $this->from_email);
        $replyToName = $this->reply_to_name ?: config('mail.reply_to.name', $this->from_name);

        // Tøm eksisterende reply-to slik at vi ikke får DUPLIKATER fra
        // Laravel mail middleware eller Resend-adapteren. ->replyTo()
        // appender til denne arrayen, så vi må reset-e først.
        $this->replyTo = [];

        $email = $this->to($this->recipient)
            ->from($this->from_email, $this->from_name)
            ->replyTo($replyToAddress, $replyToName)
            ->subject($this->email_subject)
            ->view($this->emailView)
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
