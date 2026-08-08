<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomProductRuleRequest;
use App\Http\Requests\Admin\UpdateCustomProductRuleRequest;
use App\Http\Resources\Admin\Product\CustomProductRuleResource;
use App\Models\Product\CustomProduct;
use App\Models\Product\CustomProductItem;
use App\Models\Product\CustomProductRule;
use App\Traits\HttpResponses;

class CustomProductRuleController extends Controller
{
    use HttpResponses;

    public function index(CustomProduct $customProduct, CustomProductItem $customProductItem)
    {
        return CustomProductRuleResource::collection(
            $customProductItem->rules()
                ->with('childRules')
                ->orderBy('priority')
                ->get()
        );
    }

    public function store(StoreCustomProductRuleRequest $request, CustomProduct $customProduct, CustomProductItem $customProductItem)
    {
        $rule = $customProductItem->rules()->create($request->validated());

        return new CustomProductRuleResource($rule->load('childRules'));
    }

    public function show(CustomProduct $customProduct, CustomProductItem $customProductItem, CustomProductRule $customProductRule)
    {
        return new CustomProductRuleResource(
            $customProductRule->load('childRules', 'parentRule')
        );
    }

    public function update(UpdateCustomProductRuleRequest $request, CustomProduct $customProduct, CustomProductItem $customProductItem, CustomProductRule $customProductRule)
    {
        $customProductRule->update($request->validated());

        return new CustomProductRuleResource($customProductRule->load('childRules'));
    }

    public function destroy(CustomProduct $customProduct, CustomProductItem $customProductItem, CustomProductRule $customProductRule)
    {
        $customProductRule->delete();
        return $this->success(null, 'قانون با موفقیت حذف شد');
    }
}
