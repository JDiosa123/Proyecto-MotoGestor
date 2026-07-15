<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'categoria' => fake()->randomElement(['Repuestos', 'Accesorios', 'Lubricantes', 'Herramientas']),
            'cantidad' => fake()->numberBetween(0, 120),
            'precio' => fake()->randomFloat(2, 5, 1200),
            'descripcion' => fake()->sentence(10),
            'fecha_registro' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'user_id' => User::factory(),
        ];
    }

    public function inStock(): static
    {
        return $this->state(fn () => [
            'cantidad' => fake()->numberBetween(1, 120),
        ]);
    }
}
