<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FoodItem;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $items = [
            // Pizza
            ['category' => 'pizza', 'name' => 'Spicy Chicken Pizza', 'description' => 'Pizza with spicy grilled chicken, mozzarella and chili sauce.', 'price' => 140, 'ingredients' => ['chicken', 'mozzarella', 'chili', 'tomato sauce'], 'calories' => 850, 'spicy_level' => 4, 'quantity' => 30, 'preparation_time' => 20],
            ['category' => 'pizza', 'name' => 'Margherita Pizza', 'description' => 'Classic pizza with tomato sauce, mozzarella and basil.', 'price' => 110, 'ingredients' => ['mozzarella', 'tomato sauce', 'basil'], 'calories' => 700, 'spicy_level' => 0, 'quantity' => 25, 'preparation_time' => 18],
            ['category' => 'pizza', 'name' => 'Mushroom Pizza', 'description' => 'Pizza topped with fresh mushrooms and cheese.', 'price' => 130, 'ingredients' => ['mushroom', 'mozzarella', 'tomato sauce', 'olives'], 'calories' => 720, 'spicy_level' => 0, 'quantity' => 20, 'preparation_time' => 18],

            // Sandwiches
            ['category' => 'sandwiches', 'name' => 'Chicken Sandwich', 'description' => 'Grilled chicken with lettuce, garlic sauce and pickles.', 'price' => 120, 'ingredients' => ['chicken', 'lettuce', 'garlic sauce', 'pickles', 'bread'], 'calories' => 520, 'spicy_level' => 1, 'quantity' => 40, 'preparation_time' => 10],
            ['category' => 'sandwiches', 'name' => 'Cheese Sandwich', 'description' => 'Melted cheddar and mozzarella in toasted bread.', 'price' => 70, 'ingredients' => ['cheddar', 'mozzarella', 'bread', 'butter'], 'calories' => 430, 'spicy_level' => 0, 'quantity' => 35, 'preparation_time' => 8],
            ['category' => 'sandwiches', 'name' => 'Falafel Sandwich', 'description' => 'Crispy falafel with tahini, tomato and salad.', 'price' => 45, 'ingredients' => ['falafel', 'tahini', 'tomato', 'lettuce', 'bread'], 'calories' => 380, 'spicy_level' => 1, 'quantity' => 50, 'preparation_time' => 6],

            // Burgers
            ['category' => 'burgers', 'name' => 'Beef Burger', 'description' => 'Juicy beef patty with cheddar, lettuce and tomato.', 'price' => 160, 'ingredients' => ['beef', 'cheddar', 'lettuce', 'tomato', 'bun'], 'calories' => 780, 'spicy_level' => 1, 'quantity' => 25, 'preparation_time' => 15],
            ['category' => 'burgers', 'name' => 'Chicken Burger', 'description' => 'Crispy chicken fillet with cheese and mayo.', 'price' => 135, 'ingredients' => ['chicken', 'cheddar', 'lettuce', 'mayo', 'bun'], 'calories' => 690, 'spicy_level' => 2, 'quantity' => 3, 'preparation_time' => 14], // كمية قليلة عشان نجرب Low stock

            // Pasta
            ['category' => 'pasta', 'name' => 'Spicy Chicken Pasta', 'description' => 'Penne with spicy chicken in creamy tomato sauce.', 'price' => 150, 'ingredients' => ['chicken', 'penne', 'chili', 'cream', 'tomato sauce'], 'calories' => 760, 'spicy_level' => 4, 'quantity' => 20, 'preparation_time' => 18],
            ['category' => 'pasta', 'name' => 'Alfredo Pasta', 'description' => 'Fettuccine in creamy parmesan sauce.', 'price' => 145, 'ingredients' => ['fettuccine', 'cream', 'parmesan', 'garlic', 'chicken'], 'calories' => 820, 'spicy_level' => 0, 'quantity' => 18, 'preparation_time' => 17],

            // Desserts
            ['category' => 'desserts', 'name' => 'Chocolate Cake', 'description' => 'Rich chocolate cake with chocolate ganache.', 'price' => 65, 'ingredients' => ['chocolate', 'flour', 'eggs', 'sugar', 'butter'], 'calories' => 450, 'spicy_level' => 0, 'quantity' => 15, 'preparation_time' => 3],
            ['category' => 'desserts', 'name' => 'Om Ali', 'description' => 'Traditional Egyptian dessert with milk, nuts and pastry.', 'price' => 55, 'ingredients' => ['milk', 'pastry', 'nuts', 'raisins', 'sugar'], 'calories' => 480, 'spicy_level' => 0, 'quantity' => 12, 'preparation_time' => 5],
        ];

        foreach
         ($items as $item) {
            FoodItem::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id'      => $categories[$item['category']],
                    'description'      => $item['description'],
                    'price'            => $item['price'],
                    'ingredients'      => $item['ingredients'],
                    'calories'         => $item['calories'],
                    'spicy_level'      => $item['spicy_level'],
                    'quantity'         => $item['quantity'],
                    'preparation_time' => $item['preparation_time'],
                    'status'           => true,
                ]
            );
        }
    }
}