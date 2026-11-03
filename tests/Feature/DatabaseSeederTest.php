<?php

namespace Tests\Feature;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_accounts_are_idempotent_and_use_the_configured_password(): void
    {
        $firstPassword = Str::password(16).'Aa1!';
        $secondPassword = Str::password(16).'Aa1!';

        $this->withEnvironmentVariables([
            'MOTOGESTOR_DEMO_PASSWORD' => $firstPassword,
        ], function (): void {
            $this->runSeeder('testing');
        });

        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseHas('clientes', [
            'documento' => 'DEMO-CLIENTE',
            'user_id' => User::where('email', 'demo.cliente@motogestor.com')->value('id'),
        ]);

        $this->withEnvironmentVariables([
            'MOTOGESTOR_DEMO_PASSWORD' => $secondPassword,
        ], function (): void {
            $this->runSeeder('testing');
        });

        $this->assertDatabaseCount('users', 5);
        $storedPassword = User::where('email', 'demo.admin@motogestor.com')->value('password');
        $this->assertTrue(Hash::check($secondPassword, $storedPassword));
        $this->assertFalse(Hash::check($firstPassword, $storedPassword));
    }

    public function test_production_seeds_only_an_admin_when_both_credentials_are_set(): void
    {
        $output = $this->withEnvironmentVariables([
            'ADMIN_EMAIL' => '',
            'ADMIN_PASSWORD' => '',
        ], function (): string {
            return $this->runSeeder('production');
        });

        $this->assertDatabaseCount('users', 0);
        $this->assertStringContainsString('No se creó el administrador', $output);

        $adminPassword = Str::password(16).'Aa1!';
        $this->withEnvironmentVariables([
            'ADMIN_EMAIL' => 'admin@example.test',
            'ADMIN_PASSWORD' => $adminPassword,
        ], function (): void {
            $this->runSeeder('production');
        });

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'role' => 'admin',
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'demo.admin@motogestor.com']);
        $this->assertTrue(Hash::check(
            $adminPassword,
            User::where('email', 'admin@example.test')->value('password')
        ));
    }

    public function test_missing_demo_password_is_generated_and_printed_once(): void
    {
        $output = $this->withEnvironmentVariables([
            'MOTOGESTOR_DEMO_PASSWORD' => '',
        ], function (): string {
            return $this->runSeeder('testing');
        });

        $this->assertSame(1, substr_count($output, 'Contraseña temporal de cuentas demo'));
        $this->assertMatchesRegularExpression(
            '/Contraseña temporal de cuentas demo \(guárdala ahora\): (\S{16})/',
            $output,
            'A 16-character generated password should be shown.'
        );
    }

    private function runSeeder(string $environment): string
    {
        $previousEnvironment = $this->app->environment();
        $this->app->detectEnvironment(fn (): string => $environment);

        try {
            Artisan::call('db:seed', [
                '--class' => \Database\Seeders\DatabaseSeeder::class,
                '--force' => true,
            ]);

            return Artisan::output();
        } finally {
            $this->app->detectEnvironment(fn (): string => $previousEnvironment);
        }
    }

    private function withEnvironmentVariables(array $values, Closure $callback): mixed
    {
        $previous = [];

        foreach ($values as $key => $value) {
            $previous[$key] = [
                'env' => array_key_exists($key, $_ENV) ? $_ENV[$key] : null,
                'has_env' => array_key_exists($key, $_ENV),
                'server' => array_key_exists($key, $_SERVER) ? $_SERVER[$key] : null,
                'has_server' => array_key_exists($key, $_SERVER),
                'process' => getenv($key),
            ];
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        try {
            return $callback();
        } finally {
            foreach ($previous as $key => $valuesToRestore) {
                if ($valuesToRestore['has_env']) {
                    $_ENV[$key] = $valuesToRestore['env'];
                } else {
                    unset($_ENV[$key]);
                }

                if ($valuesToRestore['has_server']) {
                    $_SERVER[$key] = $valuesToRestore['server'];
                } else {
                    unset($_SERVER[$key]);
                }

                if ($valuesToRestore['process'] === false) {
                    putenv($key);
                } else {
                    putenv("{$key}={$valuesToRestore['process']}");
                }
            }
        }
    }
}
