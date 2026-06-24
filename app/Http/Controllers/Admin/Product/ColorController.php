<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreColorRequest;
use App\Http\Requests\Admin\UpdateColorRequest;
use App\Http\Resources\Admin\Product\ColorCollection;
use App\Http\Resources\Admin\Product\ColorResource;
use App\Models\Product\Color;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ColorCollection(Color::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreColorRequest $request)
    {
        $input = $request->all();
        $color = Color::create($input);
        return new ColorResource($color);
    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        return new ColorResource($color);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateColorRequest $request, Color $color)
    {
        $input = $request->all();
        $color->update($input);
        return new ColorResource($color);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();
        return $this->success(null , "رنگ با موفقیت حذف شد");
    }
}
