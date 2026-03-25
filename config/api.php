<?php

return [
    'jwt' => [
        'access_ttl_minutes' => 15,
        'refresh_ttl_days' => 14,
    ],
    'community_sso' => [
        'code_ttl_seconds' => env('COMMUNITY_SSO_CODE_TTL_SECONDS', 120),
    ],
    'cors' => [
        'lovable_origins' => env(
            'LOVABLE_CORS_ORIGINS',
            'https://*.lovableproject.com,https://*.lovable.app,https://lovable.app,https://staging.lovable.app,https://forfatterskolen-ny-front.lovable.app,https://ny.forfatterskolen.no,https://indiemoon.no,https://shop.indiemoon.no,https://www.indiemoon.no'
        ),
        'allow_credentials' => env('LOVABLE_CORS_ALLOW_CREDENTIALS', false),
    ],
    'lovable_portal_url' => env('LOVABLE_PORTAL_URL', 'https://ny.forfatterskolen.no/portal'),
    'lovable_url' => 'https://ny.forfatterskolen.no'
];
