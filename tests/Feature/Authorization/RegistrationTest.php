<?php

use App\Http\Services\Sms\SmsService;
use App\Models\User;
use App\Models\User\Otp;
use App\Models\User\Role;

beforeEach(function () {
    seedRolesAndPermissions();

    $this->mock(SmsService::class, function ($mock) {
        $mock->shouldReceive('sendSmsOtp')->andReturn(true);
    });
});

test('new users are auto-assigned the user role on registration', function () {
    $otp = Otp::create([
        'token' => 'token-123',
        'login_id' => '09120000000',
        'otp_code' => '1234',
        'type' => 0,
        'used' => 0,
        'attempts' => 0,
    ]);

    $this->postJson('/api/register', [
        'mobile' => '09120000000',
        'otp' => '1234',
        'token' => 'token-123',
    ])->assertStatus(200);

    $user = User::where('mobile', '09120000000')->firstOrFail();

    expect($user->hasRole('user'))->toBeTrue()
        ->and($user->hasPermission('checkout.create'))->toBeTrue();
});

test('existing users logging in do not get their roles overwritten', function () {
    $admin = createUserWithRole('admin', ['mobile' => '09120000000']);

    $otp = Otp::create([
        'token' => 'token-456',
        'login_id' => '09120000000',
        'otp_code' => '5678',
        'type' => 0,
        'used' => 0,
        'attempts' => 0,
    ]);

    $this->postJson('/api/register', [
        'mobile' => '09120000000',
        'otp' => '5678',
        'token' => 'token-456',
    ])->assertStatus(200);

    expect($admin->fresh()->hasRole('admin'))->toBeTrue()
        ->and($admin->fresh()->hasRole('user'))->toBeFalse();
});

test('assign-user-role command safely assigns user role to role-less users', function () {
    $roleLess = User::factory()->create(['mobile' => '09111111111']);
    $existing = createUserWithRole('order-manager', ['mobile' => '09222222222']);

    $this->artisan('roles:assign-user')->assertSuccessful();

    expect($roleLess->fresh()->hasRole('user'))->toBeTrue()
        ->and($existing->fresh()->hasRole('user'))->toBeFalse()
        ->and($existing->fresh()->hasRole('order-manager'))->toBeTrue();
});
