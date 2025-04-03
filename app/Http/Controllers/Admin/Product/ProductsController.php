<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Http\Resources\Admin\Product\ProductsCollection;
use App\Http\Resources\Admin\Product\ProductsResource;
use App\Models\Product\Products;
use App\Http\Services\Image\ImageService;
use App\Traits\HttpResponses;

class ProductsController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return new ProductsCollection(Products::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductsRequest $request, ImageService $image_service)
    {
        $input = $request->all();
        if($request->hasFile('image')){
            $savedImagePath = $image_service->save($request['image']);
         if($savedImagePath){
            $input['image']= $savedImagePath;
         }else{
            $this->error(null ,'خطا در ذخیره عکس',400);
         }
        };
        $res =Products::create($input);
        return new ProductsResource($res);
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $product)
    {
        return new ProductsResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, Products $product)
    {
       $input = $request->all();
       $product->update($input);
       return new ProductsResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $id)
    {
        //
    }
}
