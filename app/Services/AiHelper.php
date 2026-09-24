<?php

namespace App\Services;

use App\Models\Beverage;
use App\Models\FoodItem;

class AiHelper
{
    
    public function menuSummary(): string
    {
        $food = FoodItem::available()->with('category')->orderBy('name')->get()
            ->map(fn ($i) => sprintf(
                '#%d | %s | category: %s | price: %s EGP | calories: %d | spicy_level: %d/5 | ingredients: %s',
                $i->id,
                $i->name,
                $i->category?->name ?? 'N/A',
                $i->price,
                $i->calories ?? 0,
                $i->spicy_level,
                implode(', ', $i->ingredients ?? [])
            ));

        $beverages = Beverage::available()->with('category')->orderBy('name')->get()
            ->map(fn ($i) => sprintf(
                '#%d | %s | category: %s | price: %s EGP | calories: %d | temperature: %s | ingredients: %s',
                $i->id,
                $i->name,
                $i->category?->name ?? 'N/A',
                $i->price,
                $i->calories ?? 0,
                $i->temperature,
                implode(', ', $i->ingredients ?? [])
            ));

        return "FOOD ITEMS (each line: #id | name | category | price | calories | spicy_level 0-5 | ingredients):\n"
            . $food->implode("\n")
            . "\n\nBEVERAGES (each line: #id | name | category | price | calories | temperature hot/cold | ingredients):\n"
            . $beverages->implode("\n");
    }
}
