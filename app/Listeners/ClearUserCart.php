<?php

namespace App\Listeners;

use app\Events\PaymentSucceeded;
use App\Http\Services\Cart\CartService;
use App\Models\Shop\Cart;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearUserCart implements ShouldQueue
{
    public function __construct(private CartService $cartService) {}

    public function handle(PaymentSucceeded $event): void
    {
        $cart = Cart::where('user_id', $event->order->user_id)->first();
        if ($cart) $this->cartService->clear($cart);
    }
}
