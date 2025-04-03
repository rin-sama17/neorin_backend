<?php

use App\Http\Controllers\Admin\Content\PageController;
use App\Http\Controllers\Admin\Product\CategoryAttributeController;
use App\Http\Controllers\Admin\Product\CategoryController;
use App\Http\Controllers\Admin\Product\CategoryValueController;
use App\Http\Controllers\Admin\Product\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('admin')->name("admin.")->group(function(){
    Route::prefix('product')->name("product.")->group(function(){
        Route::apiResource('category', CategoryController::class);
        Route::apiResource('category-attribute', CategoryAttributeController::class);
        Route::apiResource('category-value', CategoryValueController::class);
        Route::apiResource('products', ProductsController::class);
    });
});



Route::prefix('content')->name("content.")->group(function(){
    Route::apiResource('page', PageController::class);
});
