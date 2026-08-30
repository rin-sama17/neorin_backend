<?php

namespace App\Http\Controllers\App\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\UpdateAddressRequest;
use App\Http\Requests\Panel\StoreAddressRequest;
use App\Models\User\Address;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    use AuthorizesRequests;
    public function index(Request $request)
    {
        return $request->user()->addresses()->with(['state', 'city'])->latest()->get();
    }

    public function store(StoreAddressRequest $request)
    {
        $address = $request->user()->addresses()->create($request->validated());

        return response()->json($address->load(['state', 'city']), 201);
    }

    public function show(Address $address)
    {
        $this->authorize('view', $address);

        return $address->load(['state', 'city']);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        $address->update($request->validated());

        return $address->load(['state', 'city']);
    }

    public function destroy(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return response()->noContent();
    }

    public function setDefault(Address $address)
    {
        $this->authorize('update', $address);

        $address->update(['is_default' => true]);

        return $address->load(['state', 'city']);
    }
}
