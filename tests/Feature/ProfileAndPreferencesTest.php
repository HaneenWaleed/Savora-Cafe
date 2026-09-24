<?php

use App\Models\CustomerPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\putJson;

uses(RefreshDatabase::class);

it('updates a user profile and persists it to the database', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'phone' => '01000000000',
        'age' => 20,
    ]);

    actingAs($user, 'sanctum');

    $response = putJson('/api/profile', [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '01012345678',
        'age' => 27,
    ]);

    $response->assertOk();
    $response->assertJsonPath('user.name', 'New Name');
    $response->assertJsonPath('user.email', 'new@example.com');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '01012345678',
        'age' => 27,
    ]);
});

it('creates and updates user preferences without duplicating the record', function () {
    $user = User::factory()->create();

    actingAs($user, 'sanctum');

    $first = putJson('/api/profile/preferences', [
        'favorite_categories' => ['Pizza', 'Burgers'],
        'favorite_food_types' => ['Chicken', 'Fast Food'],
        'favorite_beverages' => ['Coffee', 'Juices'],
        'preferred_taste' => 'savory',
        'dietary_preferences' => ['vegetarian'],
        'price_preference' => 'medium',
        'spicy_level' => 2,
        'favorite_ingredients' => ['Chicken', 'Cheese'],
        'disliked_ingredients' => ['Mushrooms'],
    ]);

    $first->assertOk();
    $first->assertJsonPath('preference.favorite_categories.0', 'Pizza');
    $this->assertDatabaseCount('customer_preferences', 1);

    $second = putJson('/api/profile/preferences', [
        'favorite_categories' => ['Pizza', 'Pasta'],
        'favorite_food_types' => ['Chicken', 'Healthy'],
        'favorite_beverages' => ['Coffee'],
        'preferred_taste' => 'sweet',
        'dietary_preferences' => ['low-calorie'],
        'price_preference' => 'high',
        'spicy_level' => 3,
        'favorite_ingredients' => ['Cheese', 'Tomato'],
        'disliked_ingredients' => ['Olives'],
    ]);

    $second->assertOk();
    $this->assertDatabaseCount('customer_preferences', 1);

    $preference = CustomerPreference::query()->where('user_id', $user->id)->firstOrFail();
    expect($preference->favorite_categories)->toBe(['Pizza', 'Pasta']);
    expect($preference->preferred_taste)->toBe('sweet');
    expect($preference->favorite_ingredients)->toBe(['Cheese', 'Tomato']);
    expect($preference->disliked_ingredients)->toBe(['Olives']);
});

it('creates a mock-user record when preferences are saved through the email-based flow', function () {
    $first = putJson('/api/profile/preferences', [
        'favorite_categories' => ['Pizza', 'Burgers'],
        'favorite_food_types' => ['Chicken'],
        'favorite_beverages' => ['Coffee'],
        'preferred_taste' => 'savory',
        'dietary_preferences' => ['vegetarian'],
        'price_preference' => 'medium',
        'spicy_level' => 2,
        'favorite_ingredients' => ['Chicken', 'Cheese'],
        'disliked_ingredients' => ['Mushrooms'],
    ], ['X-User-Email' => 'mock-user@example.com']);

    $first->assertOk();
    $this->assertDatabaseHas('users', ['email' => 'mock-user@example.com']);
    $this->assertDatabaseCount('customer_preferences', 1);

    $second = putJson('/api/profile/preferences', [
        'favorite_categories' => ['Pizza', 'Pasta'],
        'favorite_food_types' => ['Healthy'],
        'favorite_beverages' => ['Tea'],
        'preferred_taste' => 'sweet',
        'dietary_preferences' => ['low-calorie'],
        'price_preference' => 'high',
        'spicy_level' => 3,
        'favorite_ingredients' => ['Tomato'],
        'disliked_ingredients' => ['Olives'],
    ], ['X-User-Email' => 'mock-user@example.com']);

    $second->assertOk();
    $this->assertDatabaseCount('customer_preferences', 1);

    $user = User::query()->where('email', 'mock-user@example.com')->firstOrFail();
    $preference = $user->fresh()->preference()->firstOrFail();

    expect($preference->favorite_categories)->toBe(['Pizza', 'Pasta']);
    expect($preference->preferred_taste)->toBe('sweet');
    expect($preference->favorite_ingredients)->toBe(['Tomato']);
});
