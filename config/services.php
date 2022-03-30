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
        'client_id_new' => '3002661156716042',
        'client_secret_new' => '4c286c6c840db1d5304e4acccf2c5227',
        'client_id' => '300010277156315',
        'client_secret' => '39c7964cecb16b346c8df0ae2e21bcd4',
        'redirect' => 'https://www.forfatterskolen.no/auth/login/facebook/callback',
    ],

    'google' => [
        'client_id' => '720221210213-4ek56b462equ94e1s1lgeudofld8vfuv.apps.googleusercontent.com',
        'client_secret' => 'wyQVaOU2sI3e8aIf8hY9mFSB',
        'redirect' => 'https://www.forfatterskolen.no/auth/login/google/callback',
    ],

    'gotowebinar' => [
        'consumer_key'      => '4jVbfF2qGPp6cw7TAJHD24jJmwGK3hlH',
        'consumer_secret'   => 'C0AByMZssixkcVK0',
        'user_id'           => env('GT_WEBINAR_USER'),
        'password'          => env('GT_WEBINAR_PASS')
    ],

    'bambora' => [
        'secret_key' => 'U4Kj2Ku07AovKcfKnpI9EyiVvvrqri9R1wiX0AxC',
        'access_key' => '3lKQyd8TYvaBBJ0lWmRR',
        'merchant_number' => 'T526080101',
        'encoded_api_key' => 'M2xLUXlkOFRZdmFCQkowbFdtUlI=',
        'md5_key' => 'SMimSoiCqI'
    ],

    'fiken' => [
        'username' => 'elybutabara@yahoo.com',
        'password' => 'janiel12',
        'client_id' => 'xNmlQovYLSHDgAGi46623755940317270',
        'client_secret' => 'a3b5bf4c-6949-4e96-9627-046b586c9be1',
        'personal_api_key' => '1480241174.4djcOoTcjawknSORCxQWr8rF5KToetss', // PERSONAL API KEY
        'api_url' => 'https://api.fiken.no/api/v2',
        'company_slug' => 'forfatterskolen-as',
        'company_slug_test' => 'fiken-demo-glede-og-bil-as2'
    ],

    'big_marker' => [
        'api_key' => '64bd08aa02b7d6ad36b6', //382d97b149ef81d34034
        'register_link' => 'https://www.bigmarker.com/api/v1/conferences/register',
        'show_conference_link' => 'https://www.bigmarker.com/api/v1/conferences/'
    ],

    'cross-domain' => [
        'url' => 'https://www.pilotleser.no/api/cross-domain'
    ],

    'jwt' => [
        'secret' => 'cdDmzJqoOjolwgsyrY4bl6Sl3ThPYlFw', // secret used for cross domain login
        'private_key' => "51d3e2c89cccc640c52987e2e70fe410c518900cc038211b"
    ],

    'svea' => [
        'identifier' => env('SVEA_IDENTIFIER'),
        'country_code' => env('SVEA_COUNTRY_CODE'),
        'currency' => env('SVEA_CURRENCY'),
        'locale' => env('SVEA_LOCALE'),
        'checkoutid' => env('SVEA_CHECKOUTID'),
        'checkout_secret' => env('SVEA_CHECKOUT_SECRET'),
        'checkoutid_test' => env('SVEA_CHECKOUTID_TEST'),
        'checkout_secret_test' => env('SVEA_CHECKOUT_SECRET_TEST')
    ],

    'vipps' => [
        'client_id' => env('VIPPS_CLIENT_ID'),
        'client_secret' => env('VIPPS_CLIENT_SECRET'),
        'client_id_test' => env('VIPPS_CLIENT_ID_TEST'),
        'client_secret_test' => env('VIPPS_CLIENT_SECRET_TEST'),
    ]
];
