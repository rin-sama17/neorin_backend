<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Role\RoleCollection;
use App\Http\Resources\Admin\Role\RoleResource;
use App\Models\User\Role;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use HttpResponses;

    public function index()
    {
        return new RoleCollection(Role::with('permissions')->get());
    }

    public function show(Role $role)
    {
        return new RoleResource($role->load('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:roles,slug',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return new RoleResource($role->load('permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:roles,slug,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return new RoleResource($role->load('permissions'));
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') {
            return $this->error(null, 'امکان حذف سوپر ادمین وجود ندارد.', 422);
        }

        $role->delete();

        return $this->success(null, 'نقش با موفقیت حذف شد.', 200);
    }
}
