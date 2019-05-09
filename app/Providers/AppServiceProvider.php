<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::extend('alpha_num_spaces', function ($attribute, $value) {
            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[a-zA-Z0-9\s]+$/', $value);
        });

        Validator::extend('alpha_spaces', function ($attribute, $value) {
            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s-]+$/u', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton('Bambora', function() {
            return (object) [
                'username' => config('services.bambora.access_key').'@'.config('services.bambora.merchant_number'),
                'password' => config('services.bambora.secret_key'),
                'credentials' => base64_encode(config('services.bambora.access_key').
                    '@'.config('services.bambora.merchant_number').':'.config('services.bambora.secret_key'))
            ];
        });

        /*
         * uncomment this for the server that uses public_thml
         * $this->app->bind('path.public', function() {
            return realpath(base_path().'/../public_html');
        });*/
    }
}
