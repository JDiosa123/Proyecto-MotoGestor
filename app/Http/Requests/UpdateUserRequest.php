<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')),
                'regex:/^[A-Za-z0-9._%+-]+@motogestor\.com$/',
            ],
            'role' => ['required', Rule::in(['admin'])],
            'status' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
            'email.regex' => 'El email debe ser del dominio @motogestor.com.',
            'email.unique' => 'Este email ya está registrado.',
        ];
    }
}
