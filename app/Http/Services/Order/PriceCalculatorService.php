<?php

namespace App\Http\Services\Order;

use App\Models\Shop\CartItem;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\StrategyResolver;

class PriceCalculatorService
{
    public function __construct(
        private StrategyResolver $strategyResolver,
    ) {}

    public function calculateItem(CartItem $item): int
    {
        if ($item->item_type === 'custom_product') {
            return $this->calculateCustomItem($item);
        }

        return $this->calculateRegularItem($item);
    }

    public function calculateRegularItem(CartItem $item): int
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
            ->sum(fn($a) => $a->categoryValue?->price ?? 0);

        return $basePrice + $fabricPrice + $attrPrice;
    }

    public function calculateCustomItem(CartItem $item): int
    {
        $breakdown = $this->calculateCustomItemBreakdown($item);
        return (int) ($breakdown['total'] ?? 0);
    }

    public function calculateCustomItemBreakdown(CartItem $item): array
    {
        $itemConfig = $item->configuration ?? [];
        $itemId     = $itemConfig['custom_product_item_id'] ?? null;

        if (!$itemId) {
            return ['total' => 0];
        }

        $productItem = CustomProductItem::with('calculationProfile')->find($itemId);

        if (!$productItem) {
            return ['total' => 0];
        }

        return $this->strategyResolver->calculateForItem($item, $productItem);
    }

    public function calculateShipping(int $subtotal): int
    {
        return $subtotal >= 5_000_000 ? 0 : 350_000;
    }
}
