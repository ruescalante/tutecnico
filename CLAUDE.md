# MiTecnico — Contexto del Proyecto

## ¿Qué es?

**TuTecnico** es una plataforma web que conecta clientes con técnicos verificados para servicios a domicilio (plomería, electricidad, HVAC, etc.). Es un marketplace de dos lados:

- **Clientes** solicitan servicios técnicos
- **Técnicos** aplican para verificación y aceptan trabajos
- **Admins** aprueban/rechazan solicitudes de técnicos y monitorean la plataforma

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.3 |
| Base de datos | MySQL 8.0+ con PDO |
| Web Server | Apache 2.4 (Docker) |
| Frontend | HTML5 + Tailwind CSS (CDN) + Material Design 3 |
| Iconos/Fuentes | Material Symbols + Work Sans (Google Fonts) |
| Infraestructura | Docker + Docker Compose |
| Sesiones | PHP nativo `$_SESSION` |
| Contraseñas | bcrypt (`password_hash` / `password_verify`) |

## Arquitectura General

Este proyecto implementa **MVC sin framework** — el código del "framework" vive en `src/core/` y está escrito desde cero, con control total del ciclo request-response.

```
src/public/index.php        ← Front Controller (único punto de entrada)
         │
         ▼
src/core/Router.php         ← Dispatch + pipeline de middleware
         │
         ├── middleware/     ← Auth, roles, acceso admin
         │
         ▼
src/controllers/            ← Lógica HTTP (recibe request, llama modelos, renderiza)
         │
         ├── src/models/    ← Acceso a datos (PDO + queries)
         │
         └── src/views/     ← Templates PHP con layout master
```

## Estructura de Directorios

```
/
├── docker/                     # Dockerfile (PHP 8.3-Apache)
├── docker-compose.yml          # php + mysql + phpmyadmin
├── .env                        # DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
│
└── src/
    ├── public/
    │   ├── index.php           # Entry point: bootstrap, session, dispatch, error catch
    │   └── css/app.css
    │
    ├── core/                   # Micro-framework casero
    │   ├── Router.php          # Registro de rutas, matching, middleware chain
    │   ├── Controller.php      # Base controller: render() con layout + flash data
    │   ├── Model.php           # Base model: db() → PDO singleton
    │   ├── Request.php         # Abstracción HTTP: input(), param(), isAjax()
    │   ├── MiddlewareInterface.php
    │   └── ValidationException.php
    │
    ├── config/
    │   └── database.php        # Singleton PDO con variables de entorno
    │
    ├── routes/
    │   └── web.php             # Todas las rutas registradas
    │
    ├── controllers/
    │   ├── HomeController.php          # Página pública
    │   ├── AuthController.php          # Login, registro, logout
    │   ├── DashboardController.php     # Despacha por rol
    │   ├── ProfileController.php       # Perfil + solicitud de técnico
    │   ├── AdminController.php         # Gestión de técnicos y estadísticas
    │   ├── EjemploController.php       # CRUD de solicitudes (ejemplo)
    │   └── ErrorController.php         # Páginas 404 y 500
    │
    ├── models/
    │   ├── User.php            # Usuarios, auth, perfiles de técnico
    │   ├── Ejemplo.php         # CRUD de solicitudes
    │   └── Home.php            # Placeholder vacío
    │
    ├── middleware/
    │   ├── AuthMiddleware.php          # Verifica sesión activa y usuario en DB
    │   ├── AdminMiddleware.php         # Solo rol 'admin'
    │   ├── TechnicianMiddleware.php    # Solo rol 'tecnico'
    │   └── RoleMiddleware.php          # Helper estático de comparación de rol
    │
    ├── validators/
    │   └── Validator.php       # Reglas: required, min, max, email, in, regex
    │
    ├── helpers/
    │   └── sanitize.php        # Trim básico de inputs
    │
    ├── views/
    │   ├── layouts/
    │   │   ├── app.php         # Master layout (HTML + nav + footer)
    │   │   ├── nav.php         # Nav top + bottom nav mobile
    │   │   └── footer.php
    │   ├── home/index.php
    │   ├── auth/{login,register}.php
    │   ├── dashboard/{client,technician,admin}/index.php
    │   ├── dashboard/technician/waiting.php
    │   ├── profile/{index,edit,solicitud}.php
    │   ├── ejemplo/{index,create,edit}.php
    │   └── errors/{404,500}.php
    │
    └── database/
        ├── migrations.sql      # Schema: 10 tablas
        ├── seeds.sql           # Datos de prueba
        └── migrate.php         # Runner CLI (php migrate.php --seed)
```

