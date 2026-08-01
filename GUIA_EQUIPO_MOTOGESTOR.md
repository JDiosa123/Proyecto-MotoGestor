# Guía de equipo - MotoGestor

## 1. Resumen del sistema

MotoGestor es una aplicación web para gestionar un taller o concesionario de motos. El sistema centraliza las operaciones de:

- clientes
- motos
- citas
- productos e inventario
- entradas y salidas de stock
- usuarios del panel administrativo

## 2. Modelo de roles

Para este proyecto, el sistema contempla un único rol interno de acceso: `admin`.

Esto significa que:

- el administrador es quien opera toda la aplicación
- no existen perfiles separados para mecánicos o almacenistas dentro del panel
- las funciones de citas, clientes, motos, inventario y usuarios las realiza el administrador

## 3. Reglas de acceso

### Usuario administrativo

El acceso al sistema se realiza con un usuario autenticado en el panel administrativo.

Credenciales de acceso de prueba actualmente usadas en el proyecto:

- correo: `admin@motogestor.com`
- contraseña: `12345`

### Estado del usuario

El usuario debe estar en estado `activo` para poder iniciar sesión correctamente.

## 4. Módulos principales

### Gestión de clientes
- registrar clientes
- editar clientes
- listar clientes
- asociar motos y citas

### Gestión de motos
- registrar motos asociadas a un cliente
- editar o eliminar motos
- listar motos con filtros básicos

### Gestión de citas
- crear citas de servicio
- editar fecha, hora, estado y descripción
- ver historial de citas

### Inventario y productos
- registrar productos
- actualizar datos del producto
- controlar entradas y salidas
- mantener historial de movimientos de inventario

### Administración de usuarios
- crear usuarios administradores
- activar o desactivar cuentas
- gestionar credenciales del panel

## 5. Tecnologías utilizadas

- PHP
- Laravel
- Blade
- MySQL/MariaDB
- Vite
- Tailwind CSS

## 6. Cómo arrancar el proyecto

### Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- base de datos MySQL/MariaDB

### Pasos básicos

1. Clonar el repositorio.
2. Instalar dependencias de PHP:
   ```bash
   composer install
   ```
3. Instalar dependencias del frontend:
   ```bash
   npm install
   ```
4. Compilar assets:
   ```bash
   npm run build
   ```
5. Ejecutar migraciones y sembrar datos si corresponde:
   ```bash
   php artisan migrate:fresh --seed
   ```
6. Iniciar el servidor:
   ```bash
   php artisan serve
   ```

## 7. Observaciones importantes para el equipo

- La lógica del sistema está pensada para un solo rol interno: `admin`.
- El login y las vistas autenticadas deben manejarse con un layout estable y el manifest de Vite generado correctamente.
- Si se trabaja con autenticación, se recomienda verificar primero:
  - que exista el usuario activo
  - que el usuario tenga el role `admin`
  - que el email esté bien escrito
  - que la contraseña sea válida en la base de datos

## 8. Cómo hacer cambios en GitHub

Para mantener el proyecto ordenado, se recomienda usar el siguiente flujo:

### 1. Crear una rama de trabajo

```bash
git checkout -b feature/nombre-cambio
```

Ejemplos:

```bash
git checkout -b feature/gestion-citas
git checkout -b bugfix/login-admin
```

### 2. Hacer cambios en la rama

Realiza tus cambios y luego revisa el estado:

```bash
git status
git diff
```

### 3. Guardar los cambios

```bash
git add .
git commit -m "Describe el cambio realizado"
```

### 4. Subir la rama a GitHub

```bash
git push origin feature/nombre-cambio
```

### 5. Crear un Pull Request

- abre el repositorio en GitHub
- selecciona la rama que subiste
- crea un `Pull Request` hacia `develop` o `main`, según corresponda
- describe claramente:
  - qué se cambió
  - por qué se cambió
  - si afecta autenticación, inventario, citas o usuarios

### 6. Política recomendada de ramas

- `main`: rama estable y lista para producción
- `develop`: rama de integración para pruebas
- `feature/*`: nuevas funcionalidades
- `bugfix/*`: correcciones

### 7. Buenas prácticas

- no mezcles varios cambios distintos en un solo commit
- usa mensajes de commit claros y cortos
- antes de hacer `push`, verifica que solo vayas a subir los archivos necesarios
- si el cambio es solo guía de equipo, mantén ese archivo como referencia clara y aislada

## 9. Nota de colaboración

Este documento está pensado como una guía rápida para que un compañero nuevo entienda el contexto del proyecto sin revisar todo el resto de documentación técnica del repositorio.
