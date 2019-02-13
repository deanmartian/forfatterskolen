<?php

namespace App\Repositories;

use App\Http\AdminHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use App\PayPalIPN;
use Illuminate\Http\Request;

/**
 * Class IPNRepository
 * @package App\Repositories
 */
class IPNRepository
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $event
     * @param $verified
     * @param $invoice_id
     */
    public function handle($event, $verified, $invoice_id)
    {
        $object = $event->getMessage();

        $invoice = Invoice::find($invoice_id);
        if ($invoice)
        {
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
                if ($invoice && $invoice->unpaid()) {
                    $invoice->update([
                        'fiken_is_paid' => $invoice::COMPLETED,
                    ]);

                    $email = 'elybutabara@gmail.com';
                    $subject = 'Paypal Payment';
                    $message = $invoice->user->full_name.' has paid the amount of '.AdminHelpers::currencyFormat($invoice->gross/100).
                        ' for invoice #'.$invoice->invoice_number;
                    $from_email = '';
                    $from_name = '';

                    $emailData['email_subject'] = $subject;
                    $emailData['email_message'] = $message;
                    $emailData['from_name'] = $from_name;
                    $emailData['from_email'] = $from_email;
                    $emailData['attach_file'] = NULL;
                    \Mail::to($email)->queue(new SubjectBodyEmail($emailData));
                }
            }
        }
    }
}