<?php

use App\Http\Controllers\Admin\Content\PageController;
use App\Http\Controllers\Admin\Product\CategoryAttributeController;
use App\Http\Controllers\Admin\Product\CategoryController;
use App\Http\Controllers\Admin\Product\CategoryValueController;
use App\Http\Controllers\Admin\Product\ProductsController;
use App\Http\Controllers\Admin\Product\StateController;
use App\Http\Controllers\Admin\Setting\SettingController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\App\Home\CategoryController as HomeCategoryController;
use App\Http\Controllers\App\Home\PageController as HomePageController;
use App\Http\Controllers\App\Home\ProductsController as HomeProductsController;
use App\Http\Controllers\App\Panel\FavoriteProductsController;
use App\Http\Controllers\App\Panel\HistoryProductsController;
use App\Http\Controllers\App\Panel\ProductsController as PanelProductsController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [RegisteredUserController::class, 'verifyOtpAndRegister'])
    ->middleware('guest')
    ->name('register');
Route::post('/otp', [RegisteredUserController::class, 'sendOtp'])
    ->middleware('guest');

Route::get('categories', [HomeCategoryController::class, 'index'])->name('categories');
Route::get('pages', [HomePageController::class, 'index'])->name('pages');
Route::get('products', [HomeProductsController::class, 'index'])->name('products');



Route::prefix('admin')->name("admin.")->group(function () {
    Route::apiResource('setting', SettingController::class);

    Route::prefix('product')->name("product.")->group(function () {
        Route::apiResource('category', CategoryController::class);
        Route::apiResource('state', StateController::class);
        Route::apiResource('category-attribute', CategoryAttributeController::class);
        Route::apiResource('category-value', CategoryValueController::class);
        Route::apiResource('products', ProductsController::class);
    });


    Route::prefix('content')->name("content.")->group(function () {
        Route::apiResource('page', PageController::class);
    });

    Route::prefix('users')->name("users.")->group(function () {
        Route::apiResource('user', UserController::class);
    });
});




Route::prefix('panel')->name("panel.")->middleware(['auth:sanctum', 'mobileVerified'])->group(function () {
    Route::prefix('product')->name("product.")->group(function () {
        Route::apiResource('products', PanelProductsController::class);
    });

    Route::prefix('favorite')->name("favorite.")->group(function () {
        Route::get('/', [FavoriteProductsController::class, 'index'])->name('index');
        Route::post('/{product}', [FavoriteProductsController::class, 'store'])->name('store');
        Route::delete('/{product}', [FavoriteProductsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('history')->name("history.")->group(function () {
        Route::get('/', [HistoryProductsController::class, 'index'])->name('index');
    });
});
