<?php

namespace App\Services;

use App\Models\DiaNoLaborable;
use Carbon\CarbonImmutable;

class HorarioTaller
{
    public function esDiaLaboral(string $fecha): bool
    {
        $dia = CarbonImmutable::parse($fecha, config('app.timezone'));

        return isset(config('taller.horarios')[$dia->dayOfWeekIso])
            && ! DiaNoLaborable::query()->whereDate('fecha', $dia->toDateString())->exists();
    }

    public function esHoraValida(string $fecha, string $hora): bool
    {
        if (! preg_match('/^\d{2}:\d{2}$/', $hora)) {
            return false;
        }

        [$horaNumero, $minutoNumero] = array_map('intval', explode(':', $hora));
        if ($horaNumero > 23 || $minutoNumero > 59) {
            return false;
        }

        $dia = CarbonImmutable::parse($fecha, config('app.timezone'));
        $horario = config('taller.horarios')[$dia->dayOfWeekIso] ?? null;

        if (! $horario || ! $this->esDiaLaboral($fecha)) {
            return false;
        }

        $horaNormalizada = CarbonImmutable::createFromFormat('!H:i', substr($hora, 0, 5), config('app.timezone'));
        $apertura = CarbonImmutable::createFromFormat('!H:i', $horario['apertura'], config('app.timezone'));
        $cierre = CarbonImmutable::createFromFormat('!H:i', $horario['cierre'], config('app.timezone'))
            ->subMinutes((int) config('taller.margen_cierre_minutos'));

        if (! $horaNormalizada || ! $apertura || ! $cierre) {
            return false;
        }

        $minutosDesdeApertura = ($horaNormalizada->hour * 60 + $horaNormalizada->minute)
            - ($apertura->hour * 60 + $apertura->minute);

        return $minutosDesdeApertura >= 0
            && $horaNormalizada->lessThanOrEqualTo($cierre)
            && $minutosDesdeApertura % (int) config('taller.paso_minutos') === 0;
    }
}
