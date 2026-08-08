<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomProductItemRequest;
use App\Http\Requests\Admin\UpdateCustomProductItemRequest;
use App\Http\Resources\Admin\Product\CustomProductItemResource;
use App\Models\Product\CustomProduct;
use App\Models\Product\CustomProductItem;
use App\Traits\HttpResponses;

class CustomProductItemController extends Controller
{
    use HttpResponses;

    public function index(CustomProduct $customProduct)
    {
        return CustomProductItemResource::collection(
            $customProduct->items()->with('category', 'calculationProfile', 'rules')->orderBy('sort')->get()
        );
    }

    public function store(StoreCustomProductItemRequest $request, CustomProduct $customProduct)
    {
        $item = $customProduct->items()->create($request->validated());

        return new CustomProductItemResource($item->load('category', 'calculationProfile', 'rules'));
    }

    public function show(CustomProduct $customProduct, CustomProductItem $customProductItem)
    {
        return new CustomProductItemResource(
            $customProductItem->load('category', 'calculationProfile', 'rules.childRules')
        );
    }

    public function update(UpdateCustomProductItemRequest $request, CustomProduct $customProduct, CustomProductItem $customProductItem)
    {
        $customProductItem->update($request->validated());

        return new CustomProductItemResource($customProductItem->load('category', 'calculationProfile', 'rules'));
    }

    public function destroy(CustomProduct $customProduct, CustomProductItem $customProductItem)
    {
        $customProductItem->delete();
        return $this->success(null, 'آیتم محصول اختصاصی با موفقیت حذف شد');
    }
}
