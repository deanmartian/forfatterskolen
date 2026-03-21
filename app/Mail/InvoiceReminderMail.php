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
            $token = encrypt($this->data['email']);
            $redirectPath = $this->data['payUrl'] ?? '/learner/invoices';
            $this->data['payUrl'] = route('auth.login.emailRedirect', [
                'email' => $token,
                'redirect_link' => encrypt($redirectPath),
            ]);
        }

        return $this->from($this->data['from'] ?? 'post@forfatterskolen.no', 'Forfatterskolen')
            ->subject($this->data['subject'])
            ->view($view, $this->data);
    }
}
