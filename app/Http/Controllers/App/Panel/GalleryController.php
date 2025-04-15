<?php

namespace App\Http\Controllers\App\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Http\Resources\Admin\Product\GalleryCollection;
use App\Http\Resources\Admin\Product\GalleryResource;
use App\Http\Services\Image\ImageService;
use App\Models\Product\Gallery;
use App\Models\Product\Products;
use App\Traits\HttpResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use HttpResponses, AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index($productId)
    {
        $product = Products::findOrFail($productId);
        $this->authorize('view', $product);
        return new GalleryCollection($product->images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ImageService $imageService, Products $product)
    {
        $request->validate([
            'image' => 'required|max:3000|image|mimes:png,jpg,jpeg,gif',
            "status" => "nullable|numeric|in:0,1",
        ]);
        $this->authorize('create', $product);

        $input = [
            'product_id' => $product->id,
            'image' => $request->image,
            'status' => 3
        ];
        if ($request->hasFile('image')) {
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'product-gallery-images');
            $result = $imageService->createIndexAndSave($request->image);
            if ($result) {
                $input['image'] = $result;
            } else {
                $this->error(null, 'خطا در ذخیره عکس', 400);
            }
        };
        $gallery = Gallery::create($input);
        return new GalleryResource($gallery);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gallery $gallery)
    {
        $this->authorize('view', $gallery);
        return new GalleryResource($gallery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery, ImageService $imageService)
    {
        $request->validate([
            'image' => 'required|max:3000|image|mimes:png,jpg,jpeg,gif',
            "status" => "nullable|numeric|in:0,1",
        ]);
        $this->authorize('update', $gallery);
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
        $this->authorize('delete', $gallery);
        if (!empty($gallery->image)) {
            $imageService->deleteDirectoryAndFiles($gallery->image['directory']);
        }
        $gallery->delete();
        return $this->success(null, "گالری با موفقیت حذف شد");
    }
}
