<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        '/paypalipn',
        '/paypalipn*',
        'paypalipn',
        'paypalipn*',
        'paypalipn/*',
        '/webhook/paypal/*',
        'webhook/paypal/*',
        'gotowebinar',
        'gotowebinar*',
        'gotowebinar/*',
        '/vipps/*',
        '/vipps*',
        '/vipps/payment/v2/payments/*',
        '/vipps/payment/v2/payments*',
        '/fb-leads',
    ];
}
