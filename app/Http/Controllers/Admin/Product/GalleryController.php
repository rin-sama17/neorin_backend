<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Http\Resources\Admin\Product\GalleryCollection;
use App\Http\Resources\Admin\Product\GalleryResource;
use App\Http\Services\Image\ImageService;
use App\Models\Product\Gallery;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new GalleryCollection(Gallery::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGalleryRequest $request, ImageService $imageService)
    {
        $input = $request->all();
        foreach ($request->file('images') as $image) {
            $productImage = [
                "product_id" => $input['product_id'],
                "image" => null
            ];
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-gallery-images');
            $result = $imageService->createIndexAndSave($image);
            if ($result) {
                $productImage['image'] = $result;
            } else {
                $this->error(null, 'خطا در ذخیره عکس', 400);
            }
            Gallery::create($productImage);
        }

        return response()->json([
            'message' => "success"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        return new GalleryResource($gallery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery, ImageService $imageService)
    {
        $input = $request->all();
        if ($request->hasFile('image')) {
            $imageService->deleteDirectoryAndFiles($gallery->image['directory']);
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-gallery-images');
            $result = $imageService->createIndexAndSave($request->image);

            if ($result == false) {
                return $this->error(null, 'خطا در فرایند اپلود عکس', 400);
            }
            $input['image'] = $result;
        } else {
            if (isset($input['currentImage']) && !empty($gallery->image)) {
                $image = $gallery->image;
                $image['currentImage'] = $input['currentImage'];
                $input['image'] = $image;
            }
        };

        $gallery->update($input);
        return new GalleryResource($gallery);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery, ImageService $imageService)
    {
        if (!empty($gallery->image)) {
            $imageService->deleteDirectoryAndFiles($gallery->image['directory']);
        }
        $gallery->delete();
        return $this->success(null, "گالری با موفقیت حذف شد");
    }
}
