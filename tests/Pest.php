<?php

use App\Models\User;
use App\Models\User\Role;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function seedRolesAndPermissions(): void
{
    app(\Database\Seeders\PermissionSeeder::class)->run();
    app(\Database\Seeders\RoleSeeder::class)->run();
}

function createUserWithRole(string $roleSlug, array $attributes = []): User
{
    $user = User::factory()->create($attributes);
    $role = Role::where('slug', $roleSlug)->firstOrFail();
    $user->roles()->attach($role);

    return $user->load('roles.permissions');
}
