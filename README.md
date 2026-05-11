# TuTecnico

Aplicación web en PHP con una estructura MVC ligera para gestionar solicitudes técnicas. El proyecto usa rutas propias, controladores, modelos, vistas y un validador centralizado para formularios.

## Estado actual

Lo que ya está montado en la repo:

- Front controller en `src/public/index.php`.
- Router propio con soporte para métodos HTTP y middleware por ruta.
- Controladores y vistas para `home` y `ejemplo`.
- Modelo `Ejemplo` para operar sobre la tabla `solicitudes`.
- `Request` para encapsular la petición HTTP.
- `Validator` para validar datos en controladores.
- `ValidationException` para lanzar errores de validación.
- Sanitización básica con `sanitize_array()`.
- `AuthMiddleware` como base para autenticación futura.
- Manejo de errores de validación con redirección al formulario anterior y datos "old" en sesión.

## Estructura

```text
src/
├── config/
│   └── database.php
├── core/
│   ├── Controller.php
│   ├── Model.php
│   ├── Request.php
│   └── Router.php
├── controllers/
│   ├── EjemploController.php
│   ├── ErrorController.php
│   └── HomeController.php
├── exceptions/
│   └── ValidationException.php
├── helpers/
│   └── sanitize.php
├── middleware/
│   ├── AuthMiddleware.php
│   └── MiddlewareInterface.php
├── models/
│   ├── Ejemplo.php
│   └── Home.php
├── public/
│   ├── index.php
│   └── css/app.css
├── routes/
│   └── web.php
├── validators/
│   └── Validator.php
└── views/
    ├── ejemplo/
    │   ├── create.php
    │   ├── edit.php
    │   └── index.php
    ├── errors/
    │   └── 404.php
    ├── home/
    │   └── index.php
    └── layouts/
        ├── app.php
        ├── footer.php
        └── nav.php
```

## Arquitectura

El flujo general es:

```text
HTTP Request
  -> src/public/index.php
  -> Router
  -> Middleware de la ruta
  -> Controlador
  -> Modelo
  -> Vista
  -> Response
```

### Front controller

El archivo principal es `src/public/index.php`. Ahí se cargan los componentes base, se inicia la sesión y se despachan las rutas.

### Router

`src/core/Router.php` permite registrar rutas así:

```php
$router->get('/ejemplo', 'EjemploController@index');
$router->post('/ejemplo', 'EjemploController@store');
$router->get('/ejemplo/create', 'EjemploController@create');
$router->get('/ejemplo/edit/:id', 'EjemploController@edit');
$router->post('/ejemplo/:id', 'EjemploController@update');
$router->post('/ejemplo/delete/:id', 'EjemploController@destroy');
```

Soporta parámetros dinámicos en URLs (ej. `:id`) y middleware por ruta, además de métodos como `put`, `patch` y `delete`.

Los parámetros dinámicos se acceden en el controlador con `$request->param('id')`.

### Request

`src/core/Request.php` encapsula:

- `method`
- `path`
- `headers`
- `input()`
- `all()`
- `param()`
- `isAjax()`

Eso evita depender directamente de `$_GET`, `$_POST` y `$_SERVER` en los controladores.

## Validación

La validación se hace con `Validator::validate()` en los controladores.

Ejemplo:

```php
Validator::validate($input, [
    'titulo' => 'required|min:5|max:120',
    'descripcion' => 'required|min:10|max:1000',
    'direccion' => 'required|min:5|max:255',
]);
```

Reglas disponibles actualmente:

- `required`
- `min:N`
- `max:N`
- `integer`
- `email`
- `in:val1,val2`
- `regex:patron`

Si falla, se lanza `ValidationException` con un arreglo de errores por campo.

## Flujo de errores de validación

La intención actual del proyecto es esta:

1. El controlador valida con `Validator`.
2. Si falla, la excepción llega al `index.php`.
3. `index.php` guarda `errors` y `old` en sesión.
4. Se redirige al formulario anterior usando `_back_url` o `HTTP_REFERER`.
5. La vista lee `errors` y `old` para mostrar mensajes debajo de cada campo.

Ejemplo de formulario:

```php
<input type="hidden" name="_back_url" value="/ejemplo/create">
```

## Controladores

### `EjemploController`

`src/controllers/EjemploController.php` maneja:

- listar solicitudes (`index`)
- mostrar formulario de creación (`create`)
- crear solicitud (`store`)
- mostrar formulario de edición (`edit`)
- actualizar solicitud (`update`)
- eliminar solicitud (`destroy`)

