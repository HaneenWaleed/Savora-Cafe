<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'favorite_categories'   => ['sometimes', 'nullable', 'array', 'max:10'],
            'favorite_categories.*' => ['integer', 'distinct', Rule::exists('categories', 'id')],
            'favorite_food_types'   => ['sometimes', 'nullable', 'array', 'max:10'],
            'favorite_food_types.*' => ['string', 'max:50'],
            'favorite_beverages'    => ['sometimes', 'nullable', 'array', 'max:10'],
            'favorite_beverages.*'  => ['string', 'max:50'],
            'preferred_taste'       => ['sometimes', 'nullable', 'in:sweet,salty,spicy,sour,savory'],
            'dietary_preferences'   => ['sometimes', 'nullable', 'array', 'max:6'],
            'dietary_preferences.*' => ['in:vegetarian,vegan,low-calorie,high-protein,low-carb,gluten-free'],
            'price_preference'      => ['sometimes', 'nullable', 'in:low,medium,high'],
            'spicy_level'           => ['sometimes', 'integer', 'between:0,5'],
            'favorite_ingredients'   => ['sometimes', 'nullable', 'array', 'max:20'],
            'favorite_ingredients.*' => ['string', 'max:50'],
            'disliked_ingredients'   => ['sometimes', 'nullable', 'array', 'max:20'],
            'disliked_ingredients.*' => ['string', 'max:50'],
        ];
    }
}
