<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).+$/'],
            'role' => ['required', Rule::in(['admin', 'mecanico', 'almacenista'])],
            'status' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
            'email.regex' => 'El email debe ser del dominio @motogestor.com.',
            'email.unique' => 'Este email ya está registrado.',
            'password.regex' => 'La contraseña debe tener mayúscula, minúscula, número y carácter especial.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
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
