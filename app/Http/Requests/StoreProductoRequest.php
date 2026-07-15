<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'fecha_registro' => ['required', 'date'],
            'user_id' => ['required', Rule::exists('users', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario responsable no existe.',
        ];
    }
}
