<?php

return [

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

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
        'channel_id' => env('BIGMARKER_CHANNEL_ID'),
        // Hardkodede fallback-URL-er — disse env-variablene forsvant fra .env
        // i april 2026 og førte til at webinar-cronen feilet stille på 142
        // registreringer. Fallback hindrer at det skjer igjen.
        'register_link' => env('BIGMARKER_REGISTER_LINK', 'https://www.bigmarker.com/api/v1/conferences/register'),
        'show_conference_link' => env('BIGMARKER_SHOW_CONFERENCE_LINK', 'https://www.bigmarker.com/api/v1/conferences/'),
        'base_url' => 'https://www.bigmarker.com/api/v1',
    ],

    'activecampaign' => [
        'url' => env('ACTIVECAMPAIGN_URL'),
        'key' => env('ACTIVECAMPAIGN_KEY'),
    ],

    'facebook_ads' => [
        'app_id' => env('FACEBOOK_APP_ID'),
        'app_secret' => env('FACEBOOK_APP_SECRET'),
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
        'ad_account_id' => env('FACEBOOK_AD_ACCOUNT_ID'),
        'page_id' => env('FACEBOOK_PAGE_ID'),
        'webhook_verify_token' => env('FACEBOOK_WEBHOOK_VERIFY_TOKEN'),
    ],

    'google_ads' => [
        'id' => env('GOOGLE_ADS_ID'),
        'conversion_purchase' => env('GOOGLE_ADS_CONVERSION_PURCHASE'),
        'conversion_checkout' => env('GOOGLE_ADS_CONVERSION_CHECKOUT'),
        'conversion_lead' => env('GOOGLE_ADS_CONVERSION_LEAD'),
    ],

    'meta_pixel' => [
        'id' => env('META_PIXEL_ID'),
    ],

    'helpwise' => [
        'api_key' => env('HELPWISE_API_KEY'),
        'api_secret' => env('HELPWISE_API_SECRET'),
        'base_url' => env('HELPWISE_BASE_URL', 'https://app.helpwise.io/api/v1'),
        'webhook_secret' => env('HELPWISE_WEBHOOK_SECRET'),
        'widget_id' => env('HELPWISE_WIDGET_ID', '60b54b2873539'),
    ],

    'wistia' => [
        'api_token' => env('WISTIA_API_TOKEN'),
        'base_url' => 'https://api.wistia.com/v1',
    ],

    'tracking' => [
        'enabled' => env('TRACKING_ENABLED', false),
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

    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY') ?: (file_exists(base_path('.env')) ? (function() {
            $lines = file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with($line, 'ANTHROPIC_API_KEY=')) {
                    return trim(str_replace(['ANTHROPIC_API_KEY=', '"'], '', $line));
                }
            }
            return null;
        })() : null),
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

    'whereby' => [
        'key' => env('WHEREBY_API_KEY'),
    ],

];
