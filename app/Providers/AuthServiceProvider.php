<?php

namespace App\Providers;

use App\CoursesTaken;
use App\Invoice;
use App\Order;
use App\Policies\InvoicePolicy;
use App\Policies\LearnerPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        CoursesTaken::class => LearnerPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('viewShopManuscriptCheckoutOrder', function (User $user, Order $order): bool {
            return (int) $order->user_id === (int) $user->id && (int) $order->type === Order::MANUSCRIPT_TYPE;
        });

        Gate::define('cancelShopManuscriptCheckoutOrder', function (User $user, Order $order): bool {
            return (int) $order->user_id === (int) $user->id
                && (int) $order->type === Order::MANUSCRIPT_TYPE
                && (int) $order->is_processed === 0
                && (int) ($order->is_order_withdrawn ?? 0) === 0;
        });
    }
}
