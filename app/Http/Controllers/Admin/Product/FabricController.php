<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFabricRequest;
use App\Http\Requests\Admin\UpdateFabricRequest;
use App\Http\Resources\Admin\Product\FabricCollection;
use App\Http\Resources\Admin\Product\FabricResource;
use App\Http\Services\Image\ImageService;
use App\Models\Product\Fabric;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class FabricController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new FabricCollection(Fabric::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFabricRequest $request, ImageService $imageService)
    {
        $input = $request->all();
        if ($request->hasFile('image')) {
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'fabric-images');
            $result = $imageService->createIndexAndSave($request->image);
            if ($result) {
                $input['image'] = $result;
                } else {
                    $this->error(null, 'خطا در ذخیره عکس', 400);
                    }
        };
        $fabric = Fabric::create($input);
        if ($request->has('product_ids')) {
            $fabric->products()->sync($request->product_ids);
            }
            if ($request->has('color_ids')) {
                $fabric->colors()->sync($request->color_ids);
                }
        return new FabricResource($fabric);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fabric $fabric)
    {
        return new FabricResource($fabric);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFabricRequest $request,Fabric $fabric , ImageService $imageService)
    {
        $input  = $request->all();
        if ($request->hasFile('image')) {
            if (!empty($fabric->image)) {
                $imageService->deleteDirectoryAndFiles($fabric->image['directory']);
            }
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'fabric-images');
            $result = $imageService->createIndexAndSave($request->image);

            if ($result == false) {
                return $this->error(null, 'خطا در فرایند اپلود عکس', 400);
            }
            $input['image'] = $result;
        } else {
            if (isset($input['currentImage']) && !empty($fabric->image)) {
                $image = $fabric->image;
                $image['currentImage'] = $input['currentImage'];
                $input['image'] = $image;
            }
        };

        $fabric->update($input);

         if ($request->has('product_ids')) {
        $fabric->products()->sync($request->product_ids);
    }
     if ($request->has('color_ids')) {
        $fabric->colors()->sync($request->color_ids);
        }
        return new FabricResource($fabric);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fabric $fabric)
    {
        $fabric->delete();
        return $this->success(null , "پارچه با موفقیت حذف شد");


    }
}
