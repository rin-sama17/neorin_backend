<?php

namespace App\Http\Controllers\App\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Product\ProductsCollection;
use App\Http\Resources\Admin\Product\ProductsResource;
use App\Models\Product\Products;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class FavoriteProductsController extends Controller
{
        use HttpResponses ;

    public function index(){
        $favorites = auth()->user()->favoriteProducts()->with('category', 'city')->get();
        return new ProductsCollection($favorites);
    }

    public function store(Products $product){

        $user = auth()->user();
        if($user->favoriteProducts()->where('products_id', $product->id)->exists()){
            return $this->error(null , 'این محصول قبلا نشان شده است', 400);
        }

        $user->favoriteProducts()->attach($product->id);

        return $this->success(new ProductsResource($product),'محصول با موفقیت به علاقه مندی ها اضافه شد');
    }


    public function destroy(Products $product){
        $user = auth()->user();

        if(!$user->favoriteProducts()->where('products_id', $product->id)->exists()){
            return $this->error(null , 'این محصول در بخش نشان شده ها نیست', 400);
        }

        $user->favoriteProducts()->detach($product->id);
        return $this->success(null,'محصول با موفقیت از علاقه مندی ها حذف شد');

    }
}
