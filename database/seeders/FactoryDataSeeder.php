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
        // لو الداتا دي اتعملت قبل كده ما نكررهاش
        if (Order::exists()) {
            return;
        }

        // 10 عملاء، كل واحد له تفضيلات
        User::factory(10)
            ->has(CustomerPreference::factory(), 'preference')
            ->create();

        // أصناف إضافية فوق المنيو الأساسي
        FoodItem::factory(6)->create();
        Beverage::factory(4)->create();

        // أوردرات بأصناف حقيقية
        Order::factory(30)->create();
    }
}
