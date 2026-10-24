# Requerimientos del Sistema - MotoGestor

## 1. Objetivo

Este documento define los requerimientos funcionales y no funcionales del sistema MotoGestor, una aplicación web para la gestión de clientes, motos, citas y control de inventario en un taller o concesionario.

## 2. Alcance

MotoGestor permitirá administrar la información de clientes, motos, productos e inventario, registrar entradas y salidas de stock, gestionar citas de servicio y proveer una interfaz segura para usuarios autenticados.

## 3. Actores

- **Administrador**: Usuario con permiso para gestionar clientes, motos, productos, inventario, citas y usuarios.
- **Cliente**: Persona o entidad registrada en el sistema como titular de una moto o servicio, aunque no interactúa directamente con la aplicación web interna.

> Nota: para este taller, el sistema contempla un único rol de acceso interno: el administrador. Los mecánicos no manejan la aplicación directamente; todas las operaciones relacionadas con citas, inventario, clientes y motos son realizadas por el administrador.

## 4. Requerimientos Funcionales

### RF-1. Autenticación y autorización
- RF-1.1. El sistema debe permitir el inicio de sesión de usuarios mediante correo electrónico y contraseña.
- RF-1.2. El sistema debe permitir el cierre de sesión de usuarios.
- RF-1.3. El sistema debe restringir el acceso a las funcionalidades de administración a usuarios autenticados.
- RF-1.4. El sistema debe soportar un único rol interno de administración para el taller: Administrador.
- RF-1.5. Las operaciones de citas, inventario, clientes y motos serán ejecutadas por el administrador desde la aplicación, sin requerir perfiles separados de mecánico o empleado.

### RF-2. Gestión de clientes
- RF-2.1. El sistema debe permitir crear nuevos clientes con datos personales básicos.
- RF-2.2. El sistema debe permitir editar la información de un cliente existente.
- RF-2.3. El sistema debe permitir eliminar clientes si no existen restricciones de integridad.
- RF-2.4. El sistema debe listar los clientes y permitir buscar/filtrar por nombre, documento o teléfono.
- RF-2.5. El sistema debe registrar las relaciones entre cliente y motos, así como cliente y citas.

### RF-3. Gestión de motos
- RF-3.1. El sistema debe permitir registrar motos vinculadas a un cliente.
- RF-3.2. El sistema debe permitir editar los datos de una moto existente.
- RF-3.3. El sistema debe permitir eliminar motos si no existen dependencias activas.
- RF-3.4. El sistema debe listar las motos y permitir buscar/filtrar por placa, modelo y cliente asociado.

### RF-4. Gestión de citas
- RF-4.1. El sistema debe permitir crear citas de servicio vinculadas a un cliente y/o moto.
- RF-4.2. El sistema debe permitir editar la fecha, hora, estado y descripción de una cita.
- RF-4.3. El sistema debe permitir cambiar el estado de una cita (por ejemplo: pendiente, en curso, completada, cancelada).
- RF-4.4. El sistema debe listar las citas y permitir aplicar filtros por fecha, cliente, estado y asignación.

### RF-5. Gestión de inventario y productos
- RF-5.1. El sistema debe permitir crear productos y registrar sus características básicas.
- RF-5.2. El sistema debe permitir editar los datos de un producto.
- RF-5.3. El sistema debe permitir eliminar productos siempre que no afecte al historial del inventario.
- RF-5.4. El sistema debe listar productos disponibles y permitir búsquedas por nombre o referencia.

### RF-6. Registro de entradas y salidas de inventario
- RF-6.1. El sistema debe permitir registrar entradas de stock para productos.
- RF-6.2. El sistema debe permitir registrar salidas de stock para productos.
- RF-6.3. El sistema debe mantener el historial de movimientos de inventario.
- RF-6.4. El sistema debe actualizar el stock disponible de cada producto automáticamente tras cada movimiento.
- RF-6.5. El sistema debe validar que no se registren salidas mayores al stock disponible.

### RF-7. Reportes y consultas
- RF-7.1. El sistema debe proporcionar una vista de inventario con stock actual de productos.
- RF-7.2. El sistema debe permitir ver el historial de citas recientes.
- RF-7.3. El sistema debe permitir ver el historial de movimientos de inventario.

### RF-8. Configuración de perfil y datos de la cuenta
- RF-8.1. El sistema debe permitir a los usuarios autenticados ver su perfil.
- RF-8.2. El sistema debe permitir a los usuarios cambiar su contraseña.
- RF-8.3. El sistema debe permitir actualizar datos personales básicos del usuario.

### RF-9. Validación y manejo de errores
- RF-9.1. El sistema debe validar los datos de entrada en formularios antes de persistirlos.
- RF-9.2. El sistema debe mostrar mensajes de error claros cuando la validación falle.
- RF-9.3. El sistema debe manejar errores de base de datos y mostrar una página de error genérica al usuario si ocurre una excepción inesperada.

