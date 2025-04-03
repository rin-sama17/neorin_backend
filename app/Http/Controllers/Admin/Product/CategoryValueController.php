<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryValueRequest;
use App\Http\Requests\Admin\UpdateCategoryValueRequest;
use App\Http\Resources\Admin\Product\CategoryValueCollection;
use App\Http\Resources\Admin\Product\CategoryValueResource;
use App\Models\Product\CategoryValue;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class CategoryValueController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new CategoryValueCollection(CategoryValue::all());
   }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryValueRequest $request)
    {
        $input = $request->all();
        $res =CategoryValue::create($input);
        return new CategoryValueResource($res);
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoryValue $category_value)
    {
        return new CategoryValueResource($category_value);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryValueRequest $request, CategoryValue $category_value)
    {
        $input = $request->all();
        $category_value->update($input);
        return new CategoryValueResource($category_value);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryValue $category_value)

    {
        $category_value->delete();
        return $this->success(null , "مقدار دسته بندی با موفقیت حذف شد");
    }
}
