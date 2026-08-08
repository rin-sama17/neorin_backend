<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCalculationProfileRequest;
use App\Http\Requests\Admin\UpdateCalculationProfileRequest;
use App\Http\Resources\Admin\Product\CalculationProfileCollection;
use App\Http\Resources\Admin\Product\CalculationProfileResource;
use App\Models\Product\CalculationProfile;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class CalculationProfileController extends Controller
{
    use HttpResponses;

    public function index(): CalculationProfileCollection
    {
        return new CalculationProfileCollection(
            CalculationProfile::with('formulas')->latest()->get()
        );
    }

    public function store(StoreCalculationProfileRequest $request): CalculationProfileResource
    {
        $profile = CalculationProfile::create($request->validated());

        return new CalculationProfileResource($profile->load('formulas'));
    }

    public function show(CalculationProfile $calculationProfile): CalculationProfileResource
    {
        return new CalculationProfileResource(
            $calculationProfile->load('formulas')
        );
    }

    public function update(UpdateCalculationProfileRequest $request, CalculationProfile $calculationProfile): CalculationProfileResource
    {
        $calculationProfile->update($request->validated());

        return new CalculationProfileResource($calculationProfile->load('formulas'));
    }

    public function destroy(CalculationProfile $calculationProfile): JsonResponse
    {
        $calculationProfile->delete();
        return $this->success(null, 'پروفایل محاسباتی با موفقیت حذف شد');
    }
}
