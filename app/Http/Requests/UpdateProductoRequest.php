<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
        ];
    }
}
