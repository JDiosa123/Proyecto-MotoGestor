<?php

namespace Database\Factories;

use App\Models\Entrada;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntradaFactory extends Factory
{
    protected $model = Entrada::class;

    public function definition(): array
    {
        return [
            'id_producto' => Producto::factory(),
            'cantidad' => fake()->numberBetween(1, 50),
            'descripcion' => fake()->sentence(8),
            'creado_por' => User::factory(),
            'fecha' => fake()->date(),
        ];
    }
}
