<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:255', Rule::unique('clientes', 'documento')],
            'fecha_nacimiento' => ['nullable', 'date'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', Rule::unique('clientes', 'email')],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este email ya está registrado.',
            'documento.unique' => 'Este documento ya está registrado.',
        ];
    }
}
