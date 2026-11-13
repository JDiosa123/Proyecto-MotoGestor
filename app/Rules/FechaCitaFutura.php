<?php

namespace App\Rules;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class FechaCitaFutura implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function __construct(private readonly ?string $role)
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return;
        }

        $fecha = CarbonImmutable::createFromFormat('!Y-m-d', $value, config('app.timezone'));
        if (! $fecha || $fecha->format('Y-m-d') !== $value) {
            return;
        }

        $ahora = CarbonImmutable::now(config('app.timezone'));
        if ($fecha->startOfDay()->lessThan($ahora->startOfDay())) {
            $fail('La fecha de la cita debe ser hoy o posterior.');

            return;
        }

        if ($this->role !== User::ROLE_CLIENT || ! $fecha->isSameDay($ahora)) {
            return;
        }

        $hora = $this->data['hora'] ?? null;
        if (! is_string($hora) || ! preg_match('/^\d{2}:\d{2}$/', $hora)) {
            return;
        }

        [$horaNumero, $minutoNumero] = array_map('intval', explode(':', $hora));
        if ($horaNumero > 23 || $minutoNumero > 59) {
            return;
        }

        $fechaHora = CarbonImmutable::createFromFormat('!Y-m-d H:i', $value.' '.$hora, config('app.timezone'));
        if ($fechaHora && $fechaHora->lessThanOrEqualTo($ahora)) {
            $fail('Para hoy, la hora de la cita debe ser posterior a la hora actual.');
        }
    }
}
