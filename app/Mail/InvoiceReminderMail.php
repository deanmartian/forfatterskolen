<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $view = $this->data['type'] === 'overdue'
            ? 'emails.branded.invoice-overdue'
            : 'emails.branded.invoice-due-reminder';

        return $this->from($this->data['from'] ?? 'post@forfatterskolen.no', 'Forfatterskolen')
            ->subject($this->data['subject'])
            ->view($view, $this->data);
    }
}
