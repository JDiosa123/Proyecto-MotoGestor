<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $motoId = $this->route('moto')?->id;

        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'placa' => ['required', 'string', Rule::unique('motos', 'placa')->ignore($motoId), 'regex:/^([A-Z]{3}\d{2}[A-Z]|[A-Z]{2}\d{3})$/'],
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'cilindraje' => ['nullable', 'integer'],
            'color' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente no válido.',
            'placa.unique' => 'La placa ya está registrada.',
            'placa.required' => 'La placa es obligatoria.',
            'placa.regex' => 'Formato inválido. Las placas deben ser XXX00A o XX000.',
            'cilindraje.integer' => 'El cilindraje debe ser un número.',
        ];
    }
}
