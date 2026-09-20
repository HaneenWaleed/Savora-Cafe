<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Food
            ['name' => 'Pizza',       'type' => 'food'],
            ['name' => 'Sandwiches',  'type' => 'food'],
            ['name' => 'Burgers',     'type' => 'food'],
            ['name' => 'Pasta',       'type' => 'food'],
            ['name' => 'Desserts',    'type' => 'food'],
            // Beverages
            ['name' => 'Coffee',      'type' => 'beverage'],
            ['name' => 'Juices',      'type' => 'beverage'],
            ['name' => 'Soft Drinks', 'type' => 'beverage'],
            ['name' => 'Hot Drinks',  'type' => 'beverage'],
            ['name' => 'Cold Drinks', 'type' => 'beverage'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                ['name' => $category['name'], 'type' => $category['type']]
            );
        }
    }
}