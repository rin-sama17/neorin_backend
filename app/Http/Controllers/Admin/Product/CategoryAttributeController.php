<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryAttributeRequest;
use App\Http\Requests\Admin\UpdateCategoryAttributeRequest;
use App\Http\Resources\Admin\Product\CategoryAttributeCollection;
use App\Http\Resources\Admin\Product\CategoryAttributeResource;
use App\Models\Product\CategoryAttribute;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class CategoryAttributeController extends Controller
{
      use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new CategoryAttributeCollection(CategoryAttribute::all());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryAttributeRequest $request)
    {
        $input = $request->all();
        $res =CategoryAttribute::create($input);
        return new CategoryAttributeResource($res);
    }

    /**
     * Display the specified resource wich i never gonna use but just for fun :).
     */
    public function show(CategoryAttribute $category_attribute)
    {
        return new CategoryAttributeResource($category_attribute);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryAttributeRequest $request, CategoryAttribute $category_attribute)
    {
        $input = $request->all();
        $category_attribute->update($input);
        return new CategoryAttributeResource($category_attribute);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryAttribute $category_attribute)
    {
        $category_attribute->delete();
        return $this->success(null, 'ویژگی دسته بندی با موفقیت حذف شد');
    }
}
