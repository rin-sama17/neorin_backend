<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountRequest;
use App\Http\Requests\Admin\UpdateDiscountRequest;
use App\Http\Resources\Admin\Product\DiscountCollection;
use App\Http\Resources\Admin\Product\DiscountResource;
use App\Models\Product\Discount;
use App\Traits\HttpResponses;

class DiscountController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return new DiscountCollection(Discount::all());
    }
    
    public function store(StoreDiscountRequest $request)
    {
        $input = $request->all();
        $discount = Discount::create($input);
        return new DiscountResource($discount);
    }

    public function update(UpdateDiscountRequest $request,Discount $discount)
    {
        $input = $request->all();
        $discount->update($input);
        return new DiscountResource($discount);
    }
    
    public function destroy(Discount $discount)
    {
        $discount->delete();
        return $this->success(null, 'تخفیف حذف شد');
    }
}
