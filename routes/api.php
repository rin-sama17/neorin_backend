<?php

use App\Http\Controllers\Admin\Content\PageController;
use App\Http\Controllers\Admin\Content\SliderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\Product\CalculationProfileController;
use App\Http\Controllers\Admin\Product\CategoryAttributeController;
use App\Http\Controllers\Admin\Product\CategoryController;
use App\Http\Controllers\Admin\Product\CategoryValueController;
use App\Http\Controllers\Admin\Product\ColorController;
use App\Http\Controllers\Admin\Product\CustomProductController;
use App\Http\Controllers\Admin\Product\CustomProductItemController;
use App\Http\Controllers\Admin\Product\CustomProductRuleController;
use App\Http\Controllers\Admin\Product\DiscountController;
use App\Http\Controllers\Admin\Product\FabricController;
use App\Http\Controllers\Admin\Product\FormulaController;
use App\Http\Controllers\App\Home\FabricController as HomeFabricController;
use App\Http\Controllers\Admin\Product\GalleryController;
use App\Http\Controllers\Admin\Product\ProductsController;
use App\Http\Controllers\Admin\Product\SizeController;
use App\Http\Controllers\Admin\Product\StateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\Setting\SettingController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\App\CartController;
use App\Http\Controllers\App\Home\CategoryController as HomeCategoryController;
use App\Http\Controllers\App\Home\CityController;
use App\Http\Controllers\App\Home\CustomProductController as HomeCustomProductController;
use App\Http\Controllers\App\Home\PageController as HomePageController;
use App\Http\Controllers\App\Home\ProductsController as HomeProductsController;
use App\Http\Controllers\App\Home\SliderController as HomeSliderController;
use App\Http\Controllers\App\Home\StateController as HomeStateController;
use App\Http\Controllers\App\OrderController;
use App\Http\Controllers\App\Panel\AddressController;
use App\Http\Controllers\App\Panel\FavoriteProductsController;
use App\Http\Controllers\App\Panel\GalleryController as PanelGalleryController;
use App\Http\Controllers\App\Panel\HistoryProductsController;
use App\Http\Controllers\App\Panel\ProductsController as PanelProductsController;
use App\Http\Controllers\App\PaymentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('cart')->group(function () {
    Route::get('/',          [CartController::class, 'index']);
    Route::post('/add',      [CartController::class, 'add']);
    Route::post('/add-many', [CartController::class, 'addMany']);
    Route::patch('/{item}',  [CartController::class, 'update']);
    Route::delete('/{item}', [CartController::class, 'remove']);
    Route::delete('/',       [CartController::class, 'clear']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('favorite')->name("favorite.")->group(function () {
        Route::get('/', [FavoriteProductsController::class, 'index'])->name('index');
        Route::get('/ids', [FavoriteProductsController::class, 'ids']);
        Route::post('/{product}', [FavoriteProductsController::class, 'toggle'])->name('toggle');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/',          [OrderController::class, 'index']);
        Route::get('/{id}',      [OrderController::class, 'show']);
        Route::post('/checkout', [OrderController::class, 'checkout']);
    });

    Route::prefix('payment')->group(function () {
        Route::get('/create', [PaymentController::class, 'createPayment'])->name('payment.create');
        Route::get('/verify',         [PaymentController::class, 'verifyPayment'])->name('payment.verify');
        Route::get('/{id}',         [PaymentController::class, 'getPayment'])->name('payment.get');
    });
});

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user()->load('roles.permissions');;
});


Route::get('categories', [HomeCategoryController::class, 'index'])->name('categories');
Route::get('all-categories', [HomeCategoryController::class, 'showAll'])->name('all-categories');
Route::get('sliders', [HomeSliderController::class, 'index'])->name('sliders');
Route::get('pages', [HomePageController::class, 'index'])->name('pages');
Route::get('products', [HomeProductsController::class, 'index'])->name('products');

