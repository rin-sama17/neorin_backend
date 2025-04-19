<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSliderRequest;
use App\Http\Requests\Admin\UpdateSliderRequest;
use App\Http\Resources\Admin\Content\SliderCollection;
use App\Http\Resources\Admin\Content\SliderResource;
use App\Http\Services\Image\ImageService;
use App\Models\Content\Slider;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new SliderCollection(Slider::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSliderRequest $request, ImageService $imageService)
    {
        $input = $request->all();
        $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'slider-images');
        $result = $imageService->save($request->image);
        if ($result) {
            $input['image'] = $result;
        } else {
            $this->error(null, 'خطا در ذخیره عکس', 400);
        }
        $slider = Slider::create($input);
        return new SliderResource($slider);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider)
    {
        return new SliderResource($slider);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSliderRequest $request, Slider $slider, ImageService $imageService)
    {
        $input = $request->all();

        if ($request->hasFile('image')) {
            $imageService->deleteImage($slider->image);
            $imageService->setExclusiveDirectory('images' . DIRECTORY_SEPARATOR . 'slider-images');
            $result = $imageService->save($request->image);

            if ($result == false) {
                return $this->error(null, 'خطا در فرایند اپلود عکس', 400);
            }
            $input['image'] = $result;
        }

        $slider->update($input);
        return new SliderResource($slider);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider, ImageService $imageService)
    {
        $imageService->deleteImage($slider->image);
        $slider->delete();
        return $this->success(null, 'اسلایدر با موفقیت حذف شد');
    }
}
