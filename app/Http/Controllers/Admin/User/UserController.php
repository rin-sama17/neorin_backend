<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\User\UserCollection;
use App\Http\Resources\Admin\User\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new UserCollection(User::with('roles.permissions')->get());
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user->load('roles.permissions'));
    }

    public function store(Request $request)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        return new UserResource($user->load('roles.permissions'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return $this->error(null, 'امکان حذف سوپر ادمین وجود ندارد.', 422);
        }

        $user->delete();
        return $this->success(null, "کاربر با موفقیت حذف شد");
    }
}
