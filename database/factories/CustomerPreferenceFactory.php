<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPreferenceFactory extends Factory
{
    public function definition(): array
    {
        $categoryIds = Category::pluck('id')->all();

        return [
            'user_id'              => User::factory(),
            'favorite_categories'  => fake()->randomElements($categoryIds, fake()->numberBetween(1, 3)),
            'favorite_food_types'  => fake()->randomElements(['chicken', 'beef', 'pizza', 'pasta', 'burger', 'sandwich', 'dessert'], 2),
            'favorite_beverages'   => fake()->randomElements(['coffee', 'orange juice', 'mint lemonade', 'iced tea', 'hot chocolate'], 2),
            'preferred_taste'      => fake()->randomElement(['sweet', 'salty', 'spicy', 'sour', 'savory']),
            'dietary_preferences'  => fake()->randomElements(['vegetarian', 'low-calorie', 'high-protein', 'low-carb'], fake()->numberBetween(0, 1)),
            'price_preference'     => fake()->randomElement(['low', 'medium', 'high']),
            'spicy_level'          => fake()->numberBetween(0, 5),
            'favorite_ingredients' => fake()->randomElements(['chicken', 'cheese', 'beef', 'tomato', 'garlic', 'chocolate', 'mozzarella'], 3),
            'disliked_ingredients' => fake()->randomElements(['mushroom', 'olives', 'onion', 'tuna', 'pickles'], 2),
        ];
    }
}
