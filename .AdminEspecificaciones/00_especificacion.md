# Especificación: Panel de Administrador

> Documento de referencia para la implementación del panel admin completo.

---

## Objetivo

Reemplazar el dashboard de administrador sin estilo por un panel completo con sidebar lateral (basado en `context/adminPanel.html`) con tres secciones: **Usuarios**, **Técnicos** y **Solicitudes**. Todo respetando la arquitectura MVC existente.

---

## Secciones del Panel

### Usuarios
Gestión de todos los usuarios registrados.

| Acción | Descripción |
|--------|-------------|
| Listar | Tabla paginada con búsqueda por nombre/email y filtro por estado (activo/inactivo) |
| Editar | Formulario para cambiar nombre, email, teléfono y rol |
| Suspender | Invierte el flag `activo` (1→0 / 0→1) |
| Eliminar | Hard delete. Si el usuario tiene solicitudes (FK RESTRICT), se muestra error y se sugiere suspender |

### Técnicos
Flujo de validación de solicitudes de técnicos (ya implementado, se rediseña la UI).

| Acción | Descripción |
|--------|-------------|
| Listar | Tabla con todas las solicitudes técnicas y su estado |
| Actualizar estado | Aprobar / rechazar / suspender con comentario admin (transacción DB que sincroniza `users.rol`) |

Estados posibles: `pendiente`, `activo`, `suspendido`, `rechazado`

### Solicitudes
Vista de solo lectura de todas las solicitudes de servicio.

| Campo visible | Descripción |
|--------------|-------------|
| Título | Nombre de la solicitud |
| Cliente | JOIN a `users` |
| Técnico | LEFT JOIN a `users` (puede ser "Sin asignar") |
| Estado | Badge con 5 estados: pendiente, aceptada, en_progreso, completada, cancelada |
| Fecha | `fecha_creacion` |

---

## Arquitectura — Cambios por Archivo

### `src/core/Controller.php`
Agregar parámetro `$layout` (backward-compatible, default `'app'`):
```php
protected function render(string $view, array $data = [], string $layout = 'app'): void
```

### `src/views/layouts/admin.php` *(nuevo)*
Layout alternativo para todas las rutas admin. Estructura:
- `<body class="flex h-screen overflow-hidden">` — shell sin scroll
- Sidebar `hidden md:flex w-64` con logo, nav links y perfil+logout al fondo
- Main `flex-1 overflow-y-auto` con flash messages y `$content`
- Sin top nav, sin footer
- Variable `$activeSection` para resaltar el link activo en sidebar
- Avatar con inicial (no hay foto de perfil en DB)
- Tailwind config MD3 idéntico al de `app.php`

### `src/controllers/AdminController.php`
Métodos:

| Método | Ruta que lo llama |
|--------|------------------|
| `index()` | `GET /dashboard/admin` → redirect a `/dashboard/admin/usuarios` |
| `usuarios()` | `GET /dashboard/admin/usuarios` |
| `editUsuario()` | `GET /dashboard/admin/usuarios/:id/editar` |
| `updateUsuario()` | `POST /dashboard/admin/usuarios/:id/editar` |
| `suspenderUsuario()` | `POST /dashboard/admin/usuarios/:id/suspender` |
| `eliminarUsuario()` | `POST /dashboard/admin/usuarios/:id/eliminar` |
| `tecnicos()` | `GET /dashboard/admin/tecnicos` |
| `updateTechnicianStatus()` | `POST /dashboard/admin/tecnicos/:id/estado` *(existente)* |
| `solicitudes()` | `GET /dashboard/admin/solicitudes` |

### `src/models/User.php`
Métodos nuevos:

```php
static function listAllUsers(string $search = '', string $status = ''): array
// SELECT id, nombre, correo, telefono, rol, activo, fecha_registro
// WHERE (nombre LIKE :q OR correo LIKE :q) AND activo = :activo
// ORDER BY fecha_registro DESC

static function toggleActive(int $userId): void
// UPDATE users SET activo = IF(activo=1,0,1) WHERE id = :id

static function deleteUser(int $userId): void
// DELETE FROM users WHERE id = :id
// Lanza PDOException SQLSTATE 23xxx si tiene solicitudes

static function adminUpdateUser(int $userId, array $data): void
// UPDATE users SET nombre, correo, telefono, rol WHERE id = :id
```

### `src/models/Solicitud.php` *(nuevo)*

