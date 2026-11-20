<?php

namespace App\Http\Requests;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use App\Services\HorarioTaller;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $cita = $this->route('cita');
        if ($cita instanceof Cita && ! $this->exists('servicio_id')) {
            $this->merge(['servicio_id' => $cita->servicio_id]);
        }
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['required', Rule::exists('clientes', 'id')],
            'moto_id' => ['required', Rule::exists('motos', 'id')->where('cliente_id', $this->input('cliente_id'))],
            'mecanico_id' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_MECHANIC)],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'estado' => [
                'required',
                Rule::in($this->user()?->role === User::ROLE_ASSISTANT
                    ? ['solicitada', 'aceptada', 'cancelada']
                    : ['solicitada', 'aceptada', 'cancelada', 'atendida']),
            ],
            'servicio_id' => ['nullable', 'integer', Rule::exists('servicios', 'id')],
            'servicio' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
            'motivo_cancelacion' => ['nullable', 'required_if:estado,cancelada', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $cita = $this->route('cita');
            $fecha = is_string($this->input('fecha'))
                ? $this->normalizeDate($this->input('fecha'))
                : null;

            if (! $this->scheduleOrServiceChanged()) {
                if (($this->motoChanged() || (
                    ! in_array($cita?->estado, ['solicitada', 'aceptada'], true)
                    && in_array($this->input('estado'), ['solicitada', 'aceptada'], true)
                )) && $fecha
                    && in_array($this->input('estado'), ['solicitada', 'aceptada'], true)
                    && Cita::query()
                        ->where('moto_id', $this->input('moto_id'))
                        ->whereDate('fecha', $fecha)
                        ->whereIn('estado', ['solicitada', 'aceptada'])
                        ->when($cita instanceof Cita, fn ($query) => $query->whereKeyNot($cita->getKey()))
                        ->exists()) {
                    $validator->errors()->add('moto_id', 'La moto ya tiene una cita activa ese día.');
                }

                return;
            }

            $servicio = Servicio::query()
                ->whereKey($this->input('servicio_id'))
                ->where('activo', true)
                ->where('agendable', true)
                ->first();

            if (! $servicio) {
                $validator->errors()->add('servicio_id', 'Seleccione un servicio activo y agendable.');

                return;
            }

            if (! is_string($this->input('fecha')) || ! ($fecha = $this->normalizeDate($this->input('fecha')))) {
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

            if ($this->user()?->role === User::ROLE_CLIENT) {
                $minima = $hoy->toDateString();
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

            $ocupados = $horario->cuposOcupados($fecha);
            if ($cita instanceof Cita
                && $cita->fecha->format('Y-m-d') === $fecha
                && in_array($cita->estado, ['aceptada', 'atendida'], true)
                && $cita->servicioCatalogo?->ocupa_dia_completo) {
                $ocupados--;
            }

            if ($ocupados >= $horario->cupoDelDia($fecha)) {
                $validator->errors()->add('fecha', 'Ese día ya no tiene cupos disponibles');
            }

            if (in_array($this->input('estado'), ['solicitada', 'aceptada'], true)
                && Cita::query()
                    ->where('moto_id', $this->input('moto_id'))
                    ->whereDate('fecha', $fecha)
                    ->whereIn('estado', ['solicitada', 'aceptada'])
                    ->when($cita instanceof Cita, fn ($query) => $query->whereKeyNot($cita->getKey()))
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
            'estado.in' => 'El estado de la cita no es válido.',
            'motivo_cancelacion.required_if' => 'Indique el motivo de cancelación.',
        ];
    }

    private function scheduleOrServiceChanged(): bool
    {
        return $this->scheduleChanged() || $this->serviceChanged();
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

    private function serviceChanged(): bool
    {
        $cita = $this->route('cita');
        if (! $cita instanceof Cita || ! $this->exists('servicio_id')) {
            return false;
        }

        $newServiceId = $this->input('servicio_id');
        $currentServiceId = $cita->servicio_id === null ? null : (int) $cita->servicio_id;

        return ($newServiceId === null ? null : (int) $newServiceId) !== $currentServiceId;
    }

    private function motoChanged(): bool
    {
        $cita = $this->route('cita');

        return $cita instanceof Cita
            && (int) $this->input('moto_id') !== (int) $cita->moto_id;
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
