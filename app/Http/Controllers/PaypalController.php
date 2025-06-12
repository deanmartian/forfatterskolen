<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\AdminHelpers;
use App\Invoice;
use App\Paypal;
use App\PayPalIPN;
use App\Repositories\IPNRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PayPal\IPN\Event\IPNInvalid;
use PayPal\IPN\Event\IPNVerificationFailure;
use PayPal\IPN\Event\IPNVerified;
use PayPal\IPN\Listener\Http\ArrayListener;

/**
 * Class PayPalController
 */
class PaypalController extends Controller
{
    protected $repository;

    public function __construct(IPNRepository $repository)
    {
        $this->repository = $repository;
    }

    public function form(Request $request, $invoice_id = null): View
    {
        $invoice_id = $invoice_id ?: encrypt(1);

        $order = Invoice::findOrFail(decrypt($invoice_id));

        return view('form', compact('order'));
    }

    public function checkout($invoice_id, Request $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail(decrypt($invoice_id));

        $paypal = new Paypal;

        $response = $paypal->purchase([
            'amount' => ($invoice->gross / 100),
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
     * @param  $invoice_id
     *                     $param $page
     * @return mixed
     */
    public function completed($invoice_id, $page, Request $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($invoice_id);

        $paypal = new Paypal;

        $response = $paypal->complete([
            'amount' => ($invoice->gross / 100),
            'transactionId' => $invoice_id,
            'currency' => 'NOK',
            'cancelUrl' => $paypal->getCancelUrl($invoice_id),
            'returnUrl' => $paypal->getReturnUrl($invoice_id),
            'notifyUrl' => $paypal->getNotifyUrl($invoice_id),
        ]);

        if ($response->isSuccessful()) {
            return redirect()->route('front.shop.thankyou', ['page' => $page]);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($response->getMessage()),
        ]);
    }

    public function cancelled($invoice_id): RedirectResponse
    {
        $order = Invoice::findOrFail($invoice_id);

        return redirect()->route('app.home', encrypt($invoice_id))->with([
            'errors' => 'You have cancelled your recent PayPal payment !',
        ]);
    }

    /**
     * @param  $request  Request
     */
    public function webhook($invoice_id, $env, Request $request)
    {
        $listener = new ArrayListener;

        if ($env == 'sandbox') {
            $listener->useSandbox();
        }

        Log::info('inside webhook');
        $listener->setData($request->all());

        $listener = $listener->run();

        $listener->onInvalid(function (IPNInvalid $event) use ($invoice_id) {
            Log::info('inside invalid');
            $this->repository->handle($event, PayPalIPN::IPN_INVALID, $invoice_id);
        });

        $listener->onVerified(function (IPNVerified $event) use ($invoice_id) {
            Log::info('inside verified');
            $this->repository->handle($event, PayPalIPN::IPN_VERIFIED, $invoice_id);
        });

        $listener->onVerificationFailure(function (IPNVerificationFailure $event) use ($invoice_id) {
            Log::info('inside failure');
            $this->repository->handle($event, PayPalIPN::IPN_FAILURE, $invoice_id);
        });

        $listener->listen();
    }
}
