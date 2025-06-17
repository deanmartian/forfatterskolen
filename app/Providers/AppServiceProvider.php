<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrap();

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

        Validator::extend('no_links', function ($attribute, $value) {
            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return ! preg_match('/\bhttps?:\/\/\S+/', $value);
        });

        // Enable pagination on collection
        if (! Collection::hasMacro('paginate')) {

            Collection::macro('paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

                    return (new LengthAwarePaginator(
                        $this->forPage($page, $perPage)->values()->all(), $this->count(), $perPage, $page, $options))
                        ->withPath('');
                });
        }

        // Share $standardProject globally
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $standardProject = auth()->user()->standardProject();
                $view->with('standardProject', $standardProject);
            }
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // allow on dev only
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton('Bambora', function () {
            return (object) [
                'username' => config('services.bambora.access_key').'@'.config('services.bambora.merchant_number'),
                'password' => config('services.bambora.secret_key'),
                'credentials' => base64_encode(config('services.bambora.access_key').
                    '@'.config('services.bambora.merchant_number').':'.config('services.bambora.secret_key')),
            ];
        });

        /*
         * uncomment this for the server that uses public_thml
         * $this->app->bind('path.public', function() {
            return realpath(base_path().'/../public_html');
        });*/
    }
}
