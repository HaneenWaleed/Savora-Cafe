<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeverageFactory extends Factory
{

    protected const MENU = [
        'Flat White'        => ['coffee', 'Smooth espresso with velvety steamed milk.', 90, 'medium', ['espresso', 'milk'], 130, 'hot'],
        'Caramel Macchiato' => ['coffee', 'Espresso with milk and caramel drizzle.', 95, 'large', ['espresso', 'milk', 'caramel'], 240, 'hot'],
        'Strawberry Juice'  => ['juices', 'Fresh strawberry juice.', 65, 'medium', ['strawberry', 'sugar'], 140, 'cold'],
        'Guava Juice'       => ['juices', 'Thick and sweet guava juice.', 55, 'medium', ['guava', 'sugar'], 150, 'cold'],
        'Coca-Cola'         => ['soft-drinks', 'Classic cola drink.', 25, 'medium', ['carbonated water', 'sugar', 'caffeine'], 140, 'cold'],
        'Fanta Orange'      => ['soft-drinks', 'Orange sparkling drink.', 25, 'medium', ['carbonated water', 'sugar', 'orange'], 150, 'cold'],
        'Karak Tea'         => ['hot-drinks', 'Spiced milk tea with cardamom.', 35, 'small', ['black tea', 'milk', 'cardamom', 'sugar'], 110, 'hot'],
        'Sahlab'            => ['hot-drinks', 'Warm creamy sahlab with nuts and coconut.', 45, 'medium', ['milk', 'sahlab', 'nuts', 'coconut'], 260, 'hot'],
        'Iced Mocha'        => ['cold-drinks', 'Cold espresso with chocolate and milk.', 85, 'large', ['espresso', 'chocolate', 'milk', 'ice'], 280, 'cold'],
        'Berry Smoothie'    => ['cold-drinks', 'Blended mixed berries with yogurt.', 70, 'large', ['strawberry', 'blueberry', 'yogurt', 'ice'], 190, 'cold'],
    ];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(array_keys(self::MENU));
        [$slug, $description, $price, $size, $ingredients, $calories, $temperature] = self::MENU[$name];

        return [
            'category_id' => Category::where('slug', $slug)->value('id'),
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'size'        => $size,
            'ingredients' => $ingredients,
            'calories'    => $calories,
            'temperature' => $temperature,
            'quantity'    => fake()->numberBetween(10, 80),
            'image'       => null,
            'status'      => true,
        ];
    }
}
