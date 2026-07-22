<?php

namespace App\Http\Services\Payment;

use App\Models\Shop\Order;
use GuzzleHttp\Psr7\Request;

class PaymentService
{
    public function initiate(Order $order)
    {
        $payment_url = $order;
        // ارتباط با زرین‌پال یا درگاه دیگه
        return $payment_url;
    }

    public function verify(Request $request)
    {
        $order = $request;
        // تایید پرداخت از درگاه
        // update payment record
        return $order;
    }
}
