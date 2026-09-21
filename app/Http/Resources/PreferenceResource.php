<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'favorite_categories'  => $this->favorite_categories ?? [],
            'favorite_food_types'  => $this->favorite_food_types ?? [],
            'favorite_beverages'   => $this->favorite_beverages ?? [],
            'preferred_taste'      => $this->preferred_taste,
            'dietary_preferences'  => $this->dietary_preferences ?? [],
            'price_preference'     => $this->price_preference,
            'spicy_level'          => $this->spicy_level,
            'favorite_ingredients' => $this->favorite_ingredients ?? [],
            'disliked_ingredients' => $this->disliked_ingredients ?? [],
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
