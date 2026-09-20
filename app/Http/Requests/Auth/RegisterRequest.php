<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone'    => ['nullable', 'string', 'regex:/^01[0125][0-9]{8}$/'],
            'age'      => ['nullable', 'integer', 'between:10,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone must be a valid Egyptian mobile number (e.g. 01012345678).',
        ];
    }
}