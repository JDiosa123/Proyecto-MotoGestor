# Plan de Implementación Paso a Paso - MotoGestor

**Tiempo estimado:** 2-3 semanas (dependiendo del equipo)  
**Prioridad:** ALTA - Recomendado antes de pasar a producción

---

## 🎯 SEMANA 1: FUNDAMENTOS (CRÍTICO)

### LUNES: Seguridad Básica

**1. Configurar .env seguro (15 min)**
```bash
# Copiar archivo para producción
cp .env .env.production
```

**En .env.production:**
```env
APP_DEBUG=false
APP_ENV=production
DB_PASSWORD=contraseña_segura_123
SESSION_ENCRYPT=true
BCRYPT_ROUNDS=12
```

**2. Generar nueva APP_KEY para producción (5 min)**
```bash
php artisan key:generate
# Copiar el nuevo key a .env.production
```

**3. Validar .gitignore (5 min)**
```bash
cat .gitignore | grep ".env"
# Debe estar presente
```

**Checklist:**
- [ ] .env.production creado
- [ ] Nueva APP_KEY generada
- [ ] .gitignore contiene .env
- [ ] Base de datos tiene contraseña

---

### MARTES: Migraciones

**1. Crear todas las migraciones (30 min)**
```bash
php artisan make:migration create_clientes_table
php artisan make:migration create_motos_table
php artisan make:migration create_productos_table
php artisan make:migration create_citas_table
php artisan make:migration create_movimientos_inventario_table
php artisan make:migration create_entradas_table
php artisan make:migration add_foreign_keys
```

**2. Escribir contenido de migraciones (según la guía GUIA_IMPLEMENTACION.md)**
- Copiar ejemplo de cliente
- Adaptar para otros modelos
- Agregar relaciones foráneas

**3. Ejecutar migraciones (5 min)**
```bash
php artisan migrate
php artisan migrate:status  # Verificar
```

**Checklist:**
- [ ] Todas las migraciones creadas
- [ ] Estructura de tabla correcta
- [ ] Índices agregados
- [ ] Migraciones ejecutadas sin errores

---

### MIÉRCOLES: Modelos

**1. Mejorar modelo Cliente (20 min)**
- Agregar casts
- Agregar relaciones
- Agregar scopes

**2. Crear/mejorar otros modelos (30 min)**
- User (validar fillable)
- Moto
- Producto
- Cita
- Entrada
- Salida

**3. Crear Factories (15 min)**
```bash
php artisan make:factory ClienteFactory
php artisan make:factory ProductoFactory
```

**Checklist:**
- [ ] Todos los modelos con fillable
- [ ] Relaciones bidireccionales
- [ ] Casts configurados
- [ ] Factories creadas

---

### JUEVES: FormRequests y Validación

**1. Crear todos los FormRequests (45 min)**
```bash
php artisan make:request StoreUserRequest
php artisan make:request UpdateUserRequest
php artisan make:request StoreClienteRequest
php artisan make:request UpdateClienteRequest
php artisan make:request StoreMotoRequest
php artisan make:request StoreCitaRequest
php artisan make:request StoreProductoRequest
```

**2. Implementar validaciones (según GUIA_IMPLEMENTACION.md)**

**3. Actualizar controladores (30 min)**
- Reemplazar $request->validate() con FormRequest
- Usar $request->validated()

**Checklist:**
- [ ] Todos los FormRequests creados
- [ ] Validaciones completas
- [ ] Controladores actualizados
- [ ] Mensajes de error personalizados

---

### VIERNES: Reorganización de Carpetas

**1. Crear estructura de carpetas (10 min)**
```bash
mkdir -p app/Http/Controllers/Almacen
mkdir -p app/Http/Controllers/Clientes
mkdir -p app/Http/Controllers/Inventario
mkdir -p app/Http/Controllers/Motos
mkdir -p app/Http/Controllers/Profile
```