## MVC: Cómo Funciona Cada Capa

### Models (`src/models/`)
Heredan de `Model` (que expone `self::db()` → PDO singleton). Contienen toda la lógica de acceso a datos con prepared statements.

**User.php** — el modelo más completo:
- `findByEmail()`, `findById()` — lookup de credenciales
- `createClient()` — registro con bcrypt
- `upsertTechnicianApplication()` — INSERT/UPDATE de perfil técnico
- `setTechnicianStatus()` — aprobación/rechazo con transacción DB
- `listTechnicianApplications()` — para el panel admin
- `countByRole()`, `updateUser()` — stats y edición de perfil

**Ejemplo.php** — CRUD básico sobre la tabla `solicitudes`:
`all()`, `find()`, `create()`, `update()`, `delete()`

### Controllers (`src/controllers/`)
Heredan de `Controller` que provee `$this->render('vista', $data)`. Este método:
1. Extrae flash data de sesión (`errors`, `old`, `success`) y la elimina
2. Hace output buffering del view
3. Inyecta el contenido como `$content` en `layouts/app.php`

Cada método de controller sigue el patrón:
```php
public function accion(): void {
    $data = Model::query();         // Obtener datos
    $this->render('vista', $data);  // Renderizar
}
```

Para formularios POST:
```php
public function store(): void {
    $validated = Validator::validate($_POST, $rules);  // Lanza ValidationException si falla
    Model::create($validated);
    header('Location: /ruta');
}
```

### Views (`src/views/`)
PHP puro con Tailwind CSS. El layout master (`layouts/app.php`) recibe `$content` (el HTML del view actual) e inserta nav y footer. Las vistas acceden a variables pasadas desde el controller como variables PHP normales.

## Routing

Definido en `src/routes/web.php`, registrado en el `Router`:

```php
$router->get('/ruta', 'Controller@metodo');
$router->post('/ruta', 'Controller@metodo', ['AuthMiddleware']);
$router->get('/ruta/:id', 'Controller@metodo', ['AuthMiddleware', 'AdminMiddleware']);
```

**Parámetros de ruta:**
- `:id` → string
- `:id(int)` → solo enteros (`\d+`)
- `:id?` → opcional

**Flujo de dispatch:**
1. `Router::dispatch()` extrae METHOD + URI
2. Itera rutas buscando match por regex
3. Instancia el controller, construye el stack de middleware
4. Encadena middleware via `array_reduce(array_reverse(...))` (LIFO)
5. Inyecta `Request` al método del controller si lo espera (reflexión)
6. 404 si no hay match

### Rutas Registradas

| Método | Ruta | Controller@Método | Middleware |
|--------|------|-------------------|-----------|
| GET | `/` | HomeController@index | — |
| GET/POST | `/login` | AuthController@showLogin / login | — |
| GET/POST | `/registro` | AuthController@showRegister / register | — |
| POST | `/logout` | AuthController@logout | — |
| GET | `/dashboard` | DashboardController@index | Auth |
| GET | `/dashboard/cliente` | DashboardController@client | Auth |
| GET | `/dashboard/tecnico` | DashboardController@technician | Auth + Technician |
| GET | `/dashboard/admin` | AdminController@index | Auth + Admin |
| POST | `/dashboard/admin/tecnicos/:id/estado` | AdminController@updateTechnicianStatus | Auth + Admin |
| GET | `/perfil` | ProfileController@index | Auth |
| GET/POST | `/perfil/editar` | ProfileController@showEditForm / updateProfile | Auth |
| GET/POST | `/perfil/solicitud-tecnico` | ProfileController@showTechnicianForm / applyTechnician | Auth |
| GET/POST | `/ejemplo` | EjemploController@index / store | Auth |
| GET | `/ejemplo/create` | EjemploController@create | Auth |
| GET/POST | `/ejemplo/edit/:id` | EjemploController@edit / update | Auth |
| POST | `/ejemplo/delete/:id` | EjemploController@destroy | Auth |

## Middleware Pipeline

```
Request → AuthMiddleware → RoleMiddleware (si aplica) → Controller
```

**AuthMiddleware**: Verifica `$_SESSION['user_id']`, consulta DB para confirmar que el usuario sigue activo, sincroniza rol y nombre en sesión. Guarda `intended_url` para redirect post-login. Redirige a `/login` si no autenticado.

