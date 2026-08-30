<?php

use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Shop\Order;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    seedRolesAndPermissions();
});

test('order owner can view their order', function () {
    $owner = createUserWithRole('user');
    $order = Order::create([
        'user_id' => $owner->id,
        'shipping_address_snapshot' => ['city' => 'Tehran'],
        'subtotal' => 1000,
        'shipping_price' => 0,
        'discount' => 0,
        'total_price' => 1000,
        'order_status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    Sanctum::actingAs($owner);

    $this->getJson('/api/orders/' . $order->id)->assertOk();
});

test('non-owner cannot view someone elses order', function () {
    $owner = createUserWithRole('user');
    $other = createUserWithRole('user');
    $order = Order::create([
        'user_id' => $owner->id,
        'shipping_address_snapshot' => ['city' => 'Tehran'],
        'subtotal' => 1000,
        'shipping_price' => 0,
        'discount' => 0,
        'total_price' => 1000,
        'order_status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    Sanctum::actingAs($other);

    $this->getJson('/api/orders/' . $order->id)->assertStatus(403);
});

test('super-admin can view any order via policy bypass', function () {
    $owner = createUserWithRole('user');
    $superAdmin = createUserWithRole('super-admin');
    $order = Order::create([
        'user_id' => $owner->id,
        'shipping_address_snapshot' => ['city' => 'Tehran'],
        'subtotal' => 1000,
        'shipping_price' => 0,
        'discount' => 0,
        'total_price' => 1000,
        'order_status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    Sanctum::actingAs($superAdmin);

    $this->getJson('/api/orders/' . $order->id)->assertOk();
});

test('cart item owner can update their cart item', function () {
    $owner = createUserWithRole('user');
    $cart = Cart::create(['user_id' => $owner->id]);
    $item = CartItem::create([
        'cart_id' => $cart->id,
        'item_type' => 'product',
        'slug' => 'test-item',
        'quantity' => 1,
    ]);

    Sanctum::actingAs($owner);

    $this->patchJson('/api/cart/' . $item->id, ['quantity' => 3])->assertOk();
});

test('non-owner cannot update someone elses cart item', function () {
    $owner = createUserWithRole('user');
    $other = createUserWithRole('user');
    $cart = Cart::create(['user_id' => $owner->id]);
    $item = CartItem::create([
        'cart_id' => $cart->id,
        'item_type' => 'product',
        'slug' => 'test-item',
        'quantity' => 1,
    ]);

    Sanctum::actingAs($other);

    $this->patchJson('/api/cart/' . $item->id, ['quantity' => 3])->assertStatus(403);
});

test('order index requires orders permission', function () {
    $user = createUserWithRole('user');

    Sanctum::actingAs($user);

    $this->getJson('/api/orders')->assertOk();
});

test('checkout requires checkout.create permission', function () {
    $user = createUserWithRole('user');

    Sanctum::actingAs($user);

    $this->postJson('/api/orders/checkout', [
        'address' => ['city' => 'Tehran', 'detail' => 'Somewhere'],
        'payment_method' => 'zarinpal',
    ])->assertStatus(422); // permission passed; validation failure expected without cart
});

test('user without orders permission cannot access orders index', function () {
    $user = createUserWithRole('product-manager');

    Sanctum::actingAs($user);

    $this->getJson('/api/orders')->assertStatus(403);
});
