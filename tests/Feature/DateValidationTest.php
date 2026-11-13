<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DateValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-12 10:15:00', 'America/Bogota'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_inventory_and_product_forms_allow_today_and_yesterday_dates(): void
    {
        $admin = User::factory()->admin()->create();
        $today = now(config('app.timezone'))->toDateString();
        $yesterday = now(config('app.timezone'))->subDay()->toDateString();

        foreach ([$today, $yesterday] as $date) {
            $this->actingAs($admin)
                ->post(route('entradas.store'), [
                    'nombre' => 'Repuesto '.$date,
                    'categoria' => 'General',
                    'precio' => 10,
                    'cantidad' => 1,
                    'fecha' => $date,
                ])
                ->assertRedirect(route('entradas.index'));

            $producto = Producto::factory()->create(['cantidad' => 3]);
            $this->post(route('salidas.store'), [
                'id_producto' => $producto->id,
                'cantidad' => 1,
                'fecha' => $date,
            ])->assertRedirect(route('salidas.index'));

            $this->post(route('productos.store'), $this->productPayload([
                'nombre' => 'Producto '.$date,
                'fecha_registro' => $date,
            ]))->assertRedirect(route('productos.index'));

            $this->assertDatabaseHas('productos', ['nombre' => 'Producto '.$date, 'fecha_registro' => $date.' 00:00:00']);
        }

        $this->assertDatabaseHas('movimientos_inventario', ['tipo' => 'entrada', 'fecha' => $today.' 00:00:00']);
        $this->assertDatabaseHas('movimientos_inventario', ['tipo' => 'entrada', 'fecha' => $yesterday.' 00:00:00']);
        $this->assertDatabaseHas('movimientos_inventario', ['tipo' => 'salida', 'fecha' => $today.' 00:00:00']);
        $this->assertDatabaseHas('movimientos_inventario', ['tipo' => 'salida', 'fecha' => $yesterday.' 00:00:00']);
    }

    public function test_tomorrow_is_rejected_for_inventory_product_and_client_dates(): void
    {
        $admin = User::factory()->admin()->create();
        $tomorrow = now(config('app.timezone'))->addDay()->toDateString();
        $producto = Producto::factory()->create(['cantidad' => 3]);

        $this->actingAs($admin)
            ->from(route('entradas.create'))
            ->post(route('entradas.store'), [
                'nombre' => 'Entrada futura',
                'categoria' => 'General',
                'precio' => 10,
                'cantidad' => 1,
                'fecha' => $tomorrow,
            ])
            ->assertSessionHasErrors('fecha');

        $this->from(route('salidas.create'))
            ->post(route('salidas.store'), [
                'id_producto' => $producto->id,
                'cantidad' => 1,
                'fecha' => $tomorrow,
            ])
            ->assertSessionHasErrors('fecha');

        $this->from(route('productos.create'))
            ->post(route('productos.store'), $this->productPayload(['fecha_registro' => $tomorrow]))
            ->assertSessionHasErrors('fecha_registro');

        $this->from(route('admin.clientes.create'))
            ->post(route('admin.clientes.store'), $this->clientPayload(['fecha_nacimiento' => $tomorrow]))
            ->assertSessionHasErrors('fecha_nacimiento');

        $this->assertDatabaseCount('movimientos_inventario', 0);
        $this->assertDatabaseCount('productos', 1);
        $this->assertDatabaseCount('clientes', 0);
    }

    public function test_today_and_yesterday_are_valid_client_birth_dates_and_omission_is_allowed(): void
    {
        $admin = User::factory()->admin()->create();
        $today = now(config('app.timezone'))->toDateString();
        $yesterday = now(config('app.timezone'))->subDay()->toDateString();

        foreach ([$today, $yesterday, null] as $index => $date) {
            $payload = $this->clientPayload([
                'documento' => 'DOC-'.($index + 1),
                'email' => 'cliente'.$index.'@example.com',
                'fecha_nacimiento' => $date,
            ]);

            $this->actingAs($admin)
                ->post(route('admin.clientes.store'), $payload)
                ->assertRedirect(route('admin.clientes.index'));
        }

        $this->assertDatabaseHas('clientes', ['documento' => 'DOC-1', 'fecha_nacimiento' => $today.' 00:00:00']);
        $this->assertDatabaseHas('clientes', ['documento' => 'DOC-2', 'fecha_nacimiento' => $yesterday.' 00:00:00']);
        $this->assertDatabaseHas('clientes', ['documento' => 'DOC-3', 'fecha_nacimiento' => null]);
    }

    public function test_product_update_rejects_tomorrow_and_accepts_today_or_yesterday(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['stock_minimo' => 0]);
        $tomorrow = now(config('app.timezone'))->addDay()->toDateString();

        $this->actingAs($admin)
            ->from(route('productos.editar', $producto))
            ->put(route('productos.actualizar', $producto), $this->productUpdatePayload($producto, $tomorrow))
            ->assertSessionHasErrors('fecha_registro');

        foreach ([
            now(config('app.timezone'))->toDateString(),
            now(config('app.timezone'))->subDay()->toDateString(),
        ] as $date) {
            $this->put(route('productos.actualizar', $producto), $this->productUpdatePayload($producto, $date))
                ->assertRedirect(route('productos.index'));

            $this->assertSame($date, $producto->fresh()->fecha_registro->format('Y-m-d'));
        }
    }

    public function test_client_update_accepts_today_and_yesterday_but_rejects_tomorrow_and_dates_not_after_1900(): void
    {
        $admin = User::factory()->admin()->create();
        $cliente = Cliente::factory()->create();
        $tomorrow = now(config('app.timezone'))->addDay()->toDateString();

        foreach ([$tomorrow, '1900-01-01'] as $date) {
            $this->actingAs($admin)
                ->from(route('admin.clientes.edit', $cliente))
                ->put(route('admin.clientes.update', $cliente), $this->clientUpdatePayload($cliente, $date))
                ->assertSessionHasErrors('fecha_nacimiento');
        }

        foreach ([
            now(config('app.timezone'))->toDateString(),
            now(config('app.timezone'))->subDay()->toDateString(),
        ] as $date) {
            $this->put(route('admin.clientes.update', $cliente), $this->clientUpdatePayload($cliente, $date))
                ->assertRedirect(route('admin.clientes.index'));

            $this->assertSame($date, $cliente->fresh()->fecha_nacimiento->format('Y-m-d'));
        }
    }

    public function test_date_fields_render_max_today_and_cita_time_constraints(): void
    {
        $admin = User::factory()->admin()->create();
        $today = now(config('app.timezone'))->toDateString();

        $this->actingAs($admin)
            ->get(route('entradas.create'))
            ->assertOk()
            ->assertSee('max="'.$today.'"', false);
        $this->get(route('salidas.create'))->assertOk()->assertSee('max="'.$today.'"', false);
        $this->get(route('productos.create'))->assertOk()->assertSee('max="'.$today.'"', false);
        $this->get(route('admin.clientes.create'))->assertOk()->assertSee('max="'.$today.'"', false);
    }

    private function productPayload(array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Producto de prueba',
            'categoria' => 'Repuestos',
            'precio' => 25.50,
            'stock_minimo' => 0,
            'descripcion' => 'Producto válido',
            'fecha_registro' => now(config('app.timezone'))->toDateString(),
        ], $overrides);
    }

    private function productUpdatePayload(Producto $producto, string $fecha): array
    {
        return [
            'nombre' => $producto->nombre,
            'categoria' => $producto->categoria,
            'precio' => $producto->precio,
            'stock_minimo' => $producto->stock_minimo,
            'descripcion' => $producto->descripcion,
            'fecha_registro' => $fecha,
        ];
    }

    private function clientPayload(array $overrides = []): array
    {
        return array_merge([
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'documento' => 'DOC-1',
            'fecha_nacimiento' => null,
            'telefono' => '3000000000',
            'email' => 'cliente@example.com',
        ], $overrides);
    }

    private function clientUpdatePayload(Cliente $cliente, string $fecha): array
    {
        return [
            'nombre' => $cliente->nombre,
            'apellido' => $cliente->apellido,
            'documento' => $cliente->documento,
            'fecha_nacimiento' => $fecha,
            'direccion' => $cliente->direccion,
            'ciudad' => $cliente->ciudad,
            'telefono' => $cliente->telefono,
            'email' => $cliente->email,
        ];
    }
}
