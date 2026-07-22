<?php

namespace App\Listeners;

use app\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderCreated $event): void
    {
        $order = $event->order->load('user', 'items');
        // Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
    }
}
