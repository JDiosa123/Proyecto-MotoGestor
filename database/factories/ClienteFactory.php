<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'documento' => fake()->unique()->numerify('#########'),
            'fecha_nacimiento' => fake()->date(),
            'direccion' => fake()->address(),
            'ciudad' => fake()->city(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    public function adulto(): static
    {
        return $this->state(fn () => [
            'fecha_nacimiento' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
        ]);
    }
}