**2. Mover controladores (10 min)**
```bash
# En Windows PowerShell
Move-Item app\Http\Controllers\ProductoController.php app\Http\Controllers\Almacen\
Move-Item app\Http\Controllers\SalidasController.php app\Http\Controllers\Almacen\
Move-Item app\Http\Controllers\EntradasController.php app\Http\Controllers\Almacen\
Move-Item app\Http\Controllers\ClienteController.php app\Http\Controllers\Clientes\
Move-Item app\Http\Controllers\CitaController.php app\Http\Controllers\Clientes\
Move-Item app\Http\Controllers\MotoController.php app\Http\Controllers\Motos\
Move-Item app\Http\Controllers\InventarioController.php app\Http\Controllers\Inventario\
Move-Item app\Http\Controllers\ProfileController.php app\Http\Controllers\Profile\
```

**3. Actualizar namespaces en controladores (15 min)**
```php
// Cambiar de:
namespace App\Http\Controllers;

// A:
namespace App\Http\Controllers\Almacen;
namespace App\Http\Controllers\Clientes;
// etc
```

**4. Actualizar importaciones en rutas (20 min)**
```php
// routes/web.php
use App\Http\Controllers\Almacen\ProductoController;
use App\Http\Controllers\Clientes\ClienteController;
// ... etc
```

**5. Testear que todo funcione (15 min)**
```bash
php artisan tinker
# Probar cargar controladores
# Verificar rutas: php artisan route:list
```

**Checklist:**
- [ ] Carpetas creadas
- [ ] Controladores movidos
- [ ] Namespaces actualizados
- [ ] Rutas actualizadas
- [ ] Sin errores al ejecutar

---

## 🎯 SEMANA 2: SEGURIDAD AVANZADA

### LUNES: Seeders

**1. Crear Seeders (30 min)**
```bash
php artisan make:seeder UserSeeder
php artisan make:seeder ClienteSeeder
php artisan make:seeder ProductoSeeder
```

**2. Escribir contenido (según GUIA_IMPLEMENTACION.md)**

**3. Actualizar DatabaseSeeder (10 min)**
```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        ClienteSeeder::class,
        ProductoSeeder::class,
    ]);
}
```

**4. Ejecutar seeders (5 min)**
```bash
php artisan migrate:refresh --seed
```

**Checklist:**
- [ ] Seeders creados
- [ ] DatabaseSeeder actualizado
- [ ] Datos de prueba cargados
- [ ] Sin errores

---

### MARTES: Autenticación y Roles

**1. Instalar Spatie Permission (10 min)**
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

**2. Configurar roles en DatabaseSeeder (20 min)**
```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    // Crear roles
    $admin = Role::create(['name' => 'admin']);
    $mecanico = Role::create(['name' => 'mecanico']);
    $almacenista = Role::create(['name' => 'almacenista']);
    
    // Crear permisos
    // ...
}
```

**3. Crear Policies (20 min)**
```bash
php artisan make:policy ClientePolicy --model=Cliente
php artisan make:policy ProductoPolicy --model=Producto
```

**Checklist:**
- [ ] Spatie Permission instalado
- [ ] Migraciones ejecutadas
- [ ] Roles creados
- [ ] Policies creadas

---

### MIÉRCOLES: Logging y Auditoría

**1. Instalar Spatie Activity Log (10 min)**
```bash
composer require spatie/laravel-activitylog
php artisan vendor:publish --provider="Spatie\ActivityLog\ActivityLogServiceProvider" --tag=migrations
php artisan migrate
```

**2. Agregar a modelos críticos (15 min)**
```php
class User extends Model
{
    use \Spatie\ActivityLog\Traits\LogsActivity;
    protected static $logAttributes = ['name', 'email', 'role'];
}
```

**3. Crear Middleware de acceso (15 min)**
```bash
php artisan make:middleware LogAccess
```

**4. Registrar en Kernel (5 min)**

**Checklist:**
- [ ] Activity Log instalado
- [ ] Modelos con logging
- [ ] Middleware creado
- [ ] Registrado en Kernel

---

### JUEVES: Rate Limiting y CSRF

**1. Configurar Rate Limiting en rutas (15 min)**
```php
Route::middleware('throttle:3,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
```

**2. Validar CSRF en Blade (10 min)**
```blade
@foreach ($forms as $form)
    @csrf
@endforeach
```

**3. Configurar AJAX con CSRF (10 min)**
```javascript
axios.defaults.headers.common['X-CSRF-TOKEN'] = 
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

**Checklist:**
- [ ] Rate limiting en login
- [ ] @csrf en todos los forms
- [ ] AJAX con token CSRF
- [ ] Validar funcionamiento

---

### VIERNES: Testing Básico

**1. Crear tests de seguridad (30 min)**
```bash
php artisan make:test SecurityTest --feature
```

**2. Escribir tests (30 min)**
```php
public function test_unauthenticated_cannot_access_admin(): void
{
    $response = $this->get('/admin/users');
    $response->assertRedirect('/login');
}
```

**3. Ejecutar tests (5 min)**
```bash
php artisan test
```

**Checklist:**
- [ ] Tests creados
- [ ] Todos pasan
- [ ] Cobertura básica

---

## 🎯 SEMANA 3: OPTIMIZACIÓN Y FINALIZACIÓN

### LUNES: Performance

**1. Configurar Caching (20 min)**
```php
// En AppServiceProvider
Cache::remember('productos.all', 3600, function () {
    return Producto::all();
});
```

**2. Agregar índices de BD (15 min)**
- Crear migración adicional para índices
- Verificar con EXPLAIN

**3. Implementar Pagination (15 min)**
```php
$productos = Producto::paginate(15);
```

**Checklist:**
- [ ] Cache configurado
- [ ] Índices agregados
- [ ] Pagination implementada

---

### MARTES: API Sanctum (Opcional pero recomendado)

**1. Instalar Sanctum (10 min)**
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

**2. Crear rutas API (20 min)**
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
```

**Checklist:**
- [ ] Sanctum instalado
- [ ] Rutas API creadas
- [ ] Tokens funcionando

---

### MIÉRCOLES: Documentación

**1. Crear README actualizado (20 min)**
```markdown
# MotoGestor

## Instalación
## Configuración
## Estructura
## API
## Testing
```

**2. Documentar API endpoints (20 min)**

**Checklist:**
- [ ] README completo
- [ ] API documentada
- [ ] Ejemplos incluidos

---

### JUEVES: Deployment

**1. Checklist pre-producción (30 min)**
```bash
php artisan test
./vendor/bin/pint
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**2. Crear deploy script (15 min)**
```bash
#!/bin/bash
# deploy.sh
git pull origin main
composer install --no-dev
npm ci
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

**Checklist:**
- [ ] Tests pasan
- [ ] Sin errores de lint
- [ ] Cache generado
- [ ] Deploy script listo

---

### VIERNES: Monitoreo

**1. Configurar logs (10 min)**
```env
LOG_CHANNEL=stack
LOG_LEVEL=warning
```

**2. Instalar Backup (15 min)**
```bash
composer require spatie/laravel-backup
php artisan backup:run
```

**3. Crear monitor básico (15 min)**

**Checklist:**
- [ ] Logs configurados
- [ ] Backup automático
- [ ] Monitoreo básico

---

## 📊 RECURSOS Y ARCHIVOS

**Archivos de referencia en el proyecto:**
1. ✅ [ANALISIS_PROYECTO.md](ANALISIS_PROYECTO.md) - Análisis detallado
2. ✅ [GUIA_IMPLEMENTACION.md](GUIA_IMPLEMENTACION.md) - Ejemplos de código
3. ✅ [CHECKLIST_SEGURIDAD.md](CHECKLIST_SEGURIDAD.md) - Checklist de seguridad

---

## ✅ VERIFICACIÓN FINAL

Cuando completes TODO el plan, tu proyecto debe pasar esto:

```bash
# Debe retornar sin errores
php artisan check

# Debe pasar todos los tests
php artisan test --coverage

# Debe estar formateado correctamente
./vendor/bin/pint --test

# Debe haber migraciones completadas
php artisan migrate:status

# Debe haber seeders
php artisan list seed

# Variables de seguridad configuradas
grep -E "APP_DEBUG|APP_ENV|DB_PASSWORD" .env.production

# Debe servir sin errores
php artisan serve
```

---

## 📞 SOPORTE

Si tienes dudas durante la implementación:

1. Revisar los archivos de referencia
2. Usar `php artisan tinker` para probar
3. Verificar logs: `storage/logs/laravel.log`
4. Ejecutar: `php artisan doctor`

---

**Estado:** 📋 Plan Completo  
**Última actualización:** 1 de mayo de 2026  - ## 🎯 SEMANA 2: SEGURIDAD AVANZADA
**Estimado:** 3 semanas a tiempo completo
