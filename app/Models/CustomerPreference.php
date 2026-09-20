<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPreference extends Model
{
    protected $fillable = [
        'user_id',
        'favorite_categories',
        'favorite_food_types',
        'favorite_beverages',
        'preferred_taste',
        'dietary_preferences',
        'price_preference',
        'spicy_level',
        'favorite_ingredients',
        'disliked_ingredients',
    ];

    protected function casts(): array
    {
        return [
            'favorite_categories'  => 'array',
            'favorite_food_types'  => 'array',
            'favorite_beverages'   => 'array',
            'dietary_preferences'  => 'array',
            'favorite_ingredients' => 'array',
            'disliked_ingredients' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}