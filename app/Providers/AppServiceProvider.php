<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Events\OrderCreated;
use App\Events\PaymentSucceeded;
use App\Http\Services\Cart\CartService;
use App\Http\Services\Order\CartToOrderConverter;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Order\PriceCalculatorService;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\ClearUserCart;
use App\Models\Shop\CartItem;
use App\Models\Shop\Order;
use App\Policies\CartItemPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use App\Policies\OrderPolicy;
use App\Services\Cart\CartMergeService;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(CartMergeService::class);
        $this->app->singleton(PriceCalculatorService::class);
        $this->app->singleton(CartToOrderConverter::class);
        $this->app->singleton(OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Auth::loginUsingId(1);
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Event::listen(OrderCreated::class, SendOrderConfirmation::class);
        Event::listen(PaymentSucceeded::class, ClearUserCart::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);
    }
}
