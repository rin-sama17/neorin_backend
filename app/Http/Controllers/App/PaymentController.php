<?php

namespace App\Http\Controllers\App;

use app\Events\PaymentSucceeded;
use App\Http\Controllers\Controller;
use App\Http\Services\Payment\PaymentService;
use App\Http\Services\Payment\ZarinpalService;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function createPayment(Request $request, ZarinpalService $zarinpalService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        $result = $zarinpalService->createPayment(
            $request->amount,
            $request->description,
            3,
            24,
            route('payment.verify')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }


    public function verifyPayment(Request $request, ZarinpalService $zarinpalService)
    {

        $authority = $request->get('Authority') ?? $request->get('authority');
        $status = $request->get('Status') ?? $request->get('status');

        if (!$authority || !$status) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'اطلاعات تراکنش معتبر نیست',
            // ], 400);
            return redirect('http://localhost:3000/success-payment?Authority=&Status=NOK');
        }

        if ($status != 'OK') {

            // return response()->json([
            //     'success' => false,
            //     'message' => 'تراکنش ناموفق بود',
            // ], 400);
            return redirect('http://localhost:3000/success-payment?Authority=&Status=NOK');
        }

        $payment = Payment::where('authority', $authority)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'تراکنش یافت نشد',
            ], 400);
        }


        $result = $zarinpalService->verifyPayment($authority, $payment->amount);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در تایید تراکنش',
            ], 400);
        }
        $order = Order::where('payment_id', $payment->id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت سفارش',
            ], 400);
        }
        $order->update([
            'order_status' => 'pending'
        ]);
        // آپدیت وضعیت پرداخت
        $payment->update([
            'status' => 'paid',
            'ref_id' => $result['payment']['ref_id'] ?? null,
            'card_pan' => $result['payment']['card_pan'] ?? null,
            'gateway_response' => $result['payment']['gateway_response'] ?? null,
        ]);


        return redirect('http://localhost:3000/success-payment?Authority=' . $authority . '&Status=OK');
    }



    public function getPayment($id)
    {

        $payment = Payment::find($id)->where('user_id', 3)->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'تراکنش یافت نشد',
            ], 400);
        }

        return response()->json($payment, 200);
    }
}
