<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Home\CityCollection;
use App\Http\Resources\Home\CityResource;
use App\Models\Geo\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::active()->get();
        return new CityCollection($cities);
    }
    public function show(City $city)
    {
        return new CityResource($city);
    }
}
