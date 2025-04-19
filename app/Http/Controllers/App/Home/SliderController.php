<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Models\Content\Slider;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new SliderController(Slider::all());
    }
}
