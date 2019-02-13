<?php

namespace App;

use Omnipay\Omnipay;

/**
 * Class PayPal
 * @package App
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
     * @param array $parameters
     * @return mixed
     */
    public function purchase(array $parameters)
    {
        $response = $this->gateway()
            ->purchase($parameters)
            ->send();

        return $response;
    }

    /**
     * @param array $parameters
     */
    public function complete(array $parameters)
    {
        $response = $this->gateway()
            ->completePurchase($parameters)
            ->send();

        return $response;
    }

    /**
     * @param $amount
     */
    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * @param $invoice_id
     */
    public function getCancelUrl($invoice_id)
    {
        return route('paypal.checkout.cancelled', $invoice_id);
    }

    /**
     * @param $invoice_id
     */
    public function getReturnUrl($invoice_id)
    {
        return route('paypal.checkout.completed', $invoice_id);
    }

    /**
     * @param $invoice_id
     */
    public function getNotifyUrl($invoice_id)
    {
        $env = /*config('paypal.credentials.sandbox') ? "sandbox" :*/ "live";

        return route('webhook.paypal.ipn', [$invoice_id, $env]);
    }
}