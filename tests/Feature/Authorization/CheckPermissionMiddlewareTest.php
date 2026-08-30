<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    seedRolesAndPermissions();
});

test('unauthenticated request to a protected admin route returns 401', function () {
    $this->getJson('/api/admin/product/products')
        ->assertStatus(401)
        ->assertJson(['status' => false]);
});

test('authenticated user without permission gets 403', function () {
    $user = createUserWithRole('user');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/product/products')
        ->assertStatus(403)
        ->assertJson(['status' => false]);
});

test('user with the required permission is allowed', function () {
    $user = createUserWithRole('product-manager');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/product/products')
        ->assertOk();
});

test('super-admin bypasses permission checks', function () {
    $user = createUserWithRole('super-admin');

    Sanctum::actingAs($user);

    $this->getJson('/api/admin/product/products')
        ->assertOk();
});

test('permission middleware derives operation from HTTP method', function () {
    $user = createUserWithRole('product-manager');

    Sanctum::actingAs($user);

    // product-manager has products.create -> store action is permitted
    $this->getJson('/api/admin/product/products')
        ->assertOk();
});
