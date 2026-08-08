<?php

namespace App\Http\Controllers\App\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Product\ProductsCollection;
use App\Http\Resources\Admin\Product\ProductsResource;
use App\Models\Product\Products;
use App\Traits\HttpResponses;

class FavoriteProductsController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $favorites = auth()->user()->favoriteProducts()->with('category', 'city')->get();
        return new ProductsCollection($favorites);
    }

    public function toggle(Products $product)
    {

        $user = auth()->user();
        if ($user->favoriteProducts()->where('products_id', $product->id)->exists()) {
            $user->favoriteProducts()->detach($product->id);
            return $this->success(null, 'محصول با موفقیت از علاقه مندی ها حذف شد');
        }

        $user->favoriteProducts()->attach($product->id);

        return $this->success(new ProductsResource($product), 'محصول با موفقیت به علاقه مندی ها اضافه شد');
    }
    public function ids()
    {

        $user = auth()->user();
        $ids = $user->favoriteProducts()->pluck('products.id');
        return response()->json($ids);
    }
}
