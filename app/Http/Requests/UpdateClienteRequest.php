<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente')?->id;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:255', Rule::unique('clientes', 'documento')->ignore($clienteId)],
            'fecha_nacimiento' => ['nullable', 'date', 'after:1900-01-01', 'before_or_equal:today'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'ciudad' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', Rule::unique('clientes', 'email')->ignore($clienteId)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Este email ya está registrado.',
            'documento.unique' => 'Este documento ya está registrado.',
            'fecha_nacimiento.after' => 'La fecha de nacimiento debe ser posterior al 1 de enero de 1900.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser posterior a hoy.',
        ];
    }
}
