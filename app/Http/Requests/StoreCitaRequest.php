<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role !== User::ROLE_CLIENT || $this->user()->cliente !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()?->role === User::ROLE_CLIENT && $this->user()->cliente) {
            $this->merge([
                'cliente_id' => $this->user()->cliente->id,
                'mecanico_id' => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'moto_id' => ['required', Rule::exists('motos', 'id')->where('cliente_id', $this->input('cliente_id'))],
            'mecanico_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_MECHANIC)],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'servicio' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente no válido.',
            'moto_id.exists' => 'Moto no válida.',
            'mecanico_id.exists' => 'Mecánico no válido.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
            'servicio.required' => 'El servicio es obligatorio.',
        ];
    }
}
