<?php

namespace App\Http\Controllers;

use App\Http\AdminHelpers;
use App\Invoice;
use App\Paypal;
use App\PayPalIPN;
use App\Repositories\IPNRepository;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

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

    public function form(Request $request, $invoice_id = null)/* : View */
    {
        $invoice_id = $invoice_id ?: encrypt(1);

        $order = Invoice::findOrFail(decrypt($invoice_id));

        return redirect()->route('learner.invoice');
        //return view('form', compact('order'));
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
        Log::info('Received IPN', ['data' => $request->all()]);
        Log::info('PAYPAL WEBHOOK TRIGGERED', [
            'uri' => $request->getRequestUri(),
            'method' => $request->method(),
            'input' => $request->all(),
            'ip' => $request->ip(),
            'referer' => $request->headers->get('referer'),
            'user-agent' => $request->userAgent(),
        ]);

        $ipnData = $request->all();
        $ipnData['cmd'] = '_notify-validate';

        $paypalUrl = $env === 'sandbox'
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';

        try {
            $client = new Client();
            $response = $client->post($paypalUrl, [
                'form_params' => $ipnData,
                'headers' => ['Connection' => 'close'],
                'http_errors' => false,
            ]);

            $body = trim((string) $response->getBody());

            if ($body === 'VERIFIED') {
                Log::info('IPN VERIFIED');
                $this->repository->handle($ipnData, \App\PayPalIPN::IPN_VERIFIED, $invoice_id);
            } elseif ($body === 'INVALID') {
                Log::warning('IPN INVALID');
                $this->repository->handle($ipnData, \App\PayPalIPN::IPN_INVALID, $invoice_id);
            } else {
                Log::error('Unexpected IPN response', ['response' => $body]);
                $this->repository->handle($ipnData, \App\PayPalIPN::IPN_FAILURE, $invoice_id);
            }
        } catch (\Exception $e) {
            Log::error('IPN Request Failed', ['error' => $e->getMessage()]);
        }

        return response('OK', 200);
    }
}