Usa `sanitize_array()` antes de validar y guardar datos.

### `HomeController`

Muestra la pantalla principal del proyecto.

### `ErrorController`

Renderiza páginas para errores como 404 y 500.

## Modelo

`src/models/Ejemplo.php` contiene consultas directas con PDO:

- `all()`
- `find()`
- `create()`
- `update()`
- `delete()`

Se apoya en `Model::db()` y en `Database::getInstance()`.

## Vistas

Las vistas están en `src/views/` y usan el layout principal `src/views/layouts/app.php`.

### Formularios de ejemplo

- `src/views/ejemplo/create.php`
- `src/views/ejemplo/edit.php`

Ambas vistas muestran errores por campo usando el arreglo `$errors` y conservan valores previos con `$old`.

## Middleware

Existe la base para middleware en `src/middleware/`.

### `AuthMiddleware`

`AuthMiddleware` revisa `$_SESSION['user_id']` y, si no existe:

- responde `401` para AJAX
- redirige a `/login` para peticiones normales

Por ahora es un placeholder para agregar login más adelante.

> Nota: en la configuración actual, el middleware existe pero no está aplicado globalmente a todas las rutas de ejemplo.

## Formulario y mensajes visuales

La UI del formulario se puede mejorar mostrando cada error debajo del campo respectivo. La estructura actual ya está preparada para eso con:

```php
<?php if (!empty($errors['titulo'])): ?>
    <p class="error"><?= htmlspecialchars($errors['titulo'][0]) ?></p>
<?php endif; ?>
```

Lo mismo aplica para `descripcion`, `direccion` y `estado`.

## Docker

El proyecto se ejecuta con `docker-compose.yml`.

Servicios:

- `php`
- `mysql`
- `phpmyadmin`

Comandos útiles:

```bash
docker-compose up -d --build
docker-compose down -v
docker-compose exec -w /var/www/html php php src/database/migrate.php --seed
```

## Ejecutar sin Docker

Si prefieres correr el proyecto de forma manual, necesitas tener instalado lo siguiente:

- **PHP 8.0+**
- **Extensiones PHP**:
    - `pdo`
    - `pdo_mysql`
    - `mbstring`
    - `openssl`
    - `json`
- **MySQL 8.0+** o MariaDB
- **Apache 2.4** con `mod_rewrite` habilitado, o cualquier servidor web que apunte a `src/public`

### 1. Crear la base de datos

Primero crea una base de datos vacía, por ejemplo `tutecnico`.

Ejemplo desde MySQL:

```sql
CREATE DATABASE tutecnico CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'admin';
GRANT ALL PRIVILEGES ON tutecnico.* TO 'admin'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Configurar el archivo `.env`

Asegúrate de que las credenciales coincidan con tu entorno local:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=tutecnico
DB_USER=admin
DB_PASS=admin
DB_ROOT_PASS=root
```

### 3. Ejecutar migraciones y seeds

El proyecto incluye un script CLI en `src/database/migrate.php` que lee los archivos SQL y los ejecuta usando PDO.

Ejecuta:

```bash
php src/database/migrate.php --seed
```

Eso hace dos cosas:

- Ejecuta `src/database/migrations.sql` para crear la estructura de tablas.
- Si usas `--seed`, también ejecuta `src/database/seeds.sql` para cargar datos iniciales.

### 4. Levantar el proyecto localmente

Si usas Apache, apunta el document root a `src/public`.

Si usas el servidor embebido de PHP para pruebas rápidas:

```bash
php -S localhost:8000 -t src/public
```

Luego abre:

- `http://localhost:8000`

### 5. Verificar conexión a la base de datos

La conexión se define en `src/config/database.php` con `PDO`. Si falla, revisa:

- que `pdo_mysql` esté habilitado
- que el usuario y contraseña existan
- que la base de datos `tutecnico` ya esté creada
- que el host y puerto coincidan con tu servidor MySQL

## Base de datos

La conexión se configura en `src/config/database.php` con variables de entorno:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_ROOT_PASS`

## Mejoras pendientes

- Agregar login real para usar `AuthMiddleware`.
- Mover la gestión de errores de validación a un flujo más robusto si se quiere evitar depender de `HTTP_REFERER`.
- Añadir tests para `Router` y `Validator`.
- Estandarizar autoload con Composer si el proyecto crece.

## Notas

- El proyecto usa PHP nativo sin framework.
- La estructura está pensada para crecer sin perder simplicidad.
- La validación actual es centralizada y suficiente para formularios CRUD básicos.
