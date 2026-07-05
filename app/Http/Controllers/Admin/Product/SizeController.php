<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSizeRequest;
use App\Http\Requests\Admin\UpdateSizeRequest;
use App\Http\Resources\Admin\Product\SizeCollection;
use App\Http\Resources\Admin\Product\SizeResource;
use App\Http\Services\Image\ImageService;
use App\Models\Product\Size;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new SizeCollection(Size::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSizeRequest $request, ImageService $imageService)
    {
        $input = $request->all(); 
        if ($request->hasFile('image')) {
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-size-images');
            $result = $imageService->createIndexAndSave($request->image);
            if ($result) {
                $input['image'] = $result;
            } else {
                $this->error(null, 'خطا در ذخیره عکس', 400);
            }
        };
        
        $size = Size::create($input);
        return new SizeResource($size);
    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {
        return new SizeResource($size);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSizeRequest $request, Size $size,ImageService $imageService)
    {
        $input = $request->all();
         if ($request->hasFile('image')) {
            if (!empty($product->image)) {
                $imageService->deleteDirectoryAndFiles($product->image['directory']);
            }
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-size-images');
            $result = $imageService->createIndexAndSave($request->image);

            if ($result == false) {
                return $this->error(null, 'خطا در فرایند اپلود عکس', 400);
            }
            $input['image'] = $result;
        } else {
            if (isset($input['currentImage']) && !empty($product->image)) {
                $image = $product->image;
                $image['currentImage'] = $input['currentImage'];
                $input['image'] = $image;
            }
        };

        $size->update($input);
        return new SizeResource($size);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $size->delete();
        return $this->success(null, "سایز با موفقیت حذف شد");
    }
}
