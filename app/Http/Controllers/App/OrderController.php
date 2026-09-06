<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Http\Requests\Shop\OrderIndexRequest;
use App\Http\Resources\Shop\OrderResource;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Payment\ZarinpalService;
use App\Models\Shop\Cart;
use App\Models\Shop\Order;
use App\Models\User\Address;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;


class OrderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private OrderService $orderService, private ZarinpalService $zarinpalService) {}

    public function index(OrderIndexRequest $request)
    {
        $orders = Order::query()
            ->forUser($request->user()->id)
            ->status($request->query('status'))
            ->with('items')
            ->latest()
            ->paginate($request->integer('per_page', 10));



        return OrderResource::collection($orders);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['items', 'payments'])->findOrFail($id);
        $this->authorize('view', $order);
        return response()->json($order);
    }

    public function checkout(CheckoutRequest $request)
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with(['items'])
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'سبد خرید خالی است'], 422);
        }
        $address = $this->resolveAddress($request);

        $addressData = [
            'title'       => $address->title,
            'state'       => $address->state->name,
            'city'        => $address->city->name,
            'address'     => $address->address,
            'plaque'      => $address->plaque,
            'unit'        => $address->unit,
            'postal_code' => $address->postal_code,
        ];
        $order = $this->orderService->checkout(
            user: auth()->user(),
            cart: $cart,
            addressData: $addressData,
            notes: $request->validated('notes'),
        );
        $payment =  $this->zarinpalService->createPayment(
            $order->total_price,
            $order->notes ?? 'ساخت سفارش جدید',
            $order->user->id,
            $order->id,
            route('payment.verify')
        );
        return response()->json([
            'payment_url' => $payment['payment_url'],
            'authority' => $payment['authority']
        ]);
    }
    private function resolveAddress(CheckoutRequest $request): Address
    {
        if ($request->filled('address_id')) {
            return $request->user()
                ->addresses()
                ->with(['state', 'city'])
                ->findOrFail($request->address_id);
        }

        $address = $request->user()->addresses()->create($request->validated('new_address'));

        return $address->load(['state', 'city']);
    }
}
