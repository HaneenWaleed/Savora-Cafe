<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BeverageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        return [
            'category_id'   => [$required, Rule::exists('categories', 'id')->where('type', 'beverage')],
            'name'          => [$required, 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'price'         => [$required, 'numeric', 'min:0', 'max:99999.99'],
            'size'          => ['sometimes', 'in:small,medium,large'],
            'ingredients'   => ['nullable', 'array'],
            'ingredients.*' => ['string', 'max:50'],
            'calories'      => ['nullable', 'integer', 'min:0'],
            'temperature'   => ['sometimes', 'in:hot,cold'],
            'quantity'      => ['sometimes', 'integer', 'min:0'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'        => ['sometimes', 'boolean'],
        ];
    }
}
