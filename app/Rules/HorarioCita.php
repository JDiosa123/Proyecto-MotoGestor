<?php

namespace App\Rules;

use App\Models\DiaNoLaborable;
use App\Services\HorarioTaller;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class HorarioCita implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function __construct(private readonly HorarioTaller $horarioTaller)
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $fecha = $this->data['fecha'] ?? $value;
        $hora = $this->data['hora'] ?? null;

        if (! is_string($fecha) || ! is_string($hora) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || ! preg_match('/^\d{2}:\d{2}$/', $hora)) {
            return;
        }

        $dia = CarbonImmutable::createFromFormat('!Y-m-d', $fecha, config('app.timezone'));
        if (! $dia || $dia->format('Y-m-d') !== $fecha) {
            return;
        }

        if ($dia->isSunday()) {
            $fail('El taller no atiende los domingos');

            return;
        }

        if (DiaNoLaborable::query()->whereDate('fecha', $fecha)->exists()) {
            $fail('Ese día es festivo');

            return;
        }

        if (! $this->horarioTaller->esHoraValida($fecha, $hora)) {
            $fail('El horario es de 8:30 a. m. a 6:00 p. m.; los sábados hasta las 12:00 m.');
        }
    }
}
