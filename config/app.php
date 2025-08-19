<?php

use Illuminate\Support\Facades\Facade;

return [

    'app_site' => env('APP_SITE', 'production'),

    'timezone' => 'Europe/Oslo',

    'log' => env('APP_LOG', 'single'),

    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    'locale' => 'no',

    'log_max_files' => env('APP_LOG_MAX_FILES', 2),

    'live_url' => env('APP_LIVE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],

    'aliases' => Facade::defaultAliases()->merge([
        'AdminHelpers' => App\Http\AdminHelpers::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'FikenInvoice' => App\Http\FikenInvoice::class,
        'FrontendHelpers' => App\Http\FrontendHelpers::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'NoCaptcha' => Anhskohbo\NoCaptcha\Facades\NoCaptcha::class,
        'Pdf' => Barryvdh\DomPDF\Facade\Pdf::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Socialite' => Laravel\Socialite\Facades\Socialite::class,
    ])->toArray(),

];
