<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(RouteServiceProvider::HOME);

        $middleware->validateCsrfTokens(except: [
            //
            '/paypalipn',
            '/paypalipn*',
            'paypalipn',
            'paypalipn*',
            'paypalipn/*',
            '/webhook/paypal/*',
            'webhook/paypal/*',
            'gotowebinar',
            'gotowebinar*',
            'gotowebinar/*',
            '/vipps/*',
            '/vipps*',
            '/vipps/payment/v2/payments/*',
            '/vipps/payment/v2/payments*',
            '/fb-leads',
        ]);

        $middleware->throttleApi();

        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'checkAutoRenewCourses' => \App\Http\Middleware\CheckAutoRenewCourses::class,
            'checkPageAccess' => \App\Http\Middleware\CheckPageAccess::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'editor' => \App\Http\Middleware\Editor::class,
            'giutbok' => \App\Http\Middleware\Giutbok::class,
            'guest' => \App\Http\Middleware\Guest::class,
            'headEditor' => \App\Http\Middleware\HeadEditor::class,
            'learner' => \App\Http\Middleware\Learner::class,
            'logActivity' => \App\Http\Middleware\LogsActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
