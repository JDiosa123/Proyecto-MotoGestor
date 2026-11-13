<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Cita;
use App\Rules\FechaCitaFutura;
use App\Rules\HorarioCita;
use App\Services\HorarioTaller;
use Carbon\CarbonImmutable;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $validarHorario = $this->scheduleChanged();

        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'moto_id' => ['required', Rule::exists('motos', 'id')->where('cliente_id', $this->input('cliente_id'))],
            'mecanico_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_MECHANIC)],
            'fecha' => array_merge(
                ['required', 'date'],
                $validarHorario
                    ? [new FechaCitaFutura($this->user()?->role), new HorarioCita(app(HorarioTaller::class))]
                    : []
            ),
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

    private function scheduleChanged(): bool
    {
        $cita = $this->route('cita');
        if (! $cita instanceof Cita) {
            return true;
        }

        return $this->normalizeDate($this->input('fecha')) !== $cita->fecha->format('Y-m-d')
            || $this->normalizeTime($this->input('hora')) !== substr($cita->hora, 0, 5);
    }

    private function normalizeDate(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        $date = CarbonImmutable::createFromFormat('!Y-m-d', $value, config('app.timezone'));

        return $date && $date->format('Y-m-d') === $value ? $date->format('Y-m-d') : null;
    }

    private function normalizeTime(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $value)) {
            return null;
        }

        return substr($value, 0, 5);
    }
}
