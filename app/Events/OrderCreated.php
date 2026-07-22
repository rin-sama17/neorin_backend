<?php

namespace app\Events;

use App\Models\Shop\Order;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderCreated
{
    use Dispatchable;
    public function __construct(public readonly Order $order) {}
}
