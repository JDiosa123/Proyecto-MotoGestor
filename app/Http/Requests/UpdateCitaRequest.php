<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'moto_id' => ['required', Rule::exists('motos', 'id')],
            'mecanico_id' => ['required', Rule::exists('users', 'id')],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente no válido.',
            'moto_id.exists' => 'Moto no válida.',
            'mecanico_id.exists' => 'Mecánico no válido.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
        ];
    }
}
