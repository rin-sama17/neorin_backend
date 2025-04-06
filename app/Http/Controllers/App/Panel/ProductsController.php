<?php

namespace App\Http\Controllers\App\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\StoreProductRequest;
use App\Http\Requests\Panel\UpdateProductRequest;
use App\Http\Resources\Admin\Product\ProductsCollection;
use App\Http\Resources\Admin\Product\ProductsResource;
use App\Http\Services\Image\ImageService;
use App\Models\Product\Products;
use App\Traits\HttpResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    use HttpResponses ,AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ProductsCollection(auth()->user()->products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, ImageService $imageService)
    {
        $input = [
            'title' => $request->title,
            'description'=>$request->description,
            'ads_type'=>$request->ads_type,
            'ads_status'=>$request->ads_status,
            'category_id'=>$request->category_id,
            'city_id'=>$request->city_id,
            'contact'=>$request->contact,
            'image'=>$request->image,
            'price'=>$request->price,
            'tags'=>$request->tags,
            'lat'=>$request->lat,
            'lng'=>$request->lng,
            'willing_to_trade'=>$request->willing_to_trade?$request->willing_to_trade :0,
            'user_id'=>auth()->user()->id,
            'status'=> 3,
        ];
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
        $this->authorize('view', $product);
        return new ProductsResource($product);
    }

  public function update(UpdateProductRequest $request, Products $product, ImageService $imageService)
    {
          $this->authorize('update', $product);
        $input = [
            'title' => $request->title,
            'description'=>$request->description,
            'ads_type'=>$request->ads_type,
            'ads_status'=>$request->ads_status,
            'category_id'=>$request->category_id,
            'city_id'=>$request->city_id,
            'contact'=>$request->contact,
            'image'=>$request->image,
            'price'=>$request->price,
            'tags'=>$request->tags,
            'lat'=>$request->lat,
            'lng'=>$request->lng,
            'willing_to_trade'=>$request->willing_to_trade,
        ];

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
          $this->authorize('delete', $product);
      $product->delete();
      return $this->success(null,"محصول با موفقیت حذف شد");
    }
}
