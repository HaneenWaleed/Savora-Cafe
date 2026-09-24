<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('allows an authenticated admin to access the dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email' => 'admin@savora.com',
    ]);

    actingAs($admin, 'sanctum');

    get('/admin')
        ->assertOk()
        ->assertSee('Savora Cafeteria')
        ->assertSee('Admin Dashboard');
});

it('blocks a customer from accessing the admin dashboard', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'email' => 'customer@savora.com',
    ]);

    actingAs($customer, 'sanctum');

    get('/admin')->assertForbidden();
});

it('allows the mock admin cookie flow to access the dashboard', function () {
    $this->withCookie('savora_user_email', 'admin@savora.com')
        ->withCookie('savora_user_role', 'admin')
        ->get('/admin')
        ->assertOk()
        ->assertSee('Admin Dashboard');
});
