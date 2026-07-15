<?php

namespace Database\Factories;

use App\Models\Salida;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalidaFactory extends Factory
{
    protected $model = Salida::class;

    public function definition(): array
    {
        return [
            'id_producto' => Producto::factory(),
            'cantidad' => fake()->numberBetween(1, 30),
            'descripcion' => fake()->sentence(8),
            'creado_por' => User::factory(),
            'fecha' => fake()->date(),
        ];
    }
}
