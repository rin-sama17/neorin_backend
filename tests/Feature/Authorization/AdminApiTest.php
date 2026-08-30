<?php

use App\Models\User;
use App\Models\User\Role;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    seedRolesAndPermissions();
});

test('user cannot access admin roles endpoint', function () {
    $user = createUserWithRole('user');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/roles')->assertStatus(403);
});

test('admin with roles.view can list roles', function () {
    $user = createUserWithRole('admin');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/roles')
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

test('super-admin can list roles without explicit permissions', function () {
    $user = createUserWithRole('super-admin');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/roles')
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

test('super-admin role cannot be deleted', function () {
    $user = createUserWithRole('admin');

    Sanctum::actingAs($user);

    $superAdmin = Role::where('slug', 'super-admin')->firstOrFail();

    $this->deleteJson('/api/admin/roles/' . $superAdmin->id)
        ->assertStatus(422);
});

test('admin can create a role with permissions', function () {
    $user = createUserWithRole('admin');

    Sanctum::actingAs($user);

    $viewPermission = \App\Models\User\Permission::where('slug', 'products.view')->firstOrFail();

    $this->postJson('/api/admin/roles', [
        'name' => 'نقش تست',
        'slug' => 'test-role',
        'permissions' => [$viewPermission->id],
    ])->assertOk()
        ->assertJsonPath('slug', 'test-role');

    expect(Role::where('slug', 'test-role')->firstOrFail()->hasPermission('products.view'))->toBeTrue();
});

test('roles endpoint responds 422 when slug is missing', function () {
    $user = createUserWithRole('admin');

    Sanctum::actingAs($user);

    $this->postJson('/api/admin/roles', [
        'name' => 'بدون اسلاگ',
    ])->assertStatus(422);
});

test('admin can update a role name and permissions', function () {
    $user = createUserWithRole('admin');

    Sanctum::actingAs($user);

    $role = Role::where('slug', 'user')->firstOrFail();

    $this->putJson('/api/admin/roles/' . $role->id, [
        'name' => 'مشتری جدید',
        'slug' => 'user',
        'permissions' => [],
    ])->assertOk()
        ->assertJsonPath('name', 'مشتری جدید');

    expect($role->fresh()->hasPermission('orders.view'))->toBeTrue();
});

test('user without users permission cannot access admin users', function () {
    $user = createUserWithRole('user');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/users/user')->assertStatus(403);
});

test('admin can access admin users list', function () {
    $user = createUserWithRole('admin');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/users/user')->assertOk();
});

test('super-admin cannot be deleted by admin', function () {
    $admin = createUserWithRole('admin');
    $superAdminUser = createUserWithRole('super-admin');

    Sanctum::actingAs($admin);

    $this->deleteJson('/api/admin/users/user/' . $superAdminUser->id)
        ->assertStatus(422);

    expect($superAdminUser->fresh())->not->toBeNull();
});