**AdminMiddleware** / **TechnicianMiddleware**: Envuelven `RoleMiddleware::ensure($rol)`, que compara `$_SESSION['role']` y redirige a `/dashboard` con 403 si no coincide.

## Sesiones y Autenticación

**Variables de sesión:**
- `$_SESSION['user_id']` — PK del usuario
- `$_SESSION['role']` — `'cliente'`, `'tecnico'`, `'admin'`
- `$_SESSION['user_name']` — nombre para UI
- `$_SESSION['intended_url']` — redirect post-login
- `$_SESSION['errors']` — errores de validación (flash)
- `$_SESSION['old']` — valores anteriores del form (flash)
- `$_SESSION['success']` — mensaje de éxito (flash)

**Login**: valida credenciales → `password_verify()` → `session_regenerate_id(true)` → set session vars → redirect por rol.

**Redirect por rol** (`redirectByRole`):
- `admin` → `/dashboard/admin`
- `tecnico` con estado `aprobado` → `/dashboard/tecnico`
- `tecnico` pendiente/rechazado → `/dashboard/tecnico/espera`
- `cliente` → `/dashboard/cliente`

## Base de Datos

**Conexión**: Singleton PDO en `src/config/database.php`, configurado por variables de entorno. Usa `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, prepared statements reales.

**10 Tablas:**

| Tabla | Propósito |
|-------|-----------|
| `users` | Clientes, técnicos y admins. Campo `rol` y flag `activo`. |
| `categorias` | Tipos de servicio (plomería, electricidad, etc.) |
| `tecnico_perfiles` | Aplicaciones de técnicos: zona, descripción, `estado` (pendiente/aprobado/rechazado/suspendido) |
| `tecnico_categorias` | Pivot técnico ↔ categorías |
| `solicitudes` | Pedidos de servicio con estado |
| `mensajes` | Chat dentro de una solicitud |
| `cotizaciones` | Presupuestos por solicitud |
| `foto_trabajos` | Portfolio del técnico |
| `calificaciones` | Reseñas post-trabajo |
| `notificaciones` | Alertas por usuario |

**Patrón de queries:**
```php
$stmt = self::db()->prepare("SELECT * FROM users WHERE correo = :email");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(); // o fetchAll()
```

**Transacciones** (usado en aprobación de técnicos):
```php
$pdo->beginTransaction();
// ... múltiples queries
$pdo->commit(); // o rollBack() en catch
```

## Validación y Manejo de Errores

`Validator::validate($data, $rules)` lanza `ValidationException` con array de errores por campo.

En `index.php` se captura globalmente:
- Si es AJAX: respuesta JSON 422
- Si es HTTP normal: redirect con `$_SESSION['errors']` y `$_SESSION['old']`

Reglas disponibles: `required`, `min:N`, `max:N`, `email`, `integer`, `in:a,b,c`, `regex:/pattern/`

## Cómo Agregar Funcionalidad

**Nueva página:**
1. Registrar ruta en `src/routes/web.php`
2. Crear/extender controller en `src/controllers/`
3. Crear view en `src/views/dominio/vista.php`
4. Llamar `$this->render('dominio/vista', $datos)` desde el controller

**Nueva interacción con DB:**
1. Crear/extender model en `src/models/` extendiendo `Model`
2. Usar `self::db()->prepare($sql)->execute($params)`
3. Llamar el model desde el controller

**Nueva validación:**
1. Llamar `Validator::validate($_POST, ['campo' => 'required|email|max:100'])` en el controller
2. La excepción se captura globalmente y flashea errores a la sesión

**Nuevo gate de acceso:**
1. Pasar middleware en la ruta: `['AuthMiddleware', 'AdminMiddleware']`

## Levantar el Proyecto

```bash
# Con Docker (recomendado)
docker-compose up -d --build
docker-compose exec php php src/database/migrate.php --seed

# Sin Docker
php src/database/migrate.php --seed
php -S localhost:8000 -t src/public

# phpMyAdmin disponible en http://localhost:8081
```

## Puntos a Tener en Cuenta

- **No hay CSRF tokens** en los formularios (pendiente de implementar)
- **No hay suite de tests** automatizados
- El script de seeds no es idempotente (puede fallar en segunda ejecución)
- El input sanitization es básico (solo trim)
- No hay logging estructurado de acciones de admin
