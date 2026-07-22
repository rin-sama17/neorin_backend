<?php

namespace App\Http\Controllers\App;

use app\Events\PaymentSucceeded;
use App\Http\Controllers\Controller;
use App\Http\Services\Payment\PaymentService;
use App\Models\Shop\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private PaymentService $paymentService) {}

    public function redirect(Order $order)
    {
        $this->authorize('view', $order);
        $url = $this->paymentService->initiate($order);
        return response()->json(['payment_url' => $url]);
    }

    public function callback(Request $request)
    {
        try {
            $order = $this->paymentService->verify($request);
            event(new PaymentSucceeded($order, $order->payments->last()));
            return redirect(env('FRONTEND_URL') . "/orders/{$order->id}/success");
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL') . "/orders/failed?message={$e->getMessage()}");
        }
    }
}
