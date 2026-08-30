<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Product\StateCollection;
use App\Models\Product\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index(Request $request)
    {
        return   State::query()
            ->when($request->city_id, fn($q) => $q->where('city_id', $request->city_id))
            ->active()
            ->get();
    }
}
