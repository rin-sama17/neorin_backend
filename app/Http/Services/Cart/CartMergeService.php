<?php

// app/Services/Cart/CartMergeService.php

namespace App\Http\Services\Cart;

use App\Http\Services\Cart\CartService;
use App\Models\Shop\Cart;
use App\Models\User;

class CartMergeService
{
    public function __construct(private CartService $cartService) {}

    public function merge(string $sessionId, User $user): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->with([
            'items.fabrics',
            'items.categoryValues',
        ])->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            $guestCart?->delete();
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $item) {
            $this->cartService->add($userCart, [
                'item_type'  => $item->item_type,
                'product_id' => $item->product_id,
                'size_id'    => $item->size_id,
                'quantity'   => $item->quantity,
                'fabric_ids' => $item->fabrics->pluck('id')->toArray(),
                'attributes' => $item->categoryValues->map(fn($a) => [
                    'attribute_id' => $a->category_attribute_id,
                    'value_id'     => $a->category_value_id,
                    'value'        => $a->value,
                ])->toArray(),
            ]);
        }

        $guestCart->delete();
    }
}
