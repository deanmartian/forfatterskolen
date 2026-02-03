<?php

return [
    'jwt' => [
        'access_ttl_minutes' => 15,
        'refresh_ttl_days' => 14,
    ],
    'cors' => [
        'lovable_origins' => env(
            'LOVABLE_CORS_ORIGINS',
            'https://*.lovableproject.com,https://*.lovable.app,https://lovable.app,https://staging.lovable.app'
        ),
        'allow_credentials' => env('LOVABLE_CORS_ALLOW_CREDENTIALS', true),
    ],
];