Route::get('/products/filters', [HomeProductsController::class, 'filters']);
Route::get('fabrics', [HomeFabricController::class, 'index'])->name('products');
Route::get('fabrics/{fabric}', [HomeFabricController::class, 'show'])->name('fabric');
Route::get('products/{product}', [HomeProductsController::class, 'show'])->name('product');
Route::get('states', [HomeStateController::class, 'index'])->name('states');

Route::get('cities', [CityController::class, 'index'])->name('index');
Route::get('cities/{city}', [CityController::class, 'show'])->name('show');


Route::prefix('custom-products')->name('custom-products.')->group(function () {
    Route::get('/', [HomeCustomProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [HomeCustomProductController::class, 'show'])->name('show');
    Route::get('/item/{itemId}', [HomeCustomProductController::class, 'showItem'])->name('item');
    Route::post('/rules', [HomeCustomProductController::class, 'evaluateRules'])->name('rules');
    Route::post('/rules/all', [HomeCustomProductController::class, 'evaluateAllRules'])->name('rules.all');
    Route::post('/calculate', [HomeCustomProductController::class, 'calculate'])->name('calculate');
});


Route::prefix('admin')->name("admin.")->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('setting', SettingController::class)->middleware('permission:settings.manage');
    Route::apiResource('roles', RoleController::class)->middleware('permission:roles');
    Route::apiResource('permission', PermissionController::class)->middleware('permission:permissions');

    Route::apiResource('custom-products', CustomProductController::class)->middleware('permission:custom-products');

    Route::apiResource('custom-products.custom-product-items', CustomProductItemController::class)->middleware('permission:custom-products.manage-items');

    Route::apiResource('custom-products.custom-product-items.custom-product-rules', CustomProductRuleController::class)->middleware('permission:custom-products.manage-rules');

    Route::apiResource('calculation-profiles', CalculationProfileController::class)->middleware('permission:calculation-profiles');

    Route::apiResource('calculation-profiles.formulas', FormulaController::class)->middleware('permission:formulas');

    Route::prefix('product')->name("product.")->group(function () {
        Route::apiResource('category', CategoryController::class)->middleware('permission:categories');
        Route::apiResource('discounts', DiscountController::class)->middleware('permission:discounts');
        Route::apiResource('colors', ColorController::class)->middleware('permission:colors');
        Route::apiResource('sizes', SizeController::class)->middleware('permission:sizes');
        Route::apiResource('state', StateController::class)->middleware('permission:states');
        Route::apiResource('category-attribute', CategoryAttributeController::class)->middleware('permission:category-attributes');
        Route::apiResource('category-value', CategoryValueController::class)->middleware('permission:category-values');
        Route::apiResource('products', ProductsController::class)->middleware('permission:products');
        Route::apiResource('fabrics', FabricController::class)->middleware('permission:fabrics');
        Route::apiResource('gallery', GalleryController::class)->middleware('permission:galleries');
    });


    Route::prefix('content')->name("content.")->group(function () {
        Route::apiResource('page', PageController::class)->middleware('permission:pages');
        Route::apiResource('slider', SliderController::class)->middleware('permission:sliders');
    });

    Route::prefix('users')->name("users.")->group(function () {
        Route::apiResource('user', UserController::class)->middleware('permission:users');
    });
});




Route::prefix('panel')->name("panel.")->middleware(['auth:sanctum', 'mobileVerified'])->group(function () {
    Route::prefix('product')->name("product.")->group(function () {
        Route::apiResource('products', PanelProductsController::class);
    });
    Route::apiResource('addresses', AddressController::class);
    Route::patch('addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    Route::prefix('gallery')->name("gallery.")->group(function () {
        Route::get('{product}/', [PanelGalleryController::class, 'index'])->name('index');
        Route::post('store/{product}', [PanelGalleryController::class, 'store'])->name('store');
        Route::get('show/{gallery}', [PanelGalleryController::class, 'show'])->name('show');
        Route::put('update/{gallery}', [PanelGalleryController::class, 'update'])->name('update');
        Route::delete('/{gallery}', [PanelGalleryController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('history')->name("history.")->group(function () {
        Route::get('/', [HistoryProductsController::class, 'index'])->name('index');
    });
});
