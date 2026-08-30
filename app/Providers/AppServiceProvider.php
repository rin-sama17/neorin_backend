<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Events\OrderCreated;
use App\Events\PaymentSucceeded;
use App\Http\Services\Cart\CartService;
use App\Http\Services\Order\CartToOrderConverter;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Order\PriceCalculatorService;
use App\Http\Services\CustomProduct\RuleEngine;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\ClearUserCart;
use App\Models\Product\Gallery;
use App\Models\Product\Products;
use App\Models\Shop\CartItem;
use App\Models\Shop\Order;
use App\Policies\CartItemPolicy;
use App\Policies\GalleryPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use App\Policies\OrderPolicy;
use App\Policies\ProductsPolicy;
use App\Http\Services\Cart\CartMergeService;
use App\Models\User\Address;
use App\Policies\AddressPolicy;
use App\Services\Order\Calculation\StrategyResolver;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StrategyResolver::class);
        $this->app->singleton(PriceCalculatorService::class);
        $this->app->singleton(CartToOrderConverter::class);
        $this->app->singleton(CartService::class);
        $this->app->singleton(CartMergeService::class);
        $this->app->singleton(OrderService::class);
        $this->app->singleton(RuleEngine::class);
    }

    public function boot()
    {
        Auth::loginUsingId(2);
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        Event::listen(OrderCreated::class, SendOrderConfirmation::class);
        Event::listen(PaymentSucceeded::class, ClearUserCart::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);
        Gate::policy(Products::class, ProductsPolicy::class);
        Gate::policy(Gallery::class, GalleryPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
    }
}
