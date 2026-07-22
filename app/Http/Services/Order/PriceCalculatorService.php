<?php

namespace App\Http\Services\Order;

use App\Models\Shop\CartItem;

class PriceCalculatorService
{
    public function calculateItem(CartItem $item): int
    {
        $item->loadMissing([
            'product',
            'size',
            'fabrics',
            'categoryValues.categoryValue',
        ]);

        $basePrice   = $item->size?->price ?? $item->product?->price ?? 0;
        $fabricPrice = $item->fabrics->sum('price');
        $attrPrice   = $item->categoryValues
            ->filter(fn($a) => $a->category_value_id !== null)
            ->sum(fn($a)    => $a->categoryValue?->price ?? 0);

        return $basePrice + $fabricPrice + $attrPrice;
    }

    public function calculateShipping(int $subtotal): int
    {
        return $subtotal >= 5_000_000 ? 0 : 350_000;
    }
}
