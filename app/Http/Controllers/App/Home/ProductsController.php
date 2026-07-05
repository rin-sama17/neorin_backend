<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\App\Panel\HistoryProductsController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Home\ProductsCollection;
use App\Http\Resources\Home\ProductsResource;
use App\Models\Product\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        return new ProductsCollection(Products::all());
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
//   public function show(Products $product)
//     {
//         $product->increment('view');

//         if (auth()->check()) {
//             $historyController = new HistoryProductsController();
//             $historyController->store($product);
//         }

//         $product = Products::with('category.parent', 'gallery', 'category.attributes', 'categoryValues')->find($product->id);
//         return new ProductsResource($product);
//     }
}
