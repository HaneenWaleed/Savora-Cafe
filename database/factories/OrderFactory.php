<?php

namespace Database\Factories;

use App\Models\Beverage;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement([
            'completed', 'completed', 'completed', 'completed',
            'pending', 'preparing', 'ready', 'cancelled',
        ]);

        $date = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'user_id'        => User::where('role', 'customer')->inRandomOrder()->value('id') ?? User::factory(),
            'total_price'    => 0,
            'status'         => $status,
            'payment_status' => match ($status) {
                'completed' => 'paid',
                'cancelled' => 'unpaid',
                default     => fake()->randomElement(['unpaid', 'paid']),
            },
            'notes'          => fake()->boolean(20) ? fake()->sentence(4) : null,
            'created_at'     => $date,
            'updated_at'     => $date,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            $foods  = FoodItem::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get();
            $drinks = Beverage::inRandomOrder()->limit(fake()->numberBetween(0, 2))->get();

            $total = 0;

            foreach ($foods->concat($drinks) as $product) {
                $quantity = fake()->numberBetween(1, 3);
                $price    = (float) $product->price;
                $subtotal = $price * $quantity;

                $order->items()->create([
                    'orderable_type' => $product instanceof FoodItem ? 'food' : 'beverage',
                    'orderable_id'   => $product->id,
                    'quantity'       => $quantity,
                    'price'          => $price,
                    'subtotal'       => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total_price' => $total]);
        });
    }
}
