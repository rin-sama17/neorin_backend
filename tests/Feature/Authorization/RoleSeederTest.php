<?php

use App\Models\User;
use App\Models\User\Permission;
use App\Models\User\Role;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

test('seeders create exactly the 5 expected roles', function () {
    seedRolesAndPermissions();

    expect(Role::count())->toBe(5);

    foreach (['super-admin', 'admin', 'product-manager', 'order-manager', 'user'] as $slug) {
        expect(Role::where('slug', $slug)->exists())->toBeTrue();
    }
});

test('seeders are idempotent when run twice', function () {
    seedRolesAndPermissions();
    $roles = Role::count();
    $permissions = Permission::count();

    seedRolesAndPermissions();

    expect(Role::count())->toBe($roles)
        ->and(Permission::count())->toBe($permissions);
});



test('product-manager role has product and catalog permissions but not user/role management', function () {
    seedRolesAndPermissions();

    $manager = Role::where('slug', 'product-manager')->firstOrFail();

    expect($manager->hasPermission('products.view'))->toBeTrue()
        ->and($manager->hasPermission('categories.create'))->toBeTrue()
        ->and($manager->hasPermission('custom-products.manage-rules'))->toBeTrue()
        ->and($manager->hasPermission('users.view'))->toBeFalse()
        ->and($manager->hasPermission('roles.view'))->toBeFalse();
});

test('order-manager role has order permissions but no product permissions', function () {
    seedRolesAndPermissions();

    $manager = Role::where('slug', 'order-manager')->firstOrFail();

    expect($manager->hasPermission('orders.view'))->toBeTrue()
        ->and($manager->hasPermission('orders.update'))->toBeTrue()
        ->and($manager->hasPermission('products.view'))->toBeFalse();
});

test('admin role has broad permissions including user and role management', function () {
    seedRolesAndPermissions();

    $admin = Role::where('slug', 'admin')->firstOrFail();

    expect($admin->hasPermission('products.view'))->toBeTrue()
        ->and($admin->hasPermission('users.view'))->toBeTrue()
        ->and($admin->hasPermission('roles.view'))->toBeTrue()
        ->and($admin->hasPermission('settings.manage'))->toBeTrue();
});

test('super-admin role is created without explicit permissions (bypass based)', function () {
    seedRolesAndPermissions();

    $superAdmin = Role::where('slug', 'super-admin')->firstOrFail();

    expect($superAdmin->permissions()->count())->toBe(0);
});

test('existing permission and role rows are preserved across reseeding', function () {
    seedRolesAndPermissions();

    $customRole = Role::create(['name' => 'کاربر آزمایشی', 'slug' => 'custom-test-role']);
    $customRole->permissions()->attach(Permission::where('slug', 'products.view')->firstOrFail()->id);

    seedRolesAndPermissions();

    expect(Role::where('slug', 'custom-test-role')->exists())->toBeTrue()
        ->and($customRole->fresh()->permissions()->count())->toBe(1);
});
