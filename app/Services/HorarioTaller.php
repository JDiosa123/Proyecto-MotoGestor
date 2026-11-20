<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\DiaNoLaborable;
use App\Models\Servicio;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

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

    public function siguienteDiaHabil(string $fecha): string
    {
        $dia = CarbonImmutable::parse($fecha, config('app.timezone'))->startOfDay()->addDay();

        while (! $this->esDiaDisponibleParaServicio($dia->toDateString(), true)) {
            $dia = $dia->addDay();
        }

        return $dia->toDateString();
    }

    public function cupoDelDia(string $fecha): int
    {
        $cupoMaximo = (int) config('taller.cupo_maximo_dia');
        $mecanicosActivos = User::query()
            ->where('role', User::ROLE_MECHANIC)
            ->where('status', 'activo')
            ->count();

        return $mecanicosActivos === 0 ? $cupoMaximo : min($cupoMaximo, $mecanicosActivos);
    }

    public function cuposOcupados(string $fecha): int
    {
        return Cita::query()
            ->where('fecha', '>=', $fecha)
            ->where('fecha', '<', CarbonImmutable::parse($fecha, config('app.timezone'))->addDay()->toDateString())
            ->whereIn('estado', ['aceptada', 'atendida'])
            ->whereHas('servicioCatalogo', fn ($query) => $query->where('ocupa_dia_completo', true))
            ->count();
    }

    public function cuposDisponibles(string $fecha): int
    {
        return max(0, $this->cupoDelDia($fecha) - $this->cuposOcupados($fecha));
    }

    public function diasDisponibles(Servicio $servicio, ?string $desde = null): Collection
    {
        if (! $servicio->activo || ! $servicio->agendable) {
            return collect();
        }

        $hoy = CarbonImmutable::now(config('app.timezone'))->startOfDay();
        $inicio = CarbonImmutable::parse($desde ?? $hoy->toDateString(), config('app.timezone'))->startOfDay();
        $limite = $hoy->addDays((int) config('taller.dias_max_anticipacion'));
        $dias = collect();

        for ($dia = $inicio; $dia->lessThanOrEqualTo($limite); $dia = $dia->addDay()) {
            $fecha = $dia->toDateString();
            if (! $this->esDiaDisponibleParaServicio($fecha, $servicio->ocupa_dia_completo)) {
                continue;
            }

            $cupos = $this->cuposDisponibles($fecha);
            if ($cupos > 0) {
                $dias->push(['fecha' => $fecha, 'cupos' => $cupos]);
            }
        }

        return $dias;
    }

    public function esDiaDisponibleParaServicio(string $fecha, bool $ocupaDiaCompleto): bool
    {
        if (! $this->esDiaLaboral($fecha)) {
            return false;
        }

        $dia = CarbonImmutable::parse($fecha, config('app.timezone'));

        return ! ($ocupaDiaCompleto
            && $dia->dayOfWeekIso === 6
            && ! config('taller.sabado_dia_completo'));
    }
}
