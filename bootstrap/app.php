<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Laravel\Tinker\TinkerServiceProvider::class,
        \Laravel\Socialite\SocialiteServiceProvider::class,
        \Maatwebsite\Excel\ExcelServiceProvider::class,
        \UniSharp\LaravelFilemanager\LaravelFilemanagerServiceProvider::class,
        \Intervention\Image\ImageServiceProvider::class,
        \Barryvdh\TranslationManager\ManagerServiceProvider::class,
        \Barryvdh\DomPDF\ServiceProvider::class,
        \Anhskohbo\NoCaptcha\NoCaptchaServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(\App\Http\Middleware\ForceWww::class);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

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
            'apiRequestId' => \App\Http\Middleware\ApiRequestId::class,
            'apiJwt' => \App\Http\Middleware\ApiJwtAuth::class,
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
        $exceptions->report(function (\Throwable $exception) {
            $request = request();

            if (! $request || ! $request->is('api/v1/*')) {
                return;
            }

            $requestId = $request->attributes->get('request_id') ?: (string) Str::uuid();
            $request->attributes->set('request_id', $requestId);

            logger()->error('API exception', [
                'request_id' => $requestId,
                'exception_class' => get_class($exception),
                'message' => $exception->getMessage(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! $request->is('api/v1/*')) {
                return null;
            }

            $requestId = $request->attributes->get('request_id') ?: (string) Str::uuid();
            $request->attributes->set('request_id', $requestId);

            $makePayload = function (string $message, string $code, int $status, array $details = null) use ($requestId) {
                $payload = [
                    'error' => [
                        'message' => $message,
                        'code' => $code,
                    ],
                    'request_id' => $requestId,
                ];

                if ($details !== null) {
                    $payload['error']['details'] = $details;
                }

                $response = response()->json($payload, $status);
                $response->headers->set('X-Request-Id', $requestId);

                return $response;
            };

            if ($exception instanceof ValidationException) {
                return $makePayload(
                    'The given data was invalid.',
                    'validation_error',
                    422,
                    $exception->errors()
                );
            }

            if ($exception instanceof Illuminate\Auth\AuthenticationException) {
                return $makePayload('Unauthenticated.', 'unauthorized', 401);
            }

            if ($exception instanceof Illuminate\Auth\Access\AuthorizationException) {
                return $makePayload('Forbidden.', 'forbidden', 403);
            }

            if ($exception instanceof NotFoundHttpException
                || $exception instanceof Illuminate\Database\Eloquent\ModelNotFoundException
            ) {
                return $makePayload('Not found.', 'not_found', 404);
            }

            if ($exception instanceof Illuminate\Routing\Exceptions\InvalidSignatureException) {
                return $makePayload('Invalid signature.', 'invalid_signature', 403);
            }

            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
                $code = match ($status) {
                    400 => 'bad_request',
                    401 => 'unauthorized',
                    403 => 'forbidden',
                    404 => 'not_found',
                    405 => 'method_not_allowed',
                    409 => 'conflict',
                    422 => 'validation_error',
                    429 => 'too_many_requests',
                    default => 'http_error',
                };

                $message = $exception->getMessage() ?: match ($status) {
                    400 => 'Bad request.',
                    401 => 'Unauthenticated.',
                    403 => 'Forbidden.',
                    404 => 'Not found.',
                    405 => 'Method not allowed.',
                    409 => 'Conflict.',
                    422 => 'The given data was invalid.',
                    429 => 'Too many requests.',
                    default => 'HTTP error.',
                };

                return $makePayload($message, $code, $status);
            }

            return $makePayload('Server error.', 'server_error', 500);
        });
    })->create();
