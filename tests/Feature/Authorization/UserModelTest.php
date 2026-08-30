<?php

use App\Models\User;
use App\Models\User\Role;

beforeEach(function () {
    seedRolesAndPermissions();
});

test('user model hasRole and hasAnyRole helpers work', function () {
    $user = createUserWithRole('user');

    expect($user->hasRole('user'))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse()
        ->and($user->hasAnyRole('admin', 'user'))->toBeTrue()
        ->and($user->hasAllRoles('user'))->toBeTrue()
        ->and($user->hasAllRoles('user', 'admin'))->toBeFalse();
});

test('user model hasPermission works through role permissions', function () {
    $user = createUserWithRole('user');
    $manager  = createUserWithRole('product-manager');

    expect($user->hasPermission('cart.create'))->toBeTrue()
        ->and($user->hasPermission('products.view'))->toBeFalse()
        ->and($manager->hasPermission('products.view'))->toBeTrue()
        ->and($manager->hasAnyPermission('products.view', 'cart.create'))->toBeTrue()
        ->and($manager->hasAllPermissions('products.view', 'products.create'))->toBeTrue();
});

test('super-admin has every permission via bypass', function () {
    $user = createUserWithRole('super-admin');

    expect($user->hasPermission('anything.at.all'))->toBeTrue()
        ->and($user->hasAnyPermission('foo'))->toBeTrue()
        ->and($user->hasAllPermissions('foo', 'bar', 'baz'))->toBeTrue()
        ->and($user->isSuperAdmin())->toBeTrue();
});

test('role-less user has no permissions', function () {
    $user = User::factory()->create();

    expect($user->hasRole('user'))->toBeFalse()
        ->and($user->hasPermission('products.view'))->toBeFalse()
        ->and($user->isSuperAdmin())->toBeFalse();
});

test('permission checks do not re-query when roles are eager loaded', function () {
    $user = createUserWithRole('user');

    DB::enableQueryLog();

    $user->hasPermission('cart.create');
    $user->hasPermission('cart.delete');

    $queries = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queries)->toBeLessThan(3);
});

test('role hasPermission helper works', function () {
    $user = Role::where('slug', 'user')->firstOrFail();

    expect($user->hasPermission('checkout.create'))->toBeTrue()
        ->and($user->hasPermission('roles.view'))->toBeFalse();
});
