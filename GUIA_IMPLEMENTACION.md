# Ejemplos de Implementación - MotoGestor

## 1. REORGANIZACIÓN DE CARPETAS

Ejecuta estos comandos para reorganizar los controladores:

```bash
# Crear las nuevas carpetas
mkdir -p app/Http/Controllers/Almacen
mkdir -p app/Http/Controllers/Clientes
mkdir -p app/Http/Controllers/Inventario
mkdir -p app/Http/Controllers/Motos
mkdir -p app/Http/Controllers/Profile

# Mover archivos (usar comando move en Windows)
move app\Http\Controllers\ProductoController.php app\Http\Controllers\Almacen\
move app\Http\Controllers\SalidasController.php app\Http\Controllers\Almacen\
move app\Http\Controllers\EntradasController.php app\Http\Controllers\Almacen\

move app\Http\Controllers\ClienteController.php app\Http\Controllers\Clientes\
move app\Http\Controllers\CitaController.php app\Http\Controllers\Clientes\

move app\Http\Controllers\InventarioController.php app\Http\Controllers\Inventario\

move app\Http\Controllers\MotoController.php app\Http\Controllers\Motos\

move app\Http\Controllers\ProfileController.php app\Http\Controllers\Profile\
```

## 2. FORM REQUESTS - EJEMPLO COMPLETO

```php
// app/Http/Requests/StoreUserRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[A-Za-z0-9._%+-]+@motogestor\.com$/'
            ],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'],
            'role' => ['required', 'in:admin,mecanico,almacenista'],
            'status' => ['required', 'in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo debe contener letras y espacios',
            'email.regex' => 'El email debe ser del dominio @motogestor.com',
            'email.unique' => 'Este email ya está registrado',
            'password.regex' => 'La contraseña debe tener mayúscula, minúscula, número y carácter especial',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        
        return $validated;
    }
}
```

**Uso en Controlador:**
```php
// app/Http/Controllers/UserController.php
public function store(StoreUserRequest $request)
{
    $user = User::create($request->validated());
    
    return redirect()->route('admin.users.index')
                     ->with('success', 'Usuario creado correctamente');
}
```

---

## 3. MIGRACIONES NECESARIAS

```bash
# Generar migraciones
php artisan make:migration create_clientes_table
php artisan make:migration create_motos_table
php artisan make:migration create_productos_table
php artisan make:migration create_citas_table
php artisan make:migration create_movimientos_inventario_table
php artisan make:migration create_entradas_table
```

**Ejemplo - Migración de Clientes:**
```php
// database/migrations/XXXX_XX_XX_XXXXXX_create_clientes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('documento')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('telefono');
            $table->string('email')->unique();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('documento');
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
```

---

## 4. MODELOS MEJORADOS

```php
// app/Models/Cliente.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'fecha_nacimiento',
        'direccion',
        'ciudad',
        'telefono',
        'email',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // RELACIONES
    public function motos()
    {
        return $this->hasMany(Moto::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    // SCOPES - Búsquedas comunes
    public function scopeActivos($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopePorDocumento($query, $documento)
    {
        return $query->where('documento', $documento);
    }

    public function scopePorEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciudad', $ciudad);
    }

    // ACCESSORS - Propiedades derivadas
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento?->diffInYears(now());
    }

    // MÉTODOS ÚTILES
    public function puedeBorrar(): bool
    {
        return !$this->motos()->exists();
    }
}
```

---

## 5. SEEDERS

```bash
# Crear seeders
php artisan make:seeder UserSeeder
php artisan make:seeder ClienteSeeder
php artisan make:seeder ProductoSeeder
```

```php
// database/seeders/UserSeeder.php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@motogestor.com',
            'password' => Hash::make('Admin123!@#'),
            'role' => 'admin',
            'status' => 'activo',
        ]);

        // Mecánicos
        User::factory()
            ->count(3)
            ->create([
                'role' => 'mecanico',
                'status' => 'activo'
            ]);

        // Almacenistas
        User::factory()
            ->count(2)
            ->create([
                'role' => 'almacenista',
                'status' => 'activo'
            ]);
    }
}
```

```php
// database/seeders/DatabaseSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ClienteSeeder::class,
            ProductoSeeder::class,
            // Agregar más seeders según sea necesario
        ]);
    }
}
```

**Ejecutar:**
```bash
php artisan migrate:refresh --seed
```

---

## 6. CONFIGURACIÓN DE SEGURIDAD EN .env

**Crear archivo `.env.production` (para producción):**
```env
APP_NAME=MotoGestor
APP_ENV=production
APP_KEY=base64:AQUI_VA_TU_KEY_GENERADO
APP_DEBUG=false
APP_URL=https://motogestor.com

APP_TIMEZONE=America/Bogota
APP_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=db.example.com
DB_PORT=3306
DB_DATABASE=motogestor_prod
DB_USERNAME=moto_user
DB_PASSWORD=contraseña_segura_aqui

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIES=true

CACHE_STORE=redis
CACHE_PREFIX=moto_prod

QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@motogestor.com
MAIL_FROM_NAME="MotoGestor"
```

---

## 7. MIDDLEWARE PERSONALIZADO

```bash
php artisan make:middleware CheckRole
php artisan make:middleware LogActivity
```

```php
// app/Http/Middleware/CheckRole.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403, 'No tienes permiso para acceder a este recurso');
        }

        return $next($request);
    }
}
```

**Registrar en `app/Http/Kernel.php`:**
```php
protected $routeMiddleware = [
    // ... otros middleware
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

**Usar en rutas:**
```php
Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});
```

---

## 8. INSTALACIÓN DE PAQUETES RECOMENDADOS

```bash
# Permisos y roles
composer require spatie/laravel-permission

# API Tokens
composer require laravel/sanctum

# Auditoría
composer require spatie/laravel-activitylog

# Validador de datos
composer require spatie/laravel-query-builder

# Slugs automáticos
composer require spatie/laravel-sluggable
```

---

## 9. TESTING BÁSICO

```bash
php artisan make:test UserTest --feature
```

```php
// tests/Feature/UserTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function test_can_create_user(): void
    {
        $response = $this->post('/admin/users', [
            'name' => 'John Doe',
            'email' => 'john@motogestor.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'role' => 'mecanico',
            'status' => 'activo',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@motogestor.com',
        ]);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'test@motogestor.com']);

        $response = $this->post('/admin/users', [
            'name' => 'Jane Doe',
            'email' => 'test@motogestor.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'role' => 'mecanico',
            'status' => 'activo',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
```

**Ejecutar tests:**
```bash
php artisan test
php artisan test --coverage  # Con cobertura
```

---

## 10. COMANDOS ÚTILES PARA PRODUCCIÓN

```bash
# Preparar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar antes de actualizar
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar
php artisan optimize

# Ver estado de migraciones
php artisan migrate:status
```

---

**Próximos pasos:**
1. Ejecutar reorganización de carpetas
2. Crear todas las migraciones
3. Crear FormRequests
4. Mejorar Modelos
5. Crear Seeders
6. Configurar variables .env seguras
