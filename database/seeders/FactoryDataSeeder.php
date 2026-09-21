<?php

namespace Database\Seeders;

use App\Models\Beverage;
use App\Models\CustomerPreference;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class FactoryDataSeeder extends Seeder
{
    public function run(): void
    {

        if (Order::exists()) {
            return;
        }


        User::factory(10)
            ->has(CustomerPreference::factory(), 'preference')
            ->create();


        FoodItem::factory(6)->create();
        Beverage::factory(4)->create();

        
        Order::factory(30)->create();
    }
}