## 5. Requerimientos No Funcionales

### RNF-1. Seguridad
- RNF-1.1. El sistema debe cifrar las contraseñas de usuario en la base de datos.
- RNF-1.2. El sistema debe usar HTTPS en producción.
- RNF-1.3. El sistema debe proteger contra ataques comunes como inyección SQL y CSRF.
- RNF-1.4. El sistema debe validar y sanitizar las entradas del usuario.

### RNF-2. Rendimiento
- RNF-2.1. El sistema debe responder a las solicitudes de página en menos de 2 segundos en condiciones normales.
- RNF-2.2. Las consultas de listado principales deben estar optimizadas y paginadas cuando haya más de 50 registros.

### RNF-3. Disponibilidad
- RNF-3.1. El sistema debe estar disponible al menos el 99% del tiempo en el entorno de producción.
- RNF-3.2. El sistema debe permitir al menos 10 usuarios concurrentes sin degradar su funcionalidad.

### RNF-4. Usabilidad
- RNF-4.1. La interfaz debe ser clara y fácil de usar para usuarios con conocimientos básicos.
- RNF-4.2. La navegación debe permitir acceder a las funciones principales en 2 clics o menos.
- RNF-4.3. El sistema debe mostrar mensajes de éxito y error comprensibles.

### RNF-5. Mantenibilidad
- RNF-5.1. El código debe seguir la convención MVC de Laravel.
- RNF-5.2. El sistema debe tener la lógica de validación separada en FormRequest o equivalentes.
- RNF-5.3. El sistema debe documentar los endpoints principales y modelos en el código.

### RNF-6. Escalabilidad
- RNF-6.1. El diseño debe permitir agregar nuevos módulos (por ejemplo, facturación o reportes avanzados) sin reescribir la arquitectura base.
- RNF-6.2. El esquema de base de datos debe ser extensible para nuevas entidades relacionadas.

### RNF-7. Compatibilidad
- RNF-7.1. El sistema debe ser compatible con PHP 8.2 o superior.
- RNF-7.2. El sistema debe funcionar correctamente en navegadores modernos: Chrome, Edge y Firefox.
- RNF-7.3. El sistema debe comportarse correctamente en pantallas de escritorio y tablet.

### RNF-8. Documentación
- RNF-8.1. El proyecto debe incluir documentación básica de instalación y uso.
- RNF-8.2. El proyecto debe incluir instrucciones para desplegar en un entorno de producción.

## 6. Diagrama de Casos de Uso

El sistema debe incluir un módulo de notificaciones para:
- Alertar sobre citas próximas y cambios en el estado de citas.
- Informar sobre movimientos críticos de inventario (por ejemplo, stock bajo o entradas/salidas registradas).

### Actores principales
- Administrador
- Cliente

### Casos de uso principales
- Iniciar sesión
- Cerrar sesión
- Gestionar clientes
- Gestionar motos
- Gestionar citas
- Gestionar productos e inventario
- Registrar entradas y salidas
- Consultar reportes
- Ver perfil y cambiar contraseña
- Enviar notificaciones

```mermaid
%%{init: {"securityLevel": "loose"}}%%
usecaseDiagram
    actor Administrador
    actor Cliente

    Administrador --> (Iniciar sesión)
    Administrador --> (Cerrar sesión)
    Administrador --> (Gestionar clientes)
    Administrador --> (Gestionar motos)
    Administrador --> (Gestionar citas)
    Administrador --> (Gestionar productos e inventario)
    Administrador --> (Registrar entradas y salidas)
    Administrador --> (Consultar reportes)
    Administrador --> (Ver perfil y cambiar contraseña)
    Administrador --> (Enviar notificaciones)

    Cliente --> (Ver perfil y cambiar contraseña)
    Cliente --> (Recibir notificaciones)

    (Enviar notificaciones) ..> (Gestionar citas) : "incluye"
    (Enviar notificaciones) ..> (Registrar entradas y salidas) : "incluye"
    (Recibir notificaciones) ..> (Enviar notificaciones)
```

> Nota: el sistema debe contemplar notificaciones internas o por correo para avisos de citas y alertas de inventario.

## 7. Supuestos

- Se asume que la base de datos principal es MySQL o MariaDB.
- Se asume que el rol Administrador es el único perfil interno del sistema para operar el taller.
- Se asume que no hay funcionalidad de pago o facturación en esta fase inicial.

## 7. Glosario

- **Cliente**: Persona o entidad registrada en el sistema que utiliza los servicios del taller.
- **Moto**: Vehículo asociado a un cliente.
- **Cita**: Registro de un servicio técnico o revisión programada para una moto.
- **Producto**: Insumo o repuesto gestionado en inventario.
- **Entrada**: Movimiento que aumenta el stock de un producto.
- **Salida**: Movimiento que reduce el stock de un producto.
