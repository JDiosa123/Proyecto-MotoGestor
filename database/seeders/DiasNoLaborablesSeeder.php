<?php

namespace Database\Seeders;

use App\Models\DiaNoLaborable;
use Illuminate\Database\Seeder;

class DiasNoLaborablesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            '2026-11-16' => 'Independencia de Cartagena',
            '2026-12-08' => 'Inmaculada Concepción',
            '2026-12-25' => 'Navidad',
        ] as $fecha => $descripcion) {
            DiaNoLaborable::updateOrCreate(
                ['fecha' => $fecha],
                ['descripcion' => $descripcion]
            );
        }
    }
}
