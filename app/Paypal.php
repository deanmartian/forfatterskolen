<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Omnipay\Omnipay;

/**
 * Class PayPal
 */
class Paypal
{
    /**
     * @return mixed
     */
    public function gateway()
    {
        $gateway = Omnipay::create('PayPal_Express');

        $gateway->setUsername(config('paypal.credentials.username'));
        $gateway->setPassword(config('paypal.credentials.password'));
        $gateway->setSignature(config('paypal.credentials.signature'));
        //$gateway->setTestMode(config('paypal.credentials.sandbox'));

        return $gateway;
    }

    /**
     * @return mixed
     */
    public function purchase(array $parameters)
    {
        Log::info('purchase');
        $response = $this->gateway()
            ->purchase($parameters)
            ->send();

        return $response;
    }

    public function complete(array $parameters)
    {
        $response = $this->gateway()
            ->completePurchase($parameters)
            ->send();

        return $response;
    }

    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    public function getCancelUrl($invoice_id)
    {
        return route('paypal.checkout.cancelled', $invoice_id);
    }

    public function getReturnUrl($invoice_id, $page = 'paypal')
    {
        return route('paypal.checkout.completed', [$invoice_id, $page]);
    }

    public function getNotifyUrl($invoice_id)
    {
        $env = /* config('paypal.credentials.sandbox') ? "sandbox" : */ 'live';
        Log::info('inside get notifiy url');
        Log::info(route('webhook.paypal.ipn', [$invoice_id, $env]));

        return route('webhook.paypal.ipn', [$invoice_id, $env]);
    }
}
