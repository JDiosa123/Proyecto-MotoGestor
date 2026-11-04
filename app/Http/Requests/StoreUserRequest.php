<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class StoreUserRequest extends FormRequest
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
                Rule::unique('users', 'email'),
                'regex:/^[A-Za-z0-9._%+-]+@motogestor\.com$/',
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(User::ROLES)],
            'status' => ['required', Rule::in(['activo', 'inactivo'])],
            'cliente_id' => [
                'nullable',
                'required_if:role,'.User::ROLE_CLIENT,
                Rule::exists('clientes', 'id')->whereNull('user_id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
            'email.regex' => 'El email debe ser del dominio @motogestor.com.',
            'email.unique' => 'Este email ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        return $validated;
    }
}
