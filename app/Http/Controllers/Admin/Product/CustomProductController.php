<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomProductRequest;
use App\Http\Requests\Admin\UpdateCustomProductRequest;
use App\Http\Resources\Admin\Product\CustomProductCollection;
use App\Http\Resources\Admin\Product\CustomProductResource;
use App\Models\Product\CustomProduct;
use App\Traits\HttpResponses;

class CustomProductController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return new CustomProductCollection(
            CustomProduct::with('items.category', 'items.calculationProfile')->latest()->get()
        );
    }

    public function store(StoreCustomProductRequest $request)
    {
        $product = CustomProduct::create($request->validated());

        return new CustomProductResource($product->load('items.category', 'items.calculationProfile'));
    }

    public function show(CustomProduct $customProduct)
    {
        return new CustomProductResource(
            $customProduct->load('items.category', 'items.calculationProfile', 'items.rules.childRules')
        );
    }

    public function update(UpdateCustomProductRequest $request, CustomProduct $customProduct)
    {
        $customProduct->update($request->validated());

        return new CustomProductResource($customProduct->load('items.category', 'items.calculationProfile'));
    }

    public function destroy(CustomProduct $customProduct)
    {
        $customProduct->delete();
        return $this->success(null, 'محصول اختصاصی با موفقیت حذف شد');
    }
}
