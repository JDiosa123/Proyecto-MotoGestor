<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServiciosSeeder extends Seeder
{
    public function run(): void
    {
        $mantenimiento = Servicio::updateOrCreate(
            ['nombre' => 'Mantenimiento general'],
            [
                'tipo' => 'mantenimiento',
                'agendable' => true,
                'ocupa_dia_completo' => true,
                'dias_anticipacion_minima' => 1,
                'hora_ingreso' => '08:30',
                'activo' => true,
            ]
        );

        Servicio::updateOrCreate(
            ['nombre' => 'Mecánica rápida'],
            [
                'tipo' => 'mecanica_rapida',
                'agendable' => false,
                'ocupa_dia_completo' => false,
                'dias_anticipacion_minima' => 0,
                'hora_ingreso' => null,
                'activo' => true,
            ]
        );

        Cita::query()
            ->whereNull('servicio_id')
            ->whereRaw('LOWER(servicio) = ?', [mb_strtolower($mantenimiento->nombre)])
            ->update(['servicio_id' => $mantenimiento->id]);
    }
}
