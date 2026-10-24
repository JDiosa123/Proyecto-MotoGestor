<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

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
            'moto_id' => ['required', Rule::exists('motos', 'id')->where('cliente_id', $this->input('cliente_id'))],
            'mecanico_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_MECHANIC)],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'estado' => [
                'required',
                Rule::in($this->user()?->role === User::ROLE_ASSISTANT
                    ? ['solicitada', 'aceptada', 'cancelada']
                    : ['solicitada', 'aceptada', 'cancelada', 'atendida']),
            ],
            'servicio' => ['required', 'string', 'max:255'],
            'motivo_cancelacion' => ['nullable', 'required_if:estado,cancelada', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente no válido.',
            'moto_id.exists' => 'Moto no válida.',
            'mecanico_id.exists' => 'Mecánico no válido.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
            'estado.in' => 'El estado de la cita no es válido.',
            'servicio.required' => 'El servicio es obligatorio.',
            'motivo_cancelacion.required_if' => 'Indique el motivo de cancelación.',
        ];
    }
}
