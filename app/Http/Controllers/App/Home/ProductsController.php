<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\App\Panel\HistoryProductsController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Home\ProductListResource;
use App\Http\Resources\Home\ProductsCollection;
use App\Http\Resources\Home\ProductsResource;
use App\Models\Product\Category;
use App\Models\Product\CategoryAttribute;
use App\Models\Product\Color;
use App\Models\Product\Fabric;
use App\Models\Product\Products;
use App\Models\Product\Size;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function index(Request $r)
    {
        $query = Products::query()
            ->with(['gallery', 'discounts', 'category', 'sizes', 'fabrics'])
            ->where('status', 1);
        if ($r->filled('search')) {
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$r->search}%")
                ->orWhere('description', 'like', "%{$r->search}%"));
        }

        if ($r->filled('categories')) {
            $query->whereIn('category_id', $r->categories);
        }

        if ($r->filled('sizes')) {
            $ids = $r->sizes;
            $query->whereHas('sizes', fn($q) => $q->whereIn('sizes.id', $ids));
        }

        if ($r->filled('colors')) {
            $ids = $r->colors;
            $query->whereHas('colors', fn($q) => $q->whereIn('colors.id', $ids));
        }

        if ($r->filled('fabrics')) {
            $ids = $r->fabrics;
            $query->whereHas('fabrics', fn($q) => $q->whereIn('fabrics.id', $ids));
        }

        // dynamic attributes: attributes[1][]=2&attributes[1][]=9
        foreach ($r->input('attributes', []) as $attributeId => $valueIds) {
            $query->whereHas('categoryValues', fn($q) => $q
                ->where('category_attribute_id', $attributeId)
                ->whereIn('category_attribute_value_id', $valueIds));
        }

        if ($r->boolean('available')) {
            $query->where('stock', '>', 0);
        }
        if ($r->boolean('special')) {
            $query->where('is_special', 1);
        }
        if ($r->boolean('has_discount')) {
            $query->whereHas('discount');
        }

        if ($r->filled('min_price')) $query->where('price', '>=', $r->min_price);
        if ($r->filled('max_price')) $query->where('price', '<=', $r->max_price);

        match ($r->get('sort', 'newest')) {
            'oldest'       => $query->oldest(),
            'most_viewed'  => $query->orderByDesc('view'),
            'best_selling' => $query->withCount('orderItems')->orderByDesc('order_items_count'),
            'price_asc'    => $query->orderBy('price'),
            'price_desc'   => $query->orderByDesc('price'),
            'discount'     => $query->whereHas('discount')->orderByDesc('discount_id'), // adjust to real discount % if stored
            default        => $query->latest(),
        };

        $products = $query->paginate($r->get('per_page', 12));

        return ProductListResource::collection($products)->response()->getData();
    }

    public function filters()
    {
        return response()->json([
            'categories' => Category::select('id', 'name')->get(),

            'sizes' => Size::select('width', 'height')
                ->distinct()
                ->orderBy('width')
                ->get()
                ->map(fn($s, $i) => ['id' => $i, 'width' => $s->width, 'height' => $s->height]),
            'colors' => Color::select('id', 'name', 'hex')->get(),

            'fabrics' => Fabric::select('id', 'title', 'image', 'price')->get()->map(fn($f) => [
                'id' => $f->id,
                'title' => $f->title,
                'image' => $f->image['indexArray']['small'] ?? null,
            ]),

            'attributes' => CategoryAttribute::with('categoryValues')->get(),

            'price_range' => [
                'min' => Products::min('price') ?? 0,
                'max' => Products::max('price') ?? 0,
            ],
        ]);
    }


    public function show(Products $product)
    {
        $product->increment('view');

        if (auth()->check()) {
            $historyController = new HistoryProductsController();
            $historyController->store($product);
        }

        $product = Products::with([
            'category.parent.attributes.categoryValues',
            'category.attributes.categoryValues',
            'sizes',
            'gallery',
            'colors',
            'fabrics.colors',
            'categoryValues',
            'city',
            'user',
        ])->find($product->id);

        return new ProductsResource($product);
    }
}
