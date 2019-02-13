<?php

namespace App\Http\Controllers;

use App\Http\AdminHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use App\Paypal;
use App\PayPalIPN;
use App\Repositories\IPNRepository;
use Illuminate\Http\Request;
use PayPal\IPN\Event\IPNInvalid;
use PayPal\IPN\Event\IPNVerificationFailure;
use PayPal\IPN\Event\IPNVerified;
use PayPal\IPN\Listener\Http\ArrayListener;

/**
 * Class PayPalController
 * @package App\Http\Controllers
 */
class PaypalController extends Controller
{

    /**
     * @param IPNRepository $repository
     */
    public function __construct(IPNRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     */
    public function form(Request $request, $invoice_id = null)
    {
        $invoice_id = $invoice_id ?: encrypt(1);

        $order = Invoice::findOrFail(decrypt($invoice_id));

        return view('form', compact('order'));
    }

    /**
     * @param $invoice_id
     * @param Request $request
     */
    public function checkout($invoice_id, Request $request)
    {
        $invoice = Invoice::findOrFail(decrypt($invoice_id));

        $paypal = new Paypal;

        $response = $paypal->purchase([
            'amount' => ($invoice->gross/100),
            'transactionId' => $invoice->invoice_number,
            'currency' => 'NOK',
            'cancelUrl' => $paypal->getCancelUrl($invoice->id),
            'returnUrl' => $paypal->getReturnUrl($invoice->id),
        ]);

        if ($response->isRedirect()) {
            $response->redirect();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($response->getMessage()),
        ]);
    }

    /**
     * @param $invoice_id
     * @param Request $request
     * @return mixed
     */
    public function completed($invoice_id, Request $request)
    {
        $invoice = Invoice::findOrFail($invoice_id);

        $paypal = new Paypal;

        $response = $paypal->complete([
            'amount' => ($invoice->gross/100),
            'transactionId' => $invoice_id,
            'currency' => 'NOK',
            'cancelUrl' => $paypal->getCancelUrl($invoice_id),
            'returnUrl' => $paypal->getReturnUrl($invoice_id),
            'notifyUrl' => $paypal->getNotifyUrl($invoice_id),
        ]);

        if ($response->isSuccessful()) {
            return redirect()->route('front.shop.thankyou',['gateway' => 'Paypal']);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($response->getMessage()),
        ]);
    }

    /**
     * @param $invoice_id
     */
    public function cancelled($invoice_id)
    {
        $order = Invoice::findOrFail($invoice_id);

        return redirect()->route('app.home', encrypt($invoice_id))->with([
            'errors' => 'You have cancelled your recent PayPal payment !',
        ]);
    }

    /**
     * @param $invoice_id
     * @param $env
     * @param $request Request
     */
    public function webhook($invoice_id, $env, Request $request)
    {
        $listener = new ArrayListener;

        if ($env == 'sandbox') {
            $listener->useSandbox();
        }

        $listener->setData($request->all());

        $listener = $listener->run();

        $listener->onInvalid(function (IPNInvalid $event) use ($invoice_id) {
            $this->repository->handle($event, PayPalIPN::IPN_INVALID, $invoice_id);
        });

        $listener->onVerified(function (IPNVerified $event) use ($invoice_id) {
            $this->repository->handle($event, PayPalIPN::IPN_VERIFIED, $invoice_id);
        });

        $listener->onVerificationFailure(function (IPNVerificationFailure $event) use ($invoice_id) {
            $this->repository->handle($event, PayPalIPN::IPN_FAILURE, $invoice_id);
        });

        $listener->listen();
    }
}