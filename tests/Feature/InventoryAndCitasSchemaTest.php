<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Cita;
use App\Models\Moto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAndCitasSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_salida_uses_a_transactional_movement_and_reduces_stock(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['cantidad' => 4]);

        $this->actingAs($user)
            ->from(route('salidas.create'))
            ->post(route('salidas.store'), [
                'id_producto' => $producto->id,
                'cantidad' => 3,
                'descripcion' => 'Mantenimiento',
            ])
            ->assertRedirect(route('salidas.index'));

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'cantidad' => 1]);
        $this->assertDatabaseHas('movimientos_inventario', [
            'id_producto' => $producto->id,
            'tipo' => 'salida',
            'cantidad' => 3,
            'user_id' => $user->id,
        ]);
    }

    public function test_salida_rejects_negative_stock_without_recording_a_movement(): void
    {
        $user = User::factory()->create();
        $producto = Producto::factory()->create(['cantidad' => 2]);

        $this->actingAs($user)
            ->from(route('salidas.create'))
            ->post(route('salidas.store'), [
                'id_producto' => $producto->id,
                'cantidad' => 3,
            ])
            ->assertSessionHasErrors('cantidad');

        $this->assertDatabaseHas('productos', ['id' => $producto->id, 'cantidad' => 2]);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_cita_creation_uses_standard_ids_and_new_statuses(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        $this->actingAs($user)
            ->post(route('admin.citas.store'), [
                'cliente_id' => $cliente->id,
                'moto_id' => $moto->id,
                'mecanico_id' => null,
                'fecha' => '2026-11-10',
                'hora' => '10:00',
                'servicio' => 'Cambio de aceite',
            ])
            ->assertRedirect(route('admin.citas.index'));

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'mecanico_id' => null,
            'estado' => 'solicitada',
            'servicio' => 'Cambio de aceite',
        ]);
    }

    public function test_updated_inventory_and_cita_views_render(): void
    {
        $user = User::factory()->create();
        $cliente = Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);
        $cita = Cita::factory()->create([
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'mecanico_id' => null,
            'fecha' => '2026-11-10',
            'hora' => '14:35:00',
        ]);
        $producto = Producto::factory()->create();

        $this->actingAs($user);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $cita->fecha);
        $this->get(route('admin.citas.edit', $cita))
            ->assertOk()
            ->assertSee('value="2026-11-10"', false)
            ->assertSee('value="14:35"', false);

        foreach ([
            route('admin.citas.index'),
            route('admin.citas.create'),
            route('inventario.index'),
            route('entradas.index'),
            route('salidas.index'),
            route('salidas.create'),
            route('productos.index'),
            route('productos.editar', $producto->id),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
