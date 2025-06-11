<?php

namespace App\Repositories;

use App\Http\AdminHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use App\PayPalIPN;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class IPNRepository
 */
class IPNRepository
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($event, $verified, $invoice_id)
    {
        $object = $event->getMessage();
        Log::info('inside ipn handle');
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            Log::info('inside ipn handle check invoice');
            Log::info(json_encode($invoice));
            $paypal = PayPalIPN::create([
                'verified' => $verified,
                'transaction_id' => $invoice->id,
                'invoice_id' => $invoice->id,
                'payment_status' => $object->get('payment_status'),
                'request_method' => $this->request->method(),
                'request_url' => $this->request->url(),
                'request_headers' => json_encode($this->request->header()),
                'payload' => json_encode($this->request->all()),
            ]);

            if ($paypal->isVerified() && $paypal->isCompleted()) {
                Log::info('inside ipn handle if verified and completed');
                if ($invoice && $invoice->unpaid()) {
                    Log::info('inside ipn handle if invoice and unpaid');
                    $invoice->update([
                        'fiken_is_paid' => $invoice::COMPLETED,
                    ]);

                    $email = 'support@forfatterskolen.no';
                    $subject = 'Paypal Payment';
                    $message = $invoice->user->full_name.' has paid the amount of '.AdminHelpers::currencyFormat($invoice->gross / 100).
                        ' for invoice #'.$invoice->invoice_number;
                    $from_email = '';
                    $from_name = '';

                    $emailData['email_subject'] = $subject;
                    $emailData['email_message'] = $message;
                    $emailData['from_name'] = $from_name;
                    $emailData['from_email'] = $from_email;
                    $emailData['attach_file'] = null;
                    \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
                    \Mail::to('lovelyayobarrientos@gmail.com')->queue(new SubjectBodyEmail($emailData));
                }
            }
        }
    }
}
