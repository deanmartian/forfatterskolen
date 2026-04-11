<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ad OS - AI Ad Operating System
    |--------------------------------------------------------------------------
    */

    'enabled' => env('ADS_OS_ENABLED', true),

    'default_automation_mode' => env('ADS_AUTOMATION_DEFAULT_MODE', 'supervised'),

    'auto_apply_enabled' => env('ADS_AUTO_APPLY_ENABLED', true),

    'max_daily_budget_change_percent' => (float) env('ADS_MAX_DAILY_AUTOMATED_BUDGET_CHANGE_PERCENT', 15),

    'require_approval_new_campaigns' => (bool) env('ADS_REQUIRE_APPROVAL_FOR_NEW_CAMPAIGNS', true),

    'require_approval_major_budget' => (bool) env('ADS_REQUIRE_APPROVAL_FOR_MAJOR_BUDGET_CHANGES', true),

    'ai_provider' => env('ADS_AI_PROVIDER', 'openai'),

    'platforms' => ['facebook', 'google'],

    'automation_levels' => [
        'manual' => [
            'label' => 'Manuell',
            'description' => 'AI foreslår, mennesket gjør alt',
        ],
        'assisted' => [
            'label' => 'Assistert',
            'description' => 'AI lager utkast og anbefalinger, mennesket godkjenner alt',
        ],
        'supervised' => [
            'label' => 'Overvåket automatisering',
            'description' => 'AI auto-utfører lavrisiko innenfor grenser, høyrisiko krever godkjenning',
        ],
        'full_operator' => [
            'label' => 'Full operatør',
            'description' => 'AI kjører daglig drift automatisk innenfor harde grenser',
        ],
    ],

    'risk_levels' => [
        'low' => ['label' => 'Lav', 'color' => '#28a745'],
        'medium' => ['label' => 'Middels', 'color' => '#ffc107'],
        'high' => ['label' => 'Høy', 'color' => '#fd7e14'],
        'critical' => ['label' => 'Kritisk', 'color' => '#dc3545'],
    ],

    'decision_types' => [
        'create_campaign',
        'pause_campaign',
        'resume_campaign',
        'increase_budget',
        'reduce_budget',
        'reallocate_budget',
        'create_new_variants',
        'duplicate_winner',
        'hold_steady',
        'request_human_review',
    ],

    'objectives' => [
        'leads' => 'Leads',
        'conversions' => 'Konverteringer',
        'traffic' => 'Trafikk',
        'awareness' => 'Kjennskap',
        'engagement' => 'Engasjement',
        'sales' => 'Salg',
        'app_installs' => 'Appinstallasjoner',
    ],

    'sync' => [
        'metrics_interval_minutes' => 60,
        'campaigns_interval_minutes' => 30,
    ],
];
