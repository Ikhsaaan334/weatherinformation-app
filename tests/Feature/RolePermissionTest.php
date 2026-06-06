<?php

use App\Models\City;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Permission::firstOrCreate(['name' => 'manage cities']);
    Permission::firstOrCreate(['name' => 'view weather']);

    Role::firstOrCreate(['name' => 'admin'])
        ->givePermissionTo('manage cities', 'view weather');

    Role::firstOrCreate(['name' => 'user'])
        ->givePermissionTo('view weather');
});

function adminUser(): User
{
    return tap(User::factory()->create())->assignRole('admin');
}

function regularUser(): User
{
    return tap(User::factory()->create())->assignRole('user');
}

function cityPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test City',
        'country' => 'ID',
        'lat' => 1.23,
        'lon' => 4.56,
    ], $overrides);
}

test('newly registered users receive the user role', function () {
    $this->post('/register', [
        'name' => 'Fresh User',
        'email' => 'fresh@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'fresh@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hasRole('user'))->toBeTrue()
        ->and($user->hasRole('admin'))->toBeFalse();
});

test('a regular user cannot create a city', function () {
    $this->actingAs(regularUser())
        ->post(route('cities.store'), cityPayload())
        ->assertForbidden();

    expect(City::count())->toBe(0);
});

test('an admin can create a city', function () {
    $this->actingAs(adminUser())
        ->post(route('cities.store'), cityPayload(['name' => 'Jakarta']));

    expect(City::where('name', 'Jakarta')->exists())->toBeTrue();
});

test('a regular user cannot update or delete a city', function () {
    $city = City::create(cityPayload(['name' => 'Bandung']));
    $user = regularUser();

    $this->actingAs($user)
        ->put(route('cities.update', $city), cityPayload(['name' => 'Hacked']))
        ->assertForbidden();

    $this->actingAs($user)
        ->delete(route('cities.destroy', $city))
        ->assertForbidden();

    expect($city->fresh()->name)->toBe('Bandung');
});

test('a regular user cannot access the admin users page', function () {
    $this->actingAs(regularUser())
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('an admin can access the admin users page', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.users.index'))
        ->assertOk();
});

test('an admin can change another user role', function () {
    $admin = adminUser();
    $target = regularUser();

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $target), ['role' => 'admin']);

    expect($target->fresh()->hasRole('admin'))->toBeTrue()
        ->and($target->fresh()->hasRole('user'))->toBeFalse();
});

test('an admin cannot demote themselves', function () {
    $admin = adminUser();

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $admin), ['role' => 'user']);

    expect($admin->fresh()->hasRole('admin'))->toBeTrue();
});

test('changing a role to a non-existent role is rejected', function () {
    $admin = adminUser();
    $target = regularUser();

    $this->actingAs($admin)
        ->patch(route('admin.users.update-role', $target), ['role' => 'superadmin'])
        ->assertSessionHasErrors('role');
});
