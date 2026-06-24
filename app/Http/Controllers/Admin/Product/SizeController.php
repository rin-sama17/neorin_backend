<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSizeRequest;
use App\Http\Requests\Admin\UpdateSizeRequest;
use App\Http\Resources\Admin\Product\SizeCollection;
use App\Http\Resources\Admin\Product\SizeResource;
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
    public function store(StoreSizeRequest $request)
    {
        $input = $request->all();
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
    public function update(UpdateSizeRequest $request, Size $size)
    {
        $input = $request->all();
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
