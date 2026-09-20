<?php

namespace Database\Seeders;

use App\Models\Beverage;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BeverageSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $items = [
            // Coffee
            ['category' => 'coffee', 'name' => 'Cappuccino', 'description' => 'Espresso with steamed milk and thick foam.', 'price' => 80, 'size' => 'medium', 'ingredients' => ['espresso', 'milk', 'milk foam'], 'calories' => 120, 'temperature' => 'hot', 'quantity' => 60],
            ['category' => 'coffee', 'name' => 'Iced Latte', 'description' => 'Chilled espresso with cold milk over ice.', 'price' => 85, 'size' => 'large', 'ingredients' => ['espresso', 'milk', 'ice'], 'calories' => 150, 'temperature' => 'cold', 'quantity' => 50],
            ['category' => 'coffee', 'name' => 'Turkish Coffee', 'description' => 'Traditional strong coffee served in a small cup.', 'price' => 40, 'size' => 'small', 'ingredients' => ['coffee', 'sugar', 'cardamom'], 'calories' => 30, 'temperature' => 'hot', 'quantity' => 80],

            // Juices
            ['category' => 'juices', 'name' => 'Fresh Orange Juice', 'description' => 'Freshly squeezed orange juice, no added sugar.', 'price' => 60, 'size' => 'medium', 'ingredients' => ['orange'], 'calories' => 110, 'temperature' => 'cold', 'quantity' => 40],
            ['category' => 'juices', 'name' => 'Mango Juice', 'description' => 'Thick and sweet Egyptian mango juice.', 'price' => 65, 'size' => 'medium', 'ingredients' => ['mango', 'sugar'], 'calories' => 180, 'temperature' => 'cold', 'quantity' => 35],

            // Soft drinks
            ['category' => 'soft-drinks', 'name' => 'Pepsi', 'description' => 'Classic cola drink.', 'price' => 25, 'size' => 'medium', 'ingredients' => ['carbonated water', 'sugar', 'caffeine'], 'calories' => 150, 'temperature' => 'cold', 'quantity' => 100],
            ['category' => 'soft-drinks', 'name' => 'Sprite', 'description' => 'Lemon-lime sparkling drink.', 'price' => 25, 'size' => 'medium', 'ingredients' => ['carbonated water', 'sugar', 'lemon'], 'calories' => 140, 'temperature' => 'cold', 'quantity' => 2], // low stock

            // Hot drinks
            ['category' => 'hot-drinks', 'name' => 'Hot Chocolate', 'description' => 'Creamy hot chocolate topped with cream.', 'price' => 75, 'size' => 'medium', 'ingredients' => ['chocolate', 'milk', 'cream'], 'calories' => 250, 'temperature' => 'hot', 'quantity' => 30],
            ['category' => 'hot-drinks', 'name' => 'Green Tea', 'description' => 'Light and refreshing green tea.', 'price' => 30, 'size' => 'small', 'ingredients' => ['green tea'], 'calories' => 5, 'temperature' => 'hot', 'quantity' => 70],

            // Cold drinks
            ['category' => 'cold-drinks', 'name' => 'Mint Lemonade', 'description' => 'Cold lemonade blended with fresh mint.', 'price' => 50, 'size' => 'large', 'ingredients' => ['lemon', 'mint', 'sugar', 'ice'], 'calories' => 100, 'temperature' => 'cold', 'quantity' => 45],
            ['category' => 'cold-drinks', 'name' => 'Iced Tea', 'description' => 'Peach-flavored iced tea.', 'price' => 45, 'size' => 'medium', 'ingredients' => ['black tea', 'peach', 'sugar', 'ice'], 'calories' => 90, 'temperature' => 'cold', 'quantity' => 40],
        ];

        foreach ($items as $item) {
            Beverage::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $categories[$item['category']],
                    'description' => $item['description'],
                    'price'       => $item['price'],
                    'size'        => $item['size'],
                    'ingredients' => $item['ingredients'],
                    'calories'    => $item['calories'],
                    'temperature' => $item['temperature'],
                    'quantity'    => $item['quantity'],
                    'status'      => true,
                ]
            );
        }
    }
}