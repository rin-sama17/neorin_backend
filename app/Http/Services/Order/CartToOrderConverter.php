<?php

namespace App\Http\Services\Order;

use App\Models\Shop\CartItem;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\StrategyResolver;

class CartToOrderConverter
{
    public function __construct(
        private StrategyResolver $strategyResolver,
    ) {}

    public function convert(CartItem $item, int $unitPrice): array
    {
        return $item->item_type === 'product'
            ? $this->convertProduct($item, $unitPrice)
            : $this->convertCustomProduct($item, $unitPrice);
    }

    private function convertProduct(CartItem $item, int $unitPrice): array
    {
        $product = $item->product;
        $fabrics = $item->fabrics;
        $attrs   = $item->categoryValues;

        return [
            'item_type'   => 'product',
            'product_id'  => $product->id,
            'quantity'    => $item->quantity,
            'unit_price'  => $unitPrice,
            'total_price' => $unitPrice * $item->quantity,

            'snapshot' => [
                'title'   => $product->title,
                'image'   => $product->image,
                'price'   => $product->price,
                'size'    => $item->size ? [
                    'id'     => $item->size->id,
                    'name'   => $item->size->name,
                    'width'  => $item->size->width,
                    'height' => $item->size->height,
                    'price'  => $item->size->price,
                ] : null,
                'fabrics' => $fabrics->map(fn($f) => [
                    'id'    => $f->id,
                    'title' => $f->title,
                    'price' => $f->price,
                    'image' => $f->image,
                ])->toArray(),
                'attributes' => $attrs->map(fn($a) => [
                    'attribute_id'   => $a->category_attribute_id,
                    'attribute_name' => $a->attribute?->name,
                    'value_id'       => $a->category_value_id,
                    'value'          => $a->category_value_id
                        ? $a->categoryValue?->value
                        : $a->value,
                    'price'          => $a->categoryValue?->price ?? 0,
                ])->toArray(),
            ],

            'configuration' => [
                'size_id'    => $item->size_id,
                'fabric_ids' => $fabrics->pluck('id')->toArray(),
                'attributes' => $attrs->map(fn($a) => [
                    'attribute_id' => $a->category_attribute_id,
                    'value_id'     => $a->category_value_id,
                    'value'        => $a->value,
                ])->toArray(),
            ],
        ];
    }

    private function convertCustomProduct(CartItem $item, int $unitPrice): array
    {
        $itemConfig   = $item->configuration ?? [];
        $productItemId = $itemConfig['custom_product_item_id'] ?? null;
        $productItem  = $productItemId ? CustomProductItem::with(['category', 'customProduct', 'calculationProfile'])->find($productItemId) : null;

        $fabrics = $item->fabrics;
        $attrs   = $item->categoryValues;

        $breakdown = [];
        if ($productItem) {
            $breakdown = $this->strategyResolver->calculateForItem($item, $productItem);
        }

        return [
            'item_type'   => 'custom_product',
            'product_id'  => null,
            'quantity'    => $item->quantity,
            'unit_price'  => $unitPrice,
            'total_price' => $unitPrice * $item->quantity,

            'snapshot' => [
                'custom_product' => $productItem?->customProduct ? [
                    'id'   => $productItem->customProduct->id,
                    'name' => $productItem->customProduct->name,
                    'slug' => $productItem->customProduct->slug,
                ] : null,
                'item' => $productItem ? [
                    'id'   => $productItem->id,
                    'name' => $productItem->name,
                ] : null,
                'calculation_profile' => $productItem?->calculationProfile ? [
                    'id'            => $productItem->calculationProfile->id,
                    'name'          => $productItem->calculationProfile->name,
                    'strategy_type' => $productItem->calculationProfile->strategy_type,
                ] : null,
                'dimensions' => $itemConfig['dimensions'] ?? null,
                'fabrics'    => $fabrics->map(fn($f) => [
                    'id'       => $f->id,
                    'title'    => $f->title,
                    'price'    => $f->price,
                    'material' => $f->material,
                    'width'    => $f->width ?? null,
                    'image'    => $f->image,
                ])->toArray(),
                'attributes' => $attrs->map(fn($a) => [
                    'attribute_id'   => $a->category_attribute_id,
                    'attribute_name' => $a->attribute?->name,
                    'attribute_type' => $a->attribute?->type,
                    'value_id'       => $a->category_value_id,
                    'value'          => $a->category_value_id
                        ? $a->categoryValue?->value
                        : $a->value,
                    'price'          => $a->categoryValue?->price ?? 0,
                ])->toArray(),
                'fabric_consumption' => $breakdown['fabric_sections'] ?? [],
                'calculation'        => $breakdown,
                'rule_results'       => $itemConfig['rule_results'] ?? null,
            ],

            'configuration' => [
                'custom_product_id'     => $productItem?->custom_product_id,
                'custom_product_item_id' => $productItemId,
                'dimensions'            => $itemConfig['dimensions'] ?? null,
                'fabric_ids'            => $fabrics->pluck('id')->toArray(),
                'attributes'            => $attrs->map(fn($a) => [
                    'attribute_id' => $a->category_attribute_id,
                    'value_id'     => $a->category_value_id,
                    'value'        => $a->value,
                ])->toArray(),
            ],
        ];
    }
}