```php
class Solicitud extends Model
{
    static function listAll(): array
    // SELECT s.id, s.titulo, s.estado, s.fecha_creacion,
    //        c.nombre AS cliente_nombre, t.nombre AS tecnico_nombre
    // FROM solicitudes s
    // JOIN users c ON c.id = s.id_cliente
    // LEFT JOIN users t ON t.id = s.id_tecnico
    // ORDER BY s.fecha_creacion DESC
}
```

### `src/routes/web.php`
Rutas nuevas:
```php
$router->get('/dashboard/admin/usuarios',                  'AdminController@usuarios',        ['AuthMiddleware','AdminMiddleware']);
$router->get('/dashboard/admin/usuarios/:id/editar',       'AdminController@editUsuario',      ['AuthMiddleware','AdminMiddleware']);
$router->post('/dashboard/admin/usuarios/:id/editar',      'AdminController@updateUsuario',    ['AuthMiddleware','AdminMiddleware']);
$router->post('/dashboard/admin/usuarios/:id/suspender',   'AdminController@suspenderUsuario', ['AuthMiddleware','AdminMiddleware']);
$router->post('/dashboard/admin/usuarios/:id/eliminar',    'AdminController@eliminarUsuario',  ['AuthMiddleware','AdminMiddleware']);
$router->get('/dashboard/admin/tecnicos',                  'AdminController@tecnicos',         ['AuthMiddleware','AdminMiddleware']);
$router->get('/dashboard/admin/solicitudes',               'AdminController@solicitudes',      ['AuthMiddleware','AdminMiddleware']);
```

---

## Reglas de Negocio Importantes

1. **Auto-protección**: El admin no puede suspenderse ni eliminarse a sí mismo (comparar `$userId` vs `$_SESSION['user_id']`)
2. **FK en delete**: `solicitudes.id_cliente → users.id ON DELETE RESTRICT`. Capturar `PDOException` SQLSTATE `23xxx` y mostrar mensaje descriptivo
3. **Sincronización de roles al gestionar técnicos**:
   - `activo` o `suspendido` → `users.rol = 'tecnico'`
   - `pendiente` o `rechazado` → `users.rol = 'cliente'`
   - Esto ocurre en `User::setTechnicianStatus()` dentro de una transacción
4. **Redirects post-acción**: Todos van a la sección correspondiente (no a `/dashboard/admin` raíz)

---

## UI — Componentes Clave

### Sidebar
```
[ilustración]
• Inicio         → /
• Usuarios       → /dashboard/admin/usuarios    [activo = bg-primary-container]
• Técnicos       → /dashboard/admin/tecnicos
• Solicitudes    → /dashboard/admin/solicitudes
• Configuración  → # (placeholder)

[avatar inicial + nombre admin]
[Cerrar Sesión]
```

### Dropdown de Acciones (tabla usuarios)
Botón `more_horiz` por fila. Al click: panel con Ver Perfil, Editar (GET), Suspender (POST form), Eliminar (POST form + `confirm()`).
JS puro: un listener en `document` cierra todos y abre el target.

### Badges de Estado

| Estado | Clases Tailwind |
|--------|----------------|
| Activo / aprobado | `bg-primary-container/20 text-primary border border-primary/20` |
| Inactivo / suspendido | `bg-error-container text-on-error-container` |
| Pendiente | `bg-secondary-container text-on-secondary-container` |
| Rechazado | `bg-surface-container text-on-surface-variant border border-outline-variant` |

---

## Archivos de Vista a Crear

```
src/views/
└── dashboard/
    └── admin/
        ├── usuarios/
        │   ├── index.php     ← tabla + filtros + dropdowns
        │   └── edit.php      ← formulario de edición
        ├── tecnicos/
        │   └── index.php     ← rediseño de la tabla actual
        └── solicitudes/
            └── index.php     ← lista read-only
```

---

## Verificación

- Rutas de otros roles (cliente, técnico) no se rompen
- `/dashboard/admin` (URL vieja) redirige a `/dashboard/admin/usuarios`
- Suspender toggle funciona (activo ↔ inactivo y badge cambia)
- Delete con FK error muestra mensaje correcto, usuario no borrado
- Editar rol actualiza `users.rol` en DB
- Aprobar técnico redirige a `/dashboard/admin/tecnicos`
- Sidebar resalta solo la sección activa
- Dropdowns no se cortan por `overflow` de la tabla
