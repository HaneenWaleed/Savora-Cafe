<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodItemFactory extends Factory
{
    // الاسم => [category slug, الوصف, السعر, المكونات, السعرات, درجة الحرارة]
    protected const MENU = [
        'Pepperoni Pizza'       => ['pizza', 'Pizza with pepperoni slices and melted mozzarella.', 125, ['pepperoni', 'mozzarella', 'tomato sauce'], 800, 1],
        'Four Cheese Pizza'     => ['pizza', 'Mozzarella, cheddar, parmesan and blue cheese.', 135, ['mozzarella', 'cheddar', 'parmesan', 'blue cheese'], 830, 0],
        'BBQ Chicken Pizza'     => ['pizza', 'Grilled chicken with smoky BBQ sauce and onions.', 145, ['chicken', 'bbq sauce', 'onion', 'mozzarella'], 860, 1],

        'Beef Shawarma Sandwich' => ['sandwiches', 'Sliced beef shawarma with tahini and pickles.', 85, ['beef', 'tahini', 'pickles', 'bread'], 540, 1],
        'Tuna Sandwich'          => ['sandwiches', 'Tuna salad with lettuce and tomato.', 75, ['tuna', 'lettuce', 'tomato', 'mayo', 'bread'], 410, 0],
        'Crispy Chicken Sandwich' => ['sandwiches', 'Crispy fried chicken with coleslaw and mayo.', 110, ['chicken', 'coleslaw', 'mayo', 'bread'], 610, 2],

        'Double Cheese Burger'  => ['burgers', 'Two beef patties with double cheddar.', 175, ['beef', 'cheddar', 'lettuce', 'pickles', 'bun'], 950, 0],
        'BBQ Burger'            => ['burgers', 'Beef patty with BBQ sauce and crispy onions.', 165, ['beef', 'bbq sauce', 'onion', 'cheddar', 'bun'], 880, 1],
        'Spicy Crispy Burger'   => ['burgers', 'Spicy fried chicken fillet with jalapeno.', 140, ['chicken', 'jalapeno', 'chili', 'lettuce', 'bun'], 760, 4],

        'Penne Arrabbiata'      => ['pasta', 'Penne in a hot tomato and garlic sauce.', 110, ['penne', 'tomato sauce', 'garlic', 'chili'], 620, 3],
        'Beef Lasagna'          => ['pasta', 'Layered pasta with beef ragu and bechamel.', 155, ['pasta', 'beef', 'bechamel', 'mozzarella'], 900, 0],
        'Pesto Pasta'           => ['pasta', 'Pasta with basil pesto and parmesan.', 130, ['pasta', 'basil', 'parmesan', 'pine nuts'], 700, 0],

        'Cheesecake'            => ['desserts', 'Creamy cheesecake with strawberry topping.', 70, ['cream cheese', 'biscuit', 'strawberry', 'sugar'], 420, 0],
        'Brownies'              => ['desserts', 'Warm chocolate brownies with walnuts.', 50, ['chocolate', 'flour', 'walnuts', 'butter', 'eggs'], 380, 0],
        'Basbousa'              => ['desserts', 'Semolina cake soaked in sweet syrup.', 40, ['semolina', 'coconut', 'sugar', 'butter'], 350, 0],
    ];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(array_keys(self::MENU));
        [$slug, $description, $price, $ingredients, $calories, $spicy] = self::MENU[$name];

        return [
            'category_id'      => Category::where('slug', $slug)->value('id'),
            'name'             => $name,
            'description'      => $description,
            'price'            => $price,
            'ingredients'      => $ingredients,
            'calories'         => $calories,
            'spicy_level'      => $spicy,
            'quantity'         => fake()->numberBetween(5, 60),
            'preparation_time' => fake()->numberBetween(5, 25),
            'image'            => null,
            'status'           => true,
        ];
    }
}
