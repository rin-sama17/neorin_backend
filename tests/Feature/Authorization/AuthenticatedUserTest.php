<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    seedRolesAndPermissions();
});

test('authenticated user me endpoint returns roles and permissions', function () {
    $user = createUserWithRole('user');

    Sanctum::actingAs($user);

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJson([
            'id' => $user->id,
            'roles' => ['user'],
        ])
        ->assertJsonPath('permissions', function ($permissions) {
            return is_array($permissions)
                && in_array('checkout.create', $permissions)
                && in_array('cart.create', $permissions);
        });
});

test('me endpoint is protected and requires authentication', function () {
    $this->getJson('/api/me')->assertStatus(401);
});

test('admin user index endpoint returns roles with permissions', function () {
    $admin = createUserWithRole('admin');

    Sanctum::actingAs($admin);

    $this->getJson('/api/admin/users/user')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [['id', 'name', 'roles']],
        ]);
});

test('admin user update endpoint can assign roles', function () {
    $admin = createUserWithRole('admin');
    $target = User::factory()->create();
    $userRole = \App\Models\User\Role::where('slug', 'user')->firstOrFail();

    Sanctum::actingAs($admin);

    $this->patchJson('/api/admin/users/user/' . $target->id, [
        'name' => 'Updated',
        'roles' => [$userRole->id],
    ])->assertOk();

    expect($target->fresh()->hasRole('user'))->toBeTrue();
});
