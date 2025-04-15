<?php

namespace App\Http\Controllers\App\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Product\ProductsCollection;
use App\Http\Resources\Admin\Product\ProductsResource;
use App\Models\Product\Products;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class HistoryProductsController extends Controller
{
  use HttpResponses;

  public function index()
  {
    $viewedProducts = auth()->user()->viewedProducts()->with('city', 'category')->get();
    return new ProductsCollection($viewedProducts);
  }

  public function store(Products $product)
  {
    $user = auth()->user();
    if ($user->viewedProducts()->where('products_id', $product->id)->exists()) {
      $user->viewedProducts()->updateExistingPivot($product->id, ['updated_at' => now()]);
    } else {
      $user->viewedProducts()->attach($product->id);
    }
  }
}
