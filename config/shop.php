<?php

return [
    'frontend_url' => env('SHOP_FRONTEND_URL', 'https://shop.indiemoon.no'),

    'shipping' => [
        'NO' => 59,
        'SE' => 99,
        'DK' => 99,
        'FI' => 99,
        'default' => 149,
        'free_above' => 500,
    ],

    'download' => [
        'expires_days' => 30,
        'max_downloads' => 5,
    ],

    'order_prefix' => 'INM',

    'notifications' => [
        'admin_email' => env('SHOP_ADMIN_EMAIL', 'post@indiemoon.no'),
        'send_order_confirmation' => true,
        'send_shipping_notification' => true,
    ],
];
