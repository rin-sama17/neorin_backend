<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\FabricCollection;
use App\Http\Resources\Home\FabricResource;
use App\Models\Product\Fabric;

class FabricController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new FabricCollection(Fabric::all());
    }



    /**
     * Display the specified resource.
     */
    public function show(Fabric $product)
    {
        return new FabricResource($product);
    }
}
