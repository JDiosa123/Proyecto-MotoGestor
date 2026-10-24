<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->cliente !== null;
    }

    public function rules(): array
    {
        return [
            'motivo_cancelacion' => ['required', 'string', 'max:1000'],
        ];
    }
}
