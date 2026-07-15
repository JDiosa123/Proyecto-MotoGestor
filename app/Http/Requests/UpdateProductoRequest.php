<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario responsable no existe.',
        ];
    }
}
