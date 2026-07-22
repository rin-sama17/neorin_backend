<?php

namespace App\Http\Services\Order;

use App\Events\OrderCreated;
use App\Models\Shop\Cart;
use App\Models\Shop\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private PriceCalculatorService $calculator,
        private CartToOrderConverter   $converter,
    ) {}

    public function checkout(
        User    $user,
        Cart    $cart,
        array   $addressData,
        string  $paymentMethod,
        ?string $notes = null,
    ): Order {
        return DB::transaction(function () use ($user, $cart, $addressData, $paymentMethod, $notes) {

            $cart->loadMissing([
                'items.product',
                'items.size',
                'items.fabrics',
                'items.categoryValues.categoryValue',
                'items.categoryValues.attribute',
            ]);

            // 1. محاسبه قیمت
            $subtotal = $cart->items->sum(
                fn($item) => $this->calculator->calculateItem($item) * $item->quantity
            );

            $shipping = $this->calculator->calculateShipping($subtotal);
            $discount = $this->getDiscount($user);

            // 2. ساخت order
            $order = Order::create([
                'user_id'                   => $user->id,
                'shipping_address_snapshot' => $addressData,
                'subtotal'                  => $subtotal,
                'shipping_price'            => $shipping,
                'discount'                  => $discount,
                'total_price'               => $subtotal + $shipping - $discount,
                'payment_method'            => $paymentMethod,
                'notes'                     => $notes,
                'order_status'              => 'pending',
                'payment_status'            => 'unpaid',
            ]);

            // 3. ساخت order items
            foreach ($cart->items as $item) {
                $unitPrice = $this->calculator->calculateItem($item);
                $order->items()->create(
                    $this->converter->convert($item, $unitPrice)
                );
            }

            // 4. fire event
            event(new OrderCreated($order));

            return $order->load('items');
        });
    }

    private function getDiscount(User $user): int
    {
        return 0;
    }
}
