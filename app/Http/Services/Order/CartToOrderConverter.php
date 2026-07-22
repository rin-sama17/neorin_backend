<?php

namespace App\Http\Services\Order;

use App\Models\Shop\CartItem;

class CartToOrderConverter
{
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
            'item_type'  => 'product',
            'product_id' => $product->id,
            'quantity'   => $item->quantity,
            'unit_price' => $unitPrice,
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
        $fabrics = $item->fabrics;
        $attrs   = $item->categoryValues;

        return [
            'item_type'   => 'custom_product',
            'product_id'  => null,
            'quantity'    => $item->quantity,
            'unit_price'  => $unitPrice,
            'total_price' => $unitPrice * $item->quantity,

            'snapshot' => [
                'title'   => 'سفارش اختصاصی',
                'fabrics' => $fabrics->map(fn($f) => [
                    'id'    => $f->id,
                    'title' => $f->title,
                    'price' => $f->price,
                ])->toArray(),
            ],

            'configuration' => [
                'fabric_ids' => $fabrics->pluck('id')->toArray(),
                'attributes' => $attrs->map(fn($a) => [
                    'attribute_id' => $a->category_attribute_id,
                    'value_id'     => $a->category_value_id,
                    'value'        => $a->value,
                ])->toArray(),
            ],
        ];
    }
}
