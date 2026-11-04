<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserRoleRequirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_is_not_nullable_has_no_default_and_keeps_its_index(): void
    {
        $roleColumn = collect(Schema::getColumns('users'))
            ->firstWhere('name', 'role');

        $this->assertNotNull($roleColumn);
        $this->assertFalse($roleColumn['nullable']);
        $this->assertNull($roleColumn['default']);
        $this->assertSame('varchar', strtolower($roleColumn['type_name']));

        $roleIndex = collect(Schema::getIndexes('users'))
            ->first(fn (array $index): bool => in_array('role', $index['columns'], true));
        $this->assertNotNull($roleIndex);
    }

    public function test_database_rejects_a_user_insert_without_a_role(): void
    {
        $this->expectException(QueryException::class);

        DB::table('users')->insert([
            'name' => 'Usuario Sin Rol',
            'email' => 'sin.rol.db@motogestor.com',
            'password' => Hash::make(Str::password(16)),
            'status' => 'activo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_admin_cannot_create_a_user_without_a_role(): void
    {
        $this->assertRoleRejected([]);
    }

    public function test_admin_cannot_create_a_user_with_an_invalid_role(): void
    {
        $this->assertRoleRejected(['role' => 'superadmin']);
    }

    private function assertRoleRejected(array $roleData): void
    {
        $admin = User::factory()->admin()->create();
        $email = 'role-required-'.Str::random(8).'@motogestor.com';
        $password = Str::password(16).'Aa1!';

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Usuario De Prueba',
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $password,
                'status' => 'activo',
                ...$roleData,
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }
}
