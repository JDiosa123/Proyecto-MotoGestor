<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    protected $model = Cita::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'moto_id' => Moto::factory(),
            'mecanico_id' => User::factory(),
            'fecha' => fake()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
            'hora' => fake()->time('H:i:s'),
            'estado' => fake()->randomElement(['pendiente', 'confirmada', 'cancelada', 'completada']),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(['estado' => 'pendiente']);
    }
}
