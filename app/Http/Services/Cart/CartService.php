<?php

namespace App\Http\Services\Cart;

use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreate(Request $request): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        return Cart::firstOrCreate(['session_id' => $request->session()->getId()]);
    }

    private function makeSlug(array $data): string
    {
        $productId = $data['product_id'] ?? 'custom';
        $sizeId    = $data['size_id']    ?? '0';

        $fabrics = collect($data['fabric_ids'] ?? [])->sort()->values()->join('-');

        $attrs = collect($data['attributes'] ?? [])
            ->sortBy('attribute_id')
            ->map(fn($a) => $a['attribute_id'] . ':' . ($a['value_id'] ?? $a['value'] ?? ''))
            ->join('|');

        return implode('_', array_filter([
            'p' . $productId,
            's' . $sizeId,
            $fabrics ? 'f' . $fabrics : null,
            $attrs   ? 'a' . $attrs   : null,
        ]));
    }

    public function add(Cart $cart, array $data): CartItem
    {
        $slug = $this->makeSlug($data);

        // duplicate check بر اساس slug
        $existing = $cart->items()->where('slug', $slug)->first();
        if ($existing) {
            return $this->updateQuantity($existing, $existing->quantity + $data['quantity']);
        }

        $item = $cart->items()->create([
            'item_type'  => $data['item_type'],
            'product_id' => $data['product_id'] ?? null,
            'size_id'    => $data['size_id']    ?? null,
            'quantity'   => $data['quantity'],
            'slug'       => $slug,
        ]);
        // $item->fabrics()->sync($data['fabric_ids'] ?? []);
        foreach ($data['attributes'] ?? [] as $attr) {
            $item->categoryValues()->create([
                'category_attribute_id' => $attr['attribute_id'],
                'category_value_id'     => $attr['value_id'] ?? null,
                'value'                 => $attr['value']    ?? null,
            ]);
        }

        return $item->load([
            'product',
            'size',
            'categoryValues.attribute',
            'categoryValues.categoryValue',
        ]);
    }

    public function addMany(Cart $cart, array $items): array
    {
        $added  = [];
        $failed = [];

        DB::transaction(function () use ($cart, $items, &$added, &$failed) {
            foreach ($items as $index => $item) {
                try {
                    $added[] = $this->add($cart, $item);
                } catch (\Exception $e) {
                    $failed[$index] = $e->getMessage();
                }
            }
        });

        return compact('added', 'failed');
    }

    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            $item->delete();
            return $item;
        }
        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function summary(Cart $cart): array
    {
        $items = $cart->items()->with([
            'product',
            'size',
            'categoryValues.attribute',
            'categoryValues.categoryValue',
        ])->get();

        $calculated = $items->map(function ($item) {
            $basePrice   = $item->size?->price ?? $item->product?->price ?? 0;
            $attrPrice   = $item->categoryValues
                ->filter(fn($a) => $a->category_value_id !== null)
                ->sum(fn($a)    => $a->categoryValue?->price ?? 0);

            $unitPrice = $basePrice   + $attrPrice;

            return [
                'id'        => $item->id,
                'slug'      => $item->slug,
                'item_type' => $item->item_type,
                'product'   => $item->product ? [
                    'id'    => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image,
                    'price'  => $item->product->price,
                ] : null,
                'size' => $item->size ? [
                    'id'     => $item->size->id,
                    'name'   => $item->size->name,
                    'width'  => $item->size->width,
                    'height' => $item->size->height,
                    'price'  => $item->size->price,
                ] : null,

                'attributes' => $item->categoryValues->map(fn($a) => [
                    'attribute_id'   => $a->category_attribute_id,
                    'attribute_name' => $a->attribute?->name,
                    'value_id'       => $a->category_value_id,
                    'value'          => $a->category_value_id
                        ? $a->categoryValue?->value
                        : $a->value,
                    'price'          => $a->categoryValue?->price ?? 0,
                ]),
                'quantity'    => $item->quantity,
                'unit_price'  => $unitPrice,
                'total_price' => $unitPrice * $item->quantity,
            ];
        });

        $subtotal = $calculated->sum('total_price');

        return [
            'items'    => $calculated->values(),
            'subtotal' => $subtotal,
            'total'    => $subtotal,
            'count'    => $items->sum('quantity'),
        ];
    }
}
