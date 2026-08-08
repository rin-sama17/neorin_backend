<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFormulaRequest;
use App\Http\Requests\Admin\UpdateFormulaRequest;
use App\Http\Resources\Admin\Product\FormulaResource;
use App\Models\Product\CalculationProfile;
use App\Models\Product\Formula;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class FormulaController extends Controller
{
    use HttpResponses;

    public function index(CalculationProfile $calculationProfile)
    {
        return FormulaResource::collection(
            $calculationProfile->formulas()->orderBy('priority')->get()
        );
    }

    public function store(StoreFormulaRequest $request, CalculationProfile $calculationProfile): FormulaResource
    {
        $formula = $calculationProfile->formulas()->create($request->validated());

        return new FormulaResource($formula);
    }

    public function show(CalculationProfile $calculationProfile, Formula $formula): FormulaResource
    {
        return new FormulaResource($formula);
    }

    public function update(UpdateFormulaRequest $request, CalculationProfile $calculationProfile, Formula $formula): FormulaResource
    {
        $formula->update($request->validated());

        return new FormulaResource($formula);
    }

    public function destroy(CalculationProfile $calculationProfile, Formula $formula): JsonResponse
    {
        $formula->delete();
        return $this->success(null, 'فرمول با موفقیت حذف شد');
    }
}
