<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'moto_id' => ['required', Rule::exists('motos', 'id')->where('cliente_id', $this->input('cliente_id'))],
            'mecanico_id' => ['nullable', Rule::exists('users', 'id')],
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
