<?php

namespace app\Events;

use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use Illuminate\Foundation\Bus\Dispatchable;

class PaymentSucceeded
{
    use Dispatchable;
    public function __construct(
        public readonly Order   $order,
        public readonly Payment $payment,
    ) {}
}
