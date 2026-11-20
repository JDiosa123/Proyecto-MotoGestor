<?php

namespace App\Http\Requests;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use App\Services\HorarioTaller;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role !== User::ROLE_CLIENT || $this->user()->cliente !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()?->role === User::ROLE_CLIENT && $this->user()->cliente) {
            $this->merge([
                'cliente_id' => $this->user()->cliente->id,
                'mecanico_id' => null,
            ]);
        }

        $servicio = is_numeric($this->input('servicio_id'))
            ? Servicio::find($this->input('servicio_id'))
            : null;

        $this->merge([
            'hora' => $servicio?->hora_ingreso ? substr($servicio->hora_ingreso, 0, 5) : null,
            'servicio' => $servicio?->nombre,
        ]);
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'moto_id' => ['required', Rule::exists('motos', 'id')->where('cliente_id', $this->input('cliente_id'))],
            'mecanico_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_MECHANIC)],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'servicio_id' => [
                'required',
                'integer',
                Rule::exists('servicios', 'id')->where('activo', true)->where('agendable', true),
            ],
            'servicio' => ['required', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $servicio = Servicio::query()
                ->whereKey($this->input('servicio_id'))
                ->where('activo', true)
                ->where('agendable', true)
                ->first();

            if (! $servicio || ! is_string($this->input('fecha'))) {
                return;
            }

            $fecha = $this->normalizeDate($this->input('fecha'));
            if (! $fecha) {
                return;
            }

            $horario = app(HorarioTaller::class);
            $hoy = CarbonImmutable::now(config('app.timezone'))->startOfDay();
            $dia = CarbonImmutable::parse($fecha, config('app.timezone'));

            if ($dia->lessThan($hoy)) {
                $validator->errors()->add('fecha', 'La fecha de la cita no puede ser anterior a hoy.');
            }

            if ($dia->greaterThan($hoy->addDays((int) config('taller.dias_max_anticipacion')))) {
                $validator->errors()->add('fecha', 'La fecha supera el máximo de días de anticipación permitido.');
            }

            if (! $horario->esDiaDisponibleParaServicio($fecha, $servicio->ocupa_dia_completo)) {
                $validator->errors()->add('fecha', 'El servicio no se puede agendar ese día.');
            }

            $minima = $hoy->toDateString();
            if ($this->user()?->role === User::ROLE_CLIENT) {
                for ($dias = 0; $dias < $servicio->dias_anticipacion_minima; $dias++) {
                    $minima = $horario->siguienteDiaHabil($minima);
                }

                if ($dia->lessThan(CarbonImmutable::parse($minima, config('app.timezone')))) {
                    $validator->errors()->add('fecha', 'Debes agendar desde el siguiente día hábil disponible.');
                }
            }

            if (! $servicio->hora_ingreso
                || ! $horario->esHoraValida($fecha, substr($servicio->hora_ingreso, 0, 5))) {
                $validator->errors()->add('fecha', 'El horario de ingreso del servicio no está disponible ese día.');
            }

            if ($horario->cuposDisponibles($fecha) <= 0) {
                $validator->errors()->add('fecha', 'Ese día ya no tiene cupos disponibles');
            }

            if (Cita::query()
                ->where('moto_id', $this->input('moto_id'))
                ->whereDate('fecha', $fecha)
                ->whereIn('estado', ['solicitada', 'aceptada'])
                ->exists()) {
                $validator->errors()->add('moto_id', 'La moto ya tiene una cita activa ese día.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'Cliente no válido.',
            'moto_id.exists' => 'Moto no válida.',
            'mecanico_id.exists' => 'Mecánico no válido.',
            'hora.date_format' => 'La hora de ingreso del servicio no es válida.',
            'servicio_id.required' => 'Seleccione un servicio agendable.',
            'servicio_id.exists' => 'El servicio seleccionado no está activo o no se puede agendar.',
            'servicio.required' => 'El servicio es obligatorio.',
            'fecha.date_format' => 'La fecha de la cita no es válida.',
        ];
    }

    private function normalizeDate(string $value): ?string
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        $date = CarbonImmutable::createFromFormat('!Y-m-d', $value, config('app.timezone'));

        return $date && $date->format('Y-m-d') === $value ? $date->toDateString() : null;
    }
}
