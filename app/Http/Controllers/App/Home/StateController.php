<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Product\StateCollection;
use App\Models\Product\State;

class StateController extends Controller
{
    public function index()
    {
        return new StateCollection(State::all()->whereNull('parent_id'));
    }
}
