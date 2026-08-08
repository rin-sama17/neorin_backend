<?php

namespace App\Http\Services\Cart;

use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Product\CustomProduct;
use App\Models\Product\CustomProductItem;
use App\Services\Order\Calculation\StrategyResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private StrategyResolver $strategyResolver,
    ) {}

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
        $itemId    = $data['custom_product_item_id'] ?? '';

        $fabrics = collect($data['fabric_ids'] ?? [])->sort()->values()->join('-');

        $attrs = collect($data['attributes'] ?? [])
            ->sortBy('attribute_id')
            ->map(fn($a) => $a['attribute_id'] . ':' . ($a['value_id'] ?? $a['value'] ?? ''))
            ->join('|');

        $dims = '';
        if (!empty($data['dimensions'])) {
            $dims = collect($data['dimensions'])
                ->sortKeys()
                ->map(fn($v, $k) => $k . ':' . $v)
                ->join(',');
        }

        return implode('_', array_filter([
            'p' . $productId,
            's' . $sizeId,
            $itemId ? 'i' . $itemId : null,
            $fabrics ? 'f' . $fabrics : null,
            $attrs   ? 'a' . $attrs   : null,
            $dims    ? 'd' . $dims    : null,
        ]));
    }

    public function add(Cart $cart, array $data): CartItem
    {
        $slug = $this->makeSlug($data);

        $existing = $cart->items()->where('slug', $slug)->first();
        if ($existing) {
            return $this->updateQuantity($existing, $existing->quantity + $data['quantity']);
        }

        $configuration = $data['configuration'] ?? [];
        if (!empty($data['custom_product_id'])) {
            $configuration['custom_product_id'] = $data['custom_product_id'];
        }
        if (!empty($data['custom_product_item_id'])) {
            $configuration['custom_product_item_id'] = $data['custom_product_item_id'];
        }
        if (!empty($data['dimensions'])) {
            $configuration['dimensions'] = $data['dimensions'];
        }

        $item = $cart->items()->create([
            'item_type'     => $data['item_type'],
            'product_id'    => $data['product_id'] ?? null,
            'size_id'       => $data['size_id']    ?? null,
            'quantity'      => $data['quantity'],
            'slug'          => $slug,
            'configuration' => !empty($configuration) ? $configuration : null,
        ]);

        $item->fabrics()->sync($data['fabric_ids'] ?? []);

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
            'fabrics',
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
            'fabrics',
            'categoryValues.attribute',
            'categoryValues.categoryValue',
        ])->get();

        $calculated = $items->map(function ($item) {
            $itemData = [
                'id'        => $item->id,
                'slug'      => $item->slug,
                'item_type' => $item->item_type,
                'product'   => $item->product ? [
                    'id'    => $item->product->id,
                    'title' => $item->product->title,
                    'image' => $item->product->image,
                    'price' => $item->product->price,
                ] : null,
                'custom_product'  => null,
                'custom_item'     => null,
                'dimensions'      => $item->configuration['dimensions'] ?? null,
                'size' => $item->size ? [
                    'id'     => $item->size->id,
                    'name'   => $item->size->name,
                    'width'  => $item->size->width,
                    'height' => $item->size->height,
                    'price'  => $item->size->price,
                ] : null,
                'fabrics' => $item->fabrics->map(fn($f) => [
                    'id'    => $f->id,
                    'title' => $f->title,
                    'price' => $f->price,
                    'image' => $f->image,
                ]),
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
                'unit_price'  => 0,
                'total_price' => 0,
            ];

            if ($item->item_type === 'custom_product') {
                $config = $item->configuration ?? [];
                $productItemId = $config['custom_product_item_id'] ?? null;

                if ($productItemId) {
                    $productItem = CustomProductItem::with(['customProduct', 'calculationProfile'])->find($productItemId);
                    if ($productItem) {
                        $itemData['custom_item'] = [
                            'id'   => $productItem->id,
                            'name' => $productItem->name,
                        ];
                        $itemData['custom_product'] = $productItem->customProduct ? [
                            'id'   => $productItem->customProduct->id,
                            'name' => $productItem->customProduct->name,
                            'slug' => $productItem->customProduct->slug,
                        ] : null;
                        $itemData['calculation_profile'] = $productItem->calculationProfile ? [
                            'id'            => $productItem->calculationProfile->id,
                            'name'          => $productItem->calculationProfile->name,
                            'strategy_type' => $productItem->calculationProfile->strategy_type,
                        ] : null;

                        $breakdown = $this->strategyResolver->calculateForItem($item, $productItem);
                        $unitPrice = $breakdown['total'] ?? 0;
                        $itemData['unit_price']  = $unitPrice;
                        $itemData['total_price'] = $unitPrice * $item->quantity;
                        $itemData['breakdown']   = $breakdown;
                    }
                }
            } else {
                $basePrice = $item->size?->price ?? $item->product?->price ?? 0;
                $fabricPrice = $item->fabrics->sum('price');
                $attrPrice = $item->categoryValues
                    ->filter(fn($a) => $a->category_value_id !== null)
                    ->sum(fn($a) => $a->categoryValue?->price ?? 0);

                $unitPrice = $basePrice + $fabricPrice + $attrPrice;
                $itemData['unit_price']  = $unitPrice;
                $itemData['total_price'] = $unitPrice * $item->quantity;
            }

            return $itemData;
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
