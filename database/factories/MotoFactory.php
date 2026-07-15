<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Moto;
use Illuminate\Database\Eloquent\Factories\Factory;

class MotoFactory extends Factory
{
    protected $model = Moto::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'placa' => strtoupper(fake()->unique()->bothify('???-####')),
            'marca' => fake()->randomElement(['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Ducati', 'BMW']),
            'modelo' => fake()->word(),
            'cilindraje' => fake()->numberBetween(100, 1800),
            'color' => fake()->safeColorName(),
        ];
    }
}
