<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id' => '300010277156315',
        'client_secret' => '39c7964cecb16b346c8df0ae2e21bcd4',
        'redirect' => 'https://www.forfatterskolen.no/auth/login/facebook/callback',
    ],

    'google' => [
        'client_id' => '720221210213-4ek56b462equ94e1s1lgeudofld8vfuv.apps.googleusercontent.com',
        'client_secret' => 'wyQVaOU2sI3e8aIf8hY9mFSB',
        'redirect' => 'https://www.forfatterskolen.no/auth/login/google/callback',
    ],

    'bambora' => [
        'secret_key' => 'U4Kj2Ku07AovKcfKnpI9EyiVvvrqri9R1wiX0AxC',
        'access_key' => '3lKQyd8TYvaBBJ0lWmRR',
        'merchant_number' => 'T526080101',
        'encoded_api_key' => 'M2xLUXlkOFRZdmFCQkowbFdtUlI=',
        'md5_key' => 'SMimSoiCqI'
    ]

];
