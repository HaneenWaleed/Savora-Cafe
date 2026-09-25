<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignoreId = $this->user()->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes', 'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($ignoreId),
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^01[0125][0-9]{8}$/'],
            'age' => ['sometimes', 'nullable', 'integer', 'between:10,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone must be a valid Egyptian mobile number (e.g. 01012345678).',
        ];
    }
}
