<?php

use Illuminate\Support\Facades\Facade;

return [

    'app_site' => env('APP_SITE', 'production'),

    'timezone' => 'Europe/Oslo',

    'log' => env('APP_LOG', 'single'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    'log_max_files' => env('APP_LOG_MAX_FILES', 2),

    'aliases' => Facade::defaultAliases()->merge([
        'AdminHelpers' => App\Http\AdminHelpers::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'FikenInvoice' => App\Http\FikenInvoice::class,
        'FrontendHelpers' => App\Http\FrontendHelpers::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'NoCaptcha' => Anhskohbo\NoCaptcha\Facades\NoCaptcha::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,
    ])->toArray(),

];
