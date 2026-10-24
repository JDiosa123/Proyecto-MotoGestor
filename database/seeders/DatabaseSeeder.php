<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = env('MOTOGESTOR_DEMO_PASSWORD', 'MotoGestorDev2026!');
        $accounts = [
            User::ROLE_ADMIN => ['Administrador Demo', 'demo.admin@motogestor.com'],
            User::ROLE_ASSISTANT => ['Asistente Demo', 'demo.asistente@motogestor.com'],
            User::ROLE_STOREKEEPER => ['Almacenista Demo', 'demo.almacenista@motogestor.com'],
            User::ROLE_MECHANIC => ['Mecánico Demo', 'demo.mecanico@motogestor.com'],
            User::ROLE_CLIENT => ['Cliente Demo', 'demo.cliente@motogestor.com'],
        ];

        foreach ($accounts as $role => [$name, $email]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => $password,
                    'role' => $role,
                    'status' => 'activo',
                ]
            );
        }

        $clienteUser = User::where('email', 'demo.cliente@motogestor.com')->firstOrFail();

        Cliente::updateOrCreate(
            ['documento' => 'DEMO-CLIENTE'],
            [
                'user_id' => $clienteUser->id,
                'nombre' => 'Cliente',
                'apellido' => 'Demo',
                'telefono' => '0000000000',
                'email' => 'cliente.demo@motogestor.com',
            ]
        );
    }
}
