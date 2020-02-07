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
        'password' => 'aristotle12'
    ],

    'big_marker' => [
        'api_key' => '382d97b149ef81d34034',
        'register_link' => 'https://www.bigmarker.com/api/v1/conferences/register',
        'show_conference_link' => 'https://www.bigmarker.com/api/v1/conferences/'
    ],

    'cross-domain' => [
        'url' => 'https://dev.pilotleser.no/api/cross-domain'
    ],

    'jwt' => [
        'secret' => 'cdDmzJqoOjolwgsyrY4bl6Sl3ThPYlFw', // secret used for cross domain login
        'private_key' => "51d3e2c89cccc640c52987e2e70fe410c518900cc038211b"
    ]

];
