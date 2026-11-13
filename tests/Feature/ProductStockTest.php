<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_shows_stock_minimum_with_zero_as_the_initial_value(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('productos.create'))
            ->assertOk()
            ->assertSee('Stock mínimo')
            ->assertSee('value="0"', false)
            ->assertSee('Al llegar a esta cantidad el producto se marca como crítico');
    }

    public function test_store_rejects_negative_decimal_and_empty_minimum_stock(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([-1, '1.5', ''] as $stockMinimum) {
            $payload = $this->validProductPayload();
            $payload['stock_minimo'] = $stockMinimum;

            $this->actingAs($admin)
                ->from(route('productos.create'))
                ->post(route('productos.store'), $payload)
                ->assertRedirect(route('productos.create'))
                ->assertSessionHasErrors('stock_minimo');
        }

        $this->assertDatabaseCount('productos', 0);
    }

    public function test_store_saves_a_valid_minimum_stock(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('productos.store'), $this->validProductPayload(['stock_minimo' => 5]))
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Pastillas de freno',
            'stock_minimo' => 5,
            'cantidad' => 0,
            'user_id' => $admin->id,
        ]);
    }

    public function test_update_saves_a_valid_minimum_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['stock_minimo' => 2]);

        $this->actingAs($admin)
            ->put(route('productos.actualizar', $producto), [
                'nombre' => $producto->nombre,
                'categoria' => $producto->categoria,
                'precio' => $producto->precio,
                'stock_minimo' => 5,
                'descripcion' => $producto->descripcion,
                'fecha_registro' => $producto->fecha_registro,
            ])
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'stock_minimo' => 5,
        ]);
    }

    public function test_critical_threshold_includes_stock_equal_to_or_below_the_minimum(): void
    {
        $above = Producto::factory()->create(['cantidad' => 6, 'stock_minimo' => 5]);
        $equal = Producto::factory()->create(['cantidad' => 5, 'stock_minimo' => 5]);
        $below = Producto::factory()->create(['cantidad' => 4, 'stock_minimo' => 5]);

        $this->assertFalse($above->esCritico());
        $this->assertTrue($equal->esCritico());
        $this->assertTrue($below->esCritico());
        $this->assertEqualsCanonicalizing(
            [$equal->id, $below->id],
            Producto::critico()->pluck('id')->all()
        );
    }

    public function test_product_list_marks_only_products_at_or_below_the_minimum_as_critical(): void
    {
        $admin = User::factory()->admin()->create();
        Producto::factory()->create([
            'nombre' => 'Producto Normal',
            'cantidad' => 6,
            'stock_minimo' => 5,
        ]);
        Producto::factory()->create([
            'nombre' => 'Producto En Umbral',
            'cantidad' => 5,
            'stock_minimo' => 5,
        ]);
        Producto::factory()->create([
            'nombre' => 'Producto Bajo Umbral',
            'cantidad' => 4,
            'stock_minimo' => 5,
        ]);

        $response = $this->actingAs($admin)->get(route('productos.index'));
        $response->assertOk()
            ->assertSee('Stock mínimo')
            ->assertSee('Producto Normal')
            ->assertSee('Producto En Umbral')
            ->assertSee('Producto Bajo Umbral');

        $plainText = strip_tags($response->getContent());
        $this->assertSame(2, substr_count($plainText, 'Crítico'));
        $this->assertSame(1, substr_count($plainText, 'OK'));

        $assistant = User::factory()->asistente()->create();
        $this->actingAs($assistant)
            ->get(route('productos.index'))
            ->assertOk()
            ->assertSee('Crítico')
            ->assertDontSee('Crear producto');
    }

    public function test_mechanic_and_client_cannot_access_product_creation(): void
    {
        foreach ([User::ROLE_MECHANIC, User::ROLE_CLIENT] as $role) {
            $user = User::factory()->state(['role' => $role])->create();

            $this->actingAs($user)
                ->get(route('productos.create'))
                ->assertForbidden();

            $this->post(route('productos.store'), $this->validProductPayload())
                ->assertForbidden();
        }
    }

    private function validProductPayload(array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Pastillas de freno',
            'categoria' => 'Repuestos',
            'precio' => 25.50,
            'stock_minimo' => 0,
            'descripcion' => 'Juego delantero',
            'fecha_registro' => now(config('app.timezone'))->toDateString(),
        ], $overrides);
    }
}
