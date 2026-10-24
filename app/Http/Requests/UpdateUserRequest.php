<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

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
            'role' => ['required', Rule::in(User::ROLES)],
            'status' => ['required', Rule::in(['activo', 'inactivo'])],
            'cliente_id' => [
                'nullable',
                'required_if:role,'.User::ROLE_CLIENT,
                Rule::exists('clientes', 'id')->where(function ($query) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', $this->route('user')->id);
                }),
            ],
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
