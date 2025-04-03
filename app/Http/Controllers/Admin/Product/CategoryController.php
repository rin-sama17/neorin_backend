<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\Admin\Product\CategoryCollection;
use App\Http\Resources\Admin\Product\CategoryResource;
use App\Models\Product\Category;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new CategoryCollection(Category::all());
    }



    public function store(StoreCategoryRequest $request)
    {
        $input = $request->all();
        $category=Category::create($input);
        return  new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
      return  new CategoryResource($category);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request,Category $category)
    {
        $input = $request->all();
        $category->update($input);
        return  new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success(null,"دسته بندی با موفقیت حذف شد");
    }
}
