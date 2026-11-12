<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivo_cancelacion' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_cancelacion.required' => 'El motivo del rechazo es obligatorio.',
            'motivo_cancelacion.min' => 'El motivo del rechazo debe tener al menos 5 caracteres.',
            'motivo_cancelacion.max' => 'El motivo del rechazo no puede superar los 255 caracteres.',
            'motivo_cancelacion.string' => 'El motivo del rechazo debe ser texto.',
        ];
    }
}
