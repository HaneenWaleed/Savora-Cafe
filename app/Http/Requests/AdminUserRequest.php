<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';
        return [
            'name'     => [$required, 'string', 'max:255'],
            'email'    => [$required, 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => [$required, 'string', 'min:8'],
            'role'     => ['sometimes', 'in:admin,customer'],
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
