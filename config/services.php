<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
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
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'client_id_old' => env('FACEBOOK_CLIENT_ID_OLD'),
        'client_secret_old' => env('FACEBOOK_CLIENT_SECRET_OLD'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'gotowebinar' => [
        'consumer_key' => env('GT_WEBINAR_CONSUMER_KEY'),
        'consumer_secret' => env('GT_WEBINAR_CONSUMER_SECRET'),
        'user_id' => env('GT_WEBINAR_USER'),
        'password' => env('GT_WEBINAR_PASS'),
    ],

    'bambora' => [
        'secret_key' => env('BAMBORA_SECRET_KEY'),
        'access_key' => env('BAMBORA_ACCESS_KEY'),
        'merchant_number' => env('BAMBORA_MERCHANT_NUMBER'),
        'encoded_api_key' => env('BAMBORA_ENCODED_API_KEY'),
        'md5_key' => env('BAMBORA_MD5_KEY'),
    ],

    'fiken' => [
        'username' => env('FIKEN_USERNAME'),
        'password' => env('FIKEN_PASSWORD'),
        'client_id' => env('FIKEN_CLIENT_ID'),
        'client_secret' => env('FIKEN_CLIENT_SECRET'),
        'personal_api_key' => env('FIKEN_PERSONAL_API_KEY'),
        'api_url' => env('FIKEN_API_URL'),
        'company_slug' => env('FIKEN_COMPANY_SLUG'),
        'company_slug_test' => env('FIKEN_COMPANY_SLUG_TEST'),
    ],

    'big_marker' => [
        'api_key' => env('BIGMARKER_API_KEY'),
        'register_link' => env('BIGMARKER_REGISTER_LINK'),
        'show_conference_link' => env('BIGMARKER_SHOW_CONFERENCE_LINK'),
    ],

    'cross-domain' => [
        'url' => env('CROSS_DOMAIN_URL'),
    ],

    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'private_key' => env('JWT_PRIVATE_KEY'),
    ],

    'svea' => [
        'identifier' => env('SVEA_IDENTIFIER'),
        'country_code' => env('SVEA_COUNTRY_CODE'),
        'currency' => env('SVEA_CURRENCY'),
        'locale' => env('SVEA_LOCALE'),
        'checkoutid' => env('SVEA_CHECKOUTID'),
        'checkout_secret' => env('SVEA_CHECKOUT_SECRET'),
        'checkoutid_test' => env('SVEA_CHECKOUTID_TEST'),
        'checkout_secret_test' => env('SVEA_CHECKOUT_SECRET_TEST'),
        'checkoutid_test2' => env('SVEA_CHECKOUTID_TEST2'),
        'checkout_secret_test2' => env('SVEA_CHECKOUT_SECRET_TEST2'),
    ],

    'vipps' => [
        'client_id' => env('VIPPS_CLIENT_ID'),
        'client_secret' => env('VIPPS_CLIENT_SECRET'),
        'client_id_test' => env('VIPPS_CLIENT_ID_TEST'),
        'client_secret_test' => env('VIPPS_CLIENT_SECRET_TEST'),
        'login_scope' => env('VIPPS_LOGIN_SCOPE', 'name email address phoneNumber birthDate'),
        'login_scope_dev' => env('VIPPS_LOGIN_SCOPE_DEV', 'openid name email address phoneNumber nin birthDate accountNumbers'),
        'login_redirect_uri' => env('VIPPS_LOGIN_REDIRECT_URI'),
        'login_auth_link' => env('VIPPS_LOGIN_AUTH_LINK'),
        'login_token_link' => env('VIPPS_LOGIN_TOKEN_LINK'),
        'login_user_info_link' => env('VIPPS_LOGIN_USER_INFO_LINK'),
        'url' => env('VIPPS_URL'),
        'url_test' => env('VIPPS_URL_TEST'),
        'subscription' => env('VIPPS_SUBSCRIPTION'),
        'subscription_test' => env('VIPPS_SUBSCRIPTION_TEST'),
        'msn' => env('VIPPS_MSN'),
        'msn_test' => env('VIPPS_MSN_TEST'),
    ],

    'gpt' => [
        'api_key' => env('GPT_API_KEY'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY_NEW'),
    ],

    'dropbox' => [
        'token' => env('DROPBOX_TOKEN'),
        'key' => env('DROPBOX_APP_KEY'),
        'secret' => env('DROPBOX_APP_SECRET'),
        'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
    ],

    'cloudconvert' => [
        'api_key' => env('CLOUDCONVERT_API_KEY'),
    ],

];
