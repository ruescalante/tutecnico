# MiTecnico - Plataforma de Gestión de Solicitudes Técnicas

Aplicación web MVC desarrollada en PHP vanilla para la gestión de solicitudes de servicios técnicos. Incluye un sistema de enrutamiento avanzado con soporte a patrones de rutas, middleware y validación centralizada.

---

## 📋 Tabla de Contenidos

1. [Estructura del Proyecto](#estructura-del-proyecto)
2. [Requisitos](#requisitos)
3. [Instalación](#instalación)
4. [Configuración](#configuración)
5. [Arquitectura](#arquitectura)
6. [Sistema de Rutas](#sistema-de-rutas)
7. [Validación](#validación)
8. [Middleware](#middleware)
9. [Request & Response](#request--response)
10. [Modelos](#modelos)
11. [Controladores](#controladores)
12. [Vistas](#vistas)
13. [Ejemplos de Uso](#ejemplos-de-uso)
14. [Mejoras Futuras](#mejoras-futuras)

---

## Estructura del Proyecto

```
MiTecnico/
├── docker/
│   └── Dockerfile          # Imagen Docker para PHP + Apache
├── src/
│   ├── config/
│   │   └── database.php    # Configuración DB (PDO Singleton)
│   ├── core/
│   │   ├── Router.php      # Enrutador con patrones y middleware
│   │   ├── Request.php     # Encapsulación de petición HTTP
│   │   ├── Controller.php  # Clase base para controladores
│   │   ├── Model.php       # Clase base para modelos
│   │   └── MiddlewareInterface.php  # Interfaz para middleware
│   ├── middleware/
│   │   ├── AuthMiddleware.php      # Middleware de autenticación (placeholder)
│   │   ├── CsrfMiddleware.php      # Validación de tokens CSRF
│   │   └── MiddlewareInterface.php # Interfaz de middleware
│   ├── controllers/
│   │   ├── HomeController.php      # Controlador principal
│   │   ├── EjemploController.php   # Controlador de solicitudes
│   │   └── ErrorController.php     # Manejo de errores
│   ├── models/
│   │   ├── Home.php
│   │   └── Ejemplo.php      # Modelo de solicitudes
│   ├── validators/
│   │   └── Validator.php    # Utilitario de validación centralizado
│   ├── exceptions/
│   │   └── ValidationException.php # Excepción de validación
│   ├── helpers/
│   │   └── sanitize.php     # Funciones sanitizadoras y helpers CSRF
│   ├── database/
│   │   ├── migrate.php
│   │   ├── migrations.sql
│   │   └── seeds.sql
│   ├── routes/
│   │   └── web.php          # Definición de rutas
│   ├── views/
│   │   ├── ejemplo/
│   │   │   ├── index.php    # Listado de solicitudes
│   │   │   ├── create.php   # Formulario crear
│   │   │   └── edit.php     # Formulario editar
│   │   ├── home/
│   │   │   └── index.php
│   │   ├── errors/
│   │   │   └── 404.php
│   │   └── layouts/
│   │       ├── app.php      # Layout principal
│   │       ├── nav.php      # Navegación
│   │       └── footer.php   # Pie de página
│   ├── public/
│   │   ├── index.php        # Front Controller
│   │   ├── css/
│   │   │   └── app.css
│   │   └── .htaccess        # Reescritura de URLs
│   └── database/
│       └── migrations.sql   # Script de BD
├── docker-compose.yml       # Orquestación de servicios
├── .env                     # Variables de entorno
└── README.md               # Este archivo
```

---

## Requisitos

- **PHP 8.0+** (con soporte para tipos declarados)
- **MySQL 8.0+**
- **Docker & Docker Compose** (opcional, para entorno containerizado)
- **Apache 2.4** con módulo `mod_rewrite` habilitado

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone <repo-url>
cd MiTecnico
```

### 2. Configurar variables de entorno

Copiar `.env` y ajustar credenciales:

```bash
cp .env.example .env
```

Editar `.env`:

```env
DB_HOST=mysql
DB_PORT=3306
DB_NAME=tutecnico
DB_USER=admin
DB_PASS=admin
DB_ROOT_PASS=root_password
```

### 3. Levantar con Docker Compose

```bash
docker-compose up -d
```

Esto inicia:
- **PHP/Apache** (puerto 8080)
- **MySQL** (puerto 3306)
- **phpMyAdmin** (puerto 8081)

### 4. Inicializar Base de Datos

```bash
docker exec php_app php /var/www/html/src/database/migrate.php
```

### 5. Acceder a la aplicación

- App: http://localhost:8080
- phpMyAdmin: http://localhost:8081

---

## Configuración

### Archivo de Configuración BD

**`src/config/database.php`**

```php
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'tutecnico');
define('DB_USER',    getenv('DB_USER')    ?: 'admin');
define('DB_PASS',    getenv('DB_PASS')    ?: 'admin');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO {
        // Singleton PDO con opciones seguras
        // ...
    }
}
```

---

## Arquitectura

### Patrón MVC

```
Petición HTTP
    ↓
Front Controller (src/public/index.php)
    ↓
Router → Matchea ruta y parámetros
    ↓
Middleware Stack → Validación CSRF, Auth, Logging
    ↓
Controlador → Lógica de negocio
    ↓
Modelo → Acceso a datos
    ↓
Vista → Renderización HTML
    ↓
Response HTTP
```

### Bootstrap (src/public/index.php)

```php
<?php
define('BASE_PATH', dirname(__DIR__));

// Cargar núcleo
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/core/Request.php';
require_once BASE_PATH . '/exceptions/ValidationException.php';
require_once BASE_PATH . '/helpers/sanitize.php';

// Iniciar sesión
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Crear router
$router = new Router();

// Cargar rutas
require_once BASE_PATH . '/routes/web.php';

// Despachar petición con manejo de excepciones
try {
    $router->dispatch();
} catch (ValidationException $e) {
    // Manejo de errores de validación
    // Respuesta JSON para AJAX, HTML para navegador
} catch (Throwable $e) {
    // Manejo de errores inesperados
    error_log($e->getMessage());
    http_response_code(500);
}
```

---

## Sistema de Rutas

### Definición de Rutas

**`src/routes/web.php`**

```php
<?php

// Registro de rutas
$router->get('/', 'HomeController@index');

// Rutas con middleware
$auth = ['AuthMiddleware'];
$router->get('/ejemplo', 'EjemploController@index');
$router->post('/ejemplo', 'EjemploController@store');
$router->get('/ejemplo/create', 'EjemploController@create', $auth);
$router->get('/ejemplo/edit', 'EjemploController@edit', $auth);
$router->post('/ejemplo/update', 'EjemploController@update', $auth);
$router->post('/ejemplo/delete', 'EjemploController@destroy', $auth);
```

### Sintaxis de Rutas

El Router soporta patrones de rutas avanzados:

```php
// Rutas simples
$router->get('/home', 'HomeController@index');

// Rutas con parámetros
$router->get('/ejemplo/:id', 'EjemploController@show');
$router->get('/user/:username', 'UserController@profile');

// Parámetros con restricción de tipo (int)
$router->get('/producto/:id(int)', 'ProductController@show');
$router->get('/page/:page(int)', 'PageController@index');

// Parámetros opcionales
$router->get('/search/:query?', 'SearchController@index');

// Métodos HTTP soportados
$router->get($path, $action, $middleware = []);
$router->post($path, $action, $middleware = []);
$router->put($path, $action, $middleware = []);
$router->patch($path, $action, $middleware = []);
$router->delete($path, $action, $middleware = []);
```

### Cómo Funciona `matchPath()`

El Router convierte patrones en expresiones regulares:

```
Ruta:     /ejemplo/:id(int)
Patrón:   #^/ejemplo/(?P<id>\d+)$#

Solicitud: /ejemplo/123
Match:     ✓ (extrae id=123)

Solicitud: /ejemplo/abc
Match:     ✗ (id debe ser número)
```

---

## Validación

### Clase `Validator`

**`src/validators/Validator.php`**

Utilidad centralizada para validación que lanza `ValidationException` si falla.

#### Reglas Disponibles

```php
Validator::validate($data, [
    'titulo'      => 'required|max:100',
    'descripcion' => 'required|max:500',
    'email'       => 'email',
    'edad'        => 'integer|min:18|max:100',
    'estado'      => 'in:pendiente,activo,cerrado',
    'telefono'    => 'regex:/^\d{10}$/',
]);
```

**Reglas Soportadas:**

| Regla | Descripción | Ejemplo |
|-------|-------------|---------|
| `required` | Campo obligatorio | `'nombre' => 'required'` |
| `max:N` | Máximo N caracteres | `'titulo' => 'max:100'` |
| `min:N` | Mínimo N caracteres | `'password' => 'min:8'` |
| `integer` | Debe ser entero | `'edad' => 'integer'` |
| `email` | Formato email válido | `'email' => 'email'` |
| `in:val1,val2` | Valor dentro de lista | `'rol' => 'in:admin,user'` |
| `regex:pattern` | Patrón regex | `'dni' => 'regex:/^\d{8}[A-Z]$/'` |

### Uso en Controlador

```php
public function store(Request $request): void
{
    // 1. Sanitizar entrada
    $input = sanitize_array($request->all());
    
    // 2. Validar (lanza ValidationException si falla)
    Validator::validate($input, [
        'titulo' => 'required|max:100',
        'descripcion' => 'required',
        'direccion' => 'required',
    ]);
    
    // 3. Si llegamos aquí, validación pasó
    // Persistir datos seguros
    Ejemplo::create($input);
    
    header('Location: /ejemplo');
    exit;
}
```

### Manejo de Excepciones

La excepción se captura en `public/index.php`:

```php
try {
    $router->dispatch();
} catch (ValidationException $e) {
    http_response_code(422);
    
    if ($request->isAjax()) {
        header('Content-Type: application/json');
        echo json_encode(['errors' => $e->getErrors()]);
    } else {
        // Mostrar errores en HTML
        foreach ($e->getErrors() as $field => $messages) {
            foreach ($messages as $msg) {
                echo "<p>$field: $msg</p>";
            }
        }
    }
    exit;
}
```

---

## Middleware

### Interfaz de Middleware

**`src/middleware/MiddlewareInterface.php`**

```php
<?php
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next);
}
```

### Middleware de Autenticación

**`src/middleware/AuthMiddleware.php`**

```php
<?php
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            // Si AJAX: responder con JSON 401
            if ($request->isAjax()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            // Si navegador: redirigir a login
            header('Location: /login');
            exit;
        }

        // Usuario presente: continuar
        return $next($request);
    }
}
```

### Middleware CSRF

**`src/middleware/CsrfMiddleware.php`**

```php
<?php
class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        // Validar solo en métodos NO-seguros
        $method = strtoupper($request->method);
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (session_status() !== PHP_SESSION_ACTIVE) session_start();
            
            $tokenSession = $_SESSION['csrf_token'] ?? null;
            $tokenInput = $request->input('_csrf') ?? null;
            
            if (!$tokenSession || !$tokenInput || !hash_equals($tokenSession, $tokenInput)) {
                throw new ValidationException(['csrf' => 'Token CSRF inválido']);
            }
        }

        return $next($request);
    }
}
```

### Registrar Middleware

#### Globalmente

```php
$router->addMiddleware('LoggingMiddleware');
$router->addMiddleware('CsrfMiddleware');
```

#### Por Ruta

```php
$router->post('/ejemplo', 'EjemploController@store', ['CsrfMiddleware']);

// Múltiples middleware
$router->post('/admin/delete', 'AdminController@destroy', [
    'AuthMiddleware',
    'AdminOnly',
    'CsrfMiddleware'
]);
```

### Stack de Middleware

El Router construye una cadena de middleware en orden:

```
Petición
  ↓
Middleware Global 1
  ↓
Middleware Global 2
  ↓
Middleware por Ruta 1
  ↓
Middleware por Ruta 2
  ↓
Controlador (final)
  ↓
Response
```

---

## Request & Response

### Clase `Request`

**`src/core/Request.php`**

Encapsula toda la información HTTP de la petición.

```php
$request = new Request($_GET, $_POST, $_SERVER, $params);

// Obtener datos de entrada
$titulo = $request->input('titulo');           // GET o POST
$email = $request->input('email', 'default');  // Con default
$all = $request->all();                        // Todos los datos

// Verificar existencia
if ($request->has('id')) { ... }

// Parámetros de ruta
$id = $request->param('id');

// Información de petición
$method = $request->method;  // GET, POST, etc.
$path = $request->path;      // /ejemplo/123
$isAjax = $request->isAjax();

// Datos JSON
$data = $request->json();

// Headers
$contentType = $request->headers['Content-Type'] ?? null;
```

---

## Modelos

### Clase Base `Model`

**`src/core/Model.php`**

```php
<?php
class Model
{
    protected static function db(): PDO
    {
        return Database::getInstance();
    }
}
```

### Modelo `Ejemplo`

**`src/models/Ejemplo.php`**

```php
<?php
class Ejemplo extends Model
{
    // Obtener todos
    public static function all(): array
    {
        return self::db()->query('
            SELECT s.*, 
                   c.nombre AS cliente,
                   t.nombre AS tecnico
            FROM solicitudes s
            JOIN users c ON s.id_cliente = c.id
            LEFT JOIN users t ON s.id_tecnico = t.id
            ORDER BY s.fecha_creacion DESC
        ')->fetchAll();
    }

    // Obtener por ID
    public static function find(int $id): array|false
    {
        $stmt = self::db()->prepare('SELECT * FROM solicitudes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Crear
    public static function create(array $data): void
    {
        $stmt = self::db()->prepare('
            INSERT INTO solicitudes (id_cliente, titulo, descripcion, direccion)
            VALUES (:id_cliente, :titulo, :descripcion, :direccion)
        ');
        $stmt->execute($data);
    }

    // Actualizar
    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare('
            UPDATE solicitudes
            SET titulo = :titulo, descripcion = :descripcion,
                direccion = :direccion, estado = :estado
            WHERE id = :id
        ');
        $stmt->execute([...$data, 'id' => $id]);
    }

    // Eliminar
    public static function delete(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM solicitudes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
```

---

## Controladores

### Clase Base `Controller`

**`src/core/Controller.php`**

```php
<?php
class Controller
{
    protected function render(string $view, array $data = []): void
    {
        // Convertir keys del array en variables
        extract($data);

        // Capturar HTML de la vista
        ob_start();
        require_once BASE_PATH . "/views/{$view}.php";
        $content = ob_get_clean();

        // Renderizar con layout
        require_once BASE_PATH . '/views/layouts/app.php';
    }
}
```

### Ejemplo: `EjemploController`

**`src/controllers/EjemploController.php`**

```php
<?php
class EjemploController extends Controller
{
    public function index(): void
    {
        $this->render('ejemplo/index', [
            'solicitudes' => Ejemplo::all(),
            'pageTitle'   => 'TuTecnico - Solicitudes',
        ]);
    }

    public function create(): void
    {
        $this->render('ejemplo/create', [
            'pageTitle' => 'TuTecnico - Nueva Solicitud',
        ]);
    }

    // Recibe Request automáticamente si declara parámetro
    public function store(Request $request): void
    {
        // 1. Sanitizar
        $input = sanitize_array($request->all());

        // 2. Validar (lanza ValidationException)
        Validator::validate($input, [
            'titulo' => 'required',
            'descripcion' => 'required',
            'direccion' => 'required',
        ]);

        // 3. Persistir
        Ejemplo::create([
            'id_cliente'  => 1, // TODO: obtener de sesión
            'titulo'      => $input['titulo'],
            'descripcion' => $input['descripcion'],
            'direccion'   => $input['direccion'],
        ]);

        header('Location: /ejemplo');
        exit;
    }

    public function edit(Request $request): void
    {
        $id = (int) $request->input('id', 0);
        $solicitud = Ejemplo::find($id);

        if (!$solicitud) {
            header('Location: /ejemplo');
            exit;
        }

        $this->render('ejemplo/edit', [
            'pageTitle' => 'TuTecnico - Editar Solicitud',
            'solicitud' => $solicitud,
        ]);
    }

    public function update(Request $request): void
    {
        $input = sanitize_array($request->all());
        $id = (int) ($input['id'] ?? 0);

        Validator::validate($input, [
            'titulo' => 'required',
            'descripcion' => 'required',
            'direccion' => 'required',
        ]);

        Ejemplo::update($id, [
            'titulo'      => $input['titulo'],
            'descripcion' => $input['descripcion'],
            'direccion'   => $input['direccion'],
            'estado'      => $input['estado'] ?? 'pendiente',
        ]);

        header('Location: /ejemplo');
        exit;
    }

    public function destroy(Request $request): void
    {
        $id = (int) ($request->input('id') ?? 0);
        Ejemplo::delete($id);
        header('Location: /ejemplo');
        exit;
    }
}
```

### Inyección Automática de Request

El Router usa Reflection para detectar si el método espera `Request`:

```php
$ref = new ReflectionMethod($controller, $methodName);
if ($ref->getNumberOfParameters() > 0) {
    // Método espera parámetros → pasar Request
    return $controller->$methodName($request);
}
// Método sin parámetros → llamar sin argumentos
return $controller->$methodName();
```

---

## Vistas

### Layout Principal

**`src/views/layouts/app.php`**

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'TuTecnico') ?></title>
    <link rel="stylesheet" href="/css/app.css">
    <?= $extraCss ?? '' ?>
</head>
<body>
    <?php require_once BASE_PATH . '/views/layouts/nav.php' ?>
    <main>
        <?= $content ?>  <!-- Contenido de la vista -->
    </main>
    <?php require_once BASE_PATH . '/views/layouts/footer.php' ?>
    <?= $extraJs ?? '' ?>
</body>
</html>
```

### Vista de Formulario

**`src/views/ejemplo/create.php`**

```php
<div class="container">
    <h1>Nueva Solicitud</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif ?>

    <form action="/ejemplo" method="POST" class="form">
        <!-- Token CSRF (generado por helper) -->
        <?= csrf_field() ?>
        
        <label>Título
            <input type="text" name="titulo" 
                   value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
        </label>
        
        <label>Descripción
            <textarea name="descripcion" required>
<?= htmlspecialchars($_POST['descripcion'] ?? '') ?>
            </textarea>
        </label>
        
        <label>Dirección
            <input type="text" name="direccion" 
                   value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>" required>
        </label>
        
        <div class="form-actions">
            <a href="/ejemplo" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear</button>
        </div>
    </form>
</div>
```

### Helpers en Vistas

```php
<?php
// Generar token CSRF oculto
<?= csrf_field() ?>

// Salida segura
<?= htmlspecialchars($user_input) ?>

// Condicionales
<?php if ($solicitudes): ?>
    <table>
        <?php foreach ($solicitudes as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['titulo']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
```

---

## Ejemplos de Uso

### 1. Crear Nueva Solicitud

```php
// Formulario en vista
<form action="/ejemplo" method="POST">
    <?= csrf_field() ?>
    <input type="text" name="titulo" required>
    <textarea name="descripcion" required></textarea>
    <input type="text" name="direccion" required>
    <button type="submit">Crear</button>
</form>

// Controlador maneja POST /ejemplo
// EjemploController::store(Request $request) ← inyección automática
// Valida → Crea en BD → Redirige
```

### 2. Ruta Parametrizada

```php
// Definir ruta con parámetro
$router->get('/ejemplo/:id(int)', 'EjemploController@show');

// Solicitud: /ejemplo/42
// Extrae parámetro id=42
// Controlador recibe via $request->param('id')

public function show(Request $request): void
{
    $id = (int) $request->param('id');
    $solicitud = Ejemplo::find($id);
    // ...
}
```

### 3. Middleware en Ruta

```php
// Solo usuarios autenticados pueden crear/editar
$router->get('/ejemplo/create', 'EjemploController@create', ['AuthMiddleware']);
$router->post('/ejemplo', 'EjemploController@store', ['AuthMiddleware', 'CsrfMiddleware']);

// Si no autenticado: AuthMiddleware redirige a /login
// Si CSRF inválido: CsrfMiddleware lanza ValidationException
```

### 4. Validación Compleja

```php
Validator::validate($data, [
    'email'      => 'required|email',
    'password'   => 'required|min:8|max:50',
    'edad'       => 'integer|min:18|max:120',
    'documento'  => 'required|regex:/^\d{8}[A-Z]$/',
    'rol'        => 'in:cliente,tecnico,admin',
]);

// Si falla: ValidationException con detalle de errores
// Si pasa: continuar ejecución
```

### 5. Respuesta JSON para AJAX

```php
// En index.php, captura ValidationException
if ($request->isAjax()) {
    header('Content-Type: application/json');
    echo json_encode(['errors' => $e->getErrors()]);
}

// Cliente JavaScript recibe:
// { "errors": { "email": ["Email inválido"], "password": ["Mínimo 8 caracteres"] } }
```

---

## Mejoras Futuras

### Corto Plazo

- [ ] **Autenticación completa**: Implementar login/logout con sesiones
- [ ] **CSRF global**: Activar `CsrfMiddleware` en todas las rutas POST
- [ ] **Paginación**: Agregar paginación a `Ejemplo::all()`
- [ ] **Logging**: Sistema de logging estructurado
- [ ] **Tests unitarios**: PHPUnit para Router y Validator

### Mediano Plazo

- [ ] **Composer + Autoload PSR-4**: Eliminar `require_once` manuales
- [ ] **ORM Ligero**: Reemplazar queries SQL manuales
- [ ] **API REST**: Endpoints JSON con autenticación Bearer token
- [ ] **Validación Client-side**: JavaScript para validación inmediata
- [ ] **Cache**: Redis para sesiones y caché de datos

### Largo Plazo

- [ ] **GraphQL**: Alternativa a REST API
- [ ] **Notificaciones**: Email y WebSockets para actualizaciones en tiempo real
- [ ] **Roles y Permisos**: Sistema granular de autorización
- [ ] **Auditoría**: Historial de cambios en solicitudes
- [ ] **Frontend Modern**: React/Vue para UX mejorada

---

## Seguridad

### ✅ Implementado

- **Prepared Statements**: Prevención de inyección SQL
- **CSRF Token**: Token en formularios POST
- **XSS Protection**: Escapado de salidas con `htmlspecialchars()`
- **Input Sanitization**: Trimming y normalización
- **Error Handling**: Logging sin exponer stack traces en producción

### ⚠️ Por Hacer

- **HTTPS Forzado**: Redirigir HTTP → HTTPS
- **Rate Limiting**: Limitar intentos de login
- **Validación de Headers**: Content-Type, Origin, etc.
- **Secrets Management**: No commitear credenciales en `.env`
- **Actualización Dependencias**: Mantener PHP y librerías actualizadas

---

## Comandos Útiles

### Docker

```bash
# Iniciar servicios
docker-compose up -d

# Detener servicios
docker-compose down

# Ver logs
docker-compose logs -f php

# Acceder a la shell de PHP
docker exec -it php_app bash

# Ejecutar PHP en contenedor
docker exec php_app php /var/www/html/src/database/migrate.php
```

### Base de Datos

```bash
# Conectarse a MySQL
docker exec -it mysql_db mysql -u admin -p tutecnico

# Restaurar dump
docker exec -i mysql_db mysql -u admin -p tutecnico < src/database/migrations.sql

# Crear usuario
CREATE USER 'admin'@'%' IDENTIFIED BY 'admin';
GRANT ALL PRIVILEGES ON tutecnico.* TO 'admin'@'%';
```

### Git

```bash
git add .
git commit -m "feat: router con patrones y validación centralizada"
git push origin main
```

---

## Contacto & Soporte

Para preguntas, issues o sugerencias, contactar al equipo de desarrollo.

---

## Licencia

Este proyecto está bajo licencia [MIT/Apache 2.0 - especificar].

---

**Última actualización**: Mayo 2026  
**Versión**: 2.0 (Router, Validación, Middleware)
