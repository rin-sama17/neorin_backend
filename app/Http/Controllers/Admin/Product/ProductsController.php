<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdateProductsRequest;
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
    public function store(StoreProductsRequest $request, ImageService $imageService)
    {
        $input = $request->all();
        if($request->hasFile('image')){
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-images');
            $result = $imageService->createIndexAndSave($request->image);
         if($result){
            $input['image']= $result;
         }else{
            $this->error(null ,'خطا در ذخیره عکس',400);
         }
        };
        $product =Products::create($input);
        return new ProductsResource($product);
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
    public function update(UpdateProductsRequest $request, Products $product, ImageService $imageService)
    {
       $input = $request->all();

        if($request->hasFile('image')){
            if(!empty($product->image)) {
            $imageService->deleteDirectoryAndFiles($product->image['directory']);
            }
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-images');
            $result = $imageService->createIndexAndSave($request->image);

            if($result ==false) {
            return $this->error(null ,'خطا در فرایند اپلود عکس',400);
            }
            $input['image'] = $result;


        }else{
            if(isset($input['currentImage']) && !empty($product->image)){
                $image = $product->image;
                $image['currentImage'] = $input['currentImage'];
                $input['image'] = $image;
            }
        };

       $product->update($input);
       return new ProductsResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $product)
    {
      $product->delete();
      return $this->success(null,"محصول با موفقیت حذف شد");
    }
}
