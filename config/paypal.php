<?php

return [
    'credentials' => [
        'username' => env('PAYPAL_USERNAME'),
        'password' => env('PAYPAL_PASSWORD'),
        'signature' => env('PAYPAL_SIGNATURE'),
        'sandbox' => env('PAYPAL_SANDBOX'),
        'live' => env('PAYPAL_LIVE'),
    ],
];
