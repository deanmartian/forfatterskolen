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

        // Generer magic login-link med redirect til faktura
        if (!empty($this->data['email'])) {
            $this->data['payUrl'] = route('auth.login.email', encrypt($this->data['email'])) . '?redirect=invoices';
        }

        return $this->from($this->data['from'] ?? 'post@forfatterskolen.no', 'Forfatterskolen')
            ->subject($this->data['subject'])
            ->view($view, $this->data);
    }
}
