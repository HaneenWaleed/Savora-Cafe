<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CustomerPreference;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@cafeteria.test'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'phone'             => '01000000000',
                'age'               => 30,
                'email_verified_at' => now(),
            ]
        );

        
        $customer = User::updateOrCreate(
            ['email' => 'customer@cafeteria.test'],
            [
                'name'              => 'Test Customer',
                'password'          => Hash::make('password'),
                'role'              => 'customer',
                'phone'             => '01111111111',
                'age'               => 22,
                'email_verified_at' => now(),
            ]
        );

        $favoriteCategories = Category::whereIn('slug', ['pizza', 'burgers', 'coffee'])
            ->pluck('id')
            ->all();

        CustomerPreference::updateOrCreate(
            ['user_id' => $customer->id],
            [
                'favorite_categories'  => $favoriteCategories,
                'favorite_food_types'  => ['chicken', 'pizza'],
                'favorite_beverages'   => ['coffee', 'orange juice'],
                'preferred_taste'      => 'spicy',
                'dietary_preferences'  => [],
                'price_preference'     => 'medium',
                'spicy_level'          => 3,
                'favorite_ingredients' => ['chicken', 'cheese'],
                'disliked_ingredients' => ['mushroom'],
            ]
        );
    }
}
