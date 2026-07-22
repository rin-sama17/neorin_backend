<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Http\Services\Order\OrderService;
use App\Models\Shop\Cart;
use App\Models\Shop\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private OrderService $orderService) {}

    public function index(): JsonResponse
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['items', 'payments'])->findOrFail($id);
        $this->authorize('view', $order);
        return response()->json($order);
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with(['items'])
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }

        $order = $this->orderService->checkout(
            user: auth()->user(),
            cart: $cart,
            addressData: $request->validated('address'),
            paymentMethod: $request->validated('payment_method'),
            notes: $request->validated('notes'),
        );

        return response()->json([
            'order_id'    => $order->id,
            'total'       => $order->total_price,
            'payment_url' => route('payment.redirect', $order->id),
        ], 201);
    }
}
