<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $adminEmail = env('ADMIN_EMAIL');
            $adminPassword = env('ADMIN_PASSWORD');

            if (! is_string($adminEmail) || trim($adminEmail) === ''
                || ! is_string($adminPassword) || trim($adminPassword) === '') {
                $this->command?->warn('No se creó el administrador: define ADMIN_EMAIL y ADMIN_PASSWORD.');

                return;
            }

            User::updateOrCreate(
                ['email' => trim($adminEmail)],
                [
                    'name' => 'Administrador',
                    'password' => $adminPassword,
                    'role' => User::ROLE_ADMIN,
                    'status' => 'activo',
                ]
            );

            return;
        }

        $password = env('MOTOGESTOR_DEMO_PASSWORD');
        if (! is_string($password) || trim($password) === '') {
            $password = Str::password(16);
            $this->command?->info("Contraseña temporal de cuentas demo (guárdala ahora): {$password}");
        }

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
