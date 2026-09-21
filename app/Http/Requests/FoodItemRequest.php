<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FoodItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        return [
            'category_id'      => [$required, Rule::exists('categories', 'id')->where('type', 'food')],
            'name'             => [$required, 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'price'            => [$required, 'numeric', 'min:0', 'max:99999.99'],
            'ingredients'      => ['nullable', 'array'],
            'ingredients.*'    => ['string', 'max:50'],
            'calories'         => ['nullable', 'integer', 'min:0'],
            'spicy_level'      => ['sometimes', 'integer', 'between:0,5'],
            'quantity'         => ['sometimes', 'integer', 'min:0'],
            'preparation_time' => ['nullable', 'integer', 'min:0', 'max:600'],
            'image'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'           => ['sometimes', 'boolean'],
        ];
    }
}
