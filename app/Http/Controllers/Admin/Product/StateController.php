<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStateRequest;
use App\Http\Requests\Admin\UpdateStateRequest;
use App\Http\Resources\Admin\Product\StateCollection;
use App\Http\Resources\Admin\Product\StateResource;
use App\Models\Product\State;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class StateController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new StateCollection(State::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request)
    {
        $input = $request->all();
        State::create($input);
        return $this->success(null, 'محل با موفقیت ساخته شد');
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        return new StateResource($state);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        $input = $request->all();
        $state->update($input);
        return new StateResource($state);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)

    {
        $state->delete();
        return $this->success(null, 'محل با موفقیت حذف شد');
    }
}
