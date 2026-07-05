<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Users\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return Role::with('permissions')->get();
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:roles',
            'description' => 'nullable',
            'permissions' => 'array'
        ]);

        $role = Role::create($data);

        $role->permissions()->sync(
            $data['permissions'] ?? []
        );

        return response()->json($role);
    }
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:roles,slug,' . $role->id,
            'description' => 'nullable',
            'permissions' => 'array'
        ]);

        $role->update($data);

        $role->permissions()->sync(
            $data['permissions'] ?? []
        );

        return response()->json($role);
    }
    public function destroy(Role $role)
    {
        if ($role->slug === 'super-admin') {

            return response()->json([
                'message' => 'امکان حذف سوپر ادمین وجود ندارد.'
            ], 422);
        }

        $role->delete();

        return response()->noContent();
    }
}
