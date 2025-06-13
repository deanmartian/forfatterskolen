<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    'app_site' => env('APP_SITE', 'production'),

    'timezone' => 'Europe/Oslo',

    'log' => env('APP_LOG', 'single'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    'log_max_files' => env('APP_LOG_MAX_FILES', 2),

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */
        Laravel\Tinker\TinkerServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        // Other service providers...
        Laravel\Socialite\SocialiteServiceProvider::class,

        Maatwebsite\Excel\ExcelServiceProvider::class,

        // Laravel File manager providers
        UniSharp\LaravelFilemanager\LaravelFilemanagerServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,

        Barryvdh\TranslationManager\ManagerServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,

        Anhskohbo\NoCaptcha\NoCaptchaServiceProvider::class,
        App\Providers\DropboxServiceProvider::class,
    ])->toArray(),

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
