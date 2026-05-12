# TuTecnico

Aplicacion web en PHP con arquitectura MVC ligera para gestionar solicitudes de servicios tecnicos.

Este README no solo describe que archivos existen, tambien explica por que estan organizados asi, como se conectan entre si y como extender el sistema sin romper su flujo.

## Tabla de contenido

1. Vision general
2. Arquitectura y decisiones de diseno
3. Flujo completo de una peticion
4. Core del framework propio
5. Rutas, controladores, modelos y vistas
6. Autenticacion, autorizacion y middleware
7. Validacion, sanitizacion y errores
8. Base de datos y capa de datos
9. Estructura del proyecto
10. Puesta en marcha con Docker
11. Ejecucion sin Docker
12. Como extender el sistema
13. Limitaciones actuales y siguientes pasos

## Vision general

TuTecnico implementa una base MVC propia para tener control total del flujo HTTP con pocas dependencias.

El proyecto prioriza:

- Simplicidad operativa para aprendizaje y evolucion gradual.
- Separacion clara de responsabilidades entre rutas, controladores, modelos y vistas.
- Middleware por ruta para proteger endpoints sin acoplar toda la app a una sola regla global.
- Validacion centralizada con excepciones para mantener controladores mas limpios.

## Arquitectura y decisiones de diseno

### Por que una arquitectura MVC ligera propia

El proyecto evita un framework grande para:

- Entender el ciclo request-response de principio a fin.
- Poder modificar router, middleware y render sin abstracciones ocultas.
- Mantener un stack pequeno y facil de depurar.

### Principios aplicados

- Entry point unico en src/public/index.php.
- Router propio en src/core/Router.php.
- Controladores del dominio en src/controllers.
- Capa de datos con PDO en src/core/Model.php + src/config/database.php.
- Layout principal con vistas parciales en src/views/layouts.
- Middleware encadenado por ruta para auth y roles.

## Flujo completo de una peticion

```text
HTTP Request
    -> src/public/index.php
    -> carga clases base y session_start()
    -> src/routes/web.php registra rutas
    -> Router::dispatch()
    -> match de ruta y extraccion de parametros
    -> middleware (Auth/Admin/Technician segun ruta)
    -> metodo del controlador
    -> modelo(s) PDO
    -> Controller::render(view, data)
    -> views/layouts/app.php + contenido de vista
    -> Response
```

Detalles clave del bootstrap en src/public/index.php:

- Define BASE_PATH.
- Carga clases core y utilidades.
- Inicia sesion.
- Ejecuta dispatch del router.
- Captura ValidationException para retornar 422 JSON o redirigir con errores/old en sesion.
- Captura Throwable para responder 500 con ErrorController.

## Core del framework propio

### src/core/Controller.php

Que es:

- Clase base para todos los controladores.

Que resuelve:

- Metodo render(string $view, array $data = []) que centraliza el render de vistas.

Como funciona:

- Lee de sesion: errors, old y success.
- Limpia esos valores de sesion despues de leerlos.
- Hace extract($data) para exponer variables en la vista.
- Captura el HTML de la vista con output buffering.
- Inserta el contenido capturado dentro del layout maestro src/views/layouts/app.php.

Por que existe asi:

- Evita repetir la logica de render en cada controlador.
- Estandariza el paso de mensajes y errores en toda la UI.

### src/core/Model.php

Que es:

- Clase base para modelos.

Que resuelve:

- Metodo protected static db(): PDO que entrega la conexion compartida.

Como se conecta con el resto:

- Los modelos concretos (por ejemplo src/models/User.php o src/models/Ejemplo.php) extienden Model.
- Esos modelos usan self::db()->prepare(...) para consultas SQL.

Por que existe asi:

- Evita duplicar codigo de conexion en cada modelo.
- Mantiene la capa de acceso a datos uniforme y predecible.

### src/core/Router.php

Responsabilidades:

- Registrar rutas GET/POST/PUT/PATCH/DELETE.
- Soportar parametros dinamicos como :id, :id(int) y opcionales :id?.
- Resolver ruta actual y despachar accion Controller@method.
- Construir pipeline de middleware (global + por ruta).

Puntos tecnicos importantes:

- matchPath convierte la ruta declarada en regex y extrae parametros nombrados.
- createRunner usa array_reduce(array_reverse(...)) para encadenar middleware.
- Si la accion del controlador recibe parametros, se inyecta Request automaticamente.

### src/core/Request.php

Que representa:

- Abstraccion de la peticion HTTP para no depender directamente de superglobales en controladores.

Metodos relevantes:

- input(), all(), has()
- param(), params()
- json()
- isMethod(), isAjax()

### src/core/ValidationException.php

Que aporta:

- Excepcion de dominio para errores de validacion con estructura por campo.

Por que es util:

- Permite manejo centralizado en src/public/index.php en lugar de repetir if/else en cada controlador.

### src/core/MiddlewareInterface.php

Contrato comun:

- Todo middleware implementa handle(Request $request, callable $next).

Beneficio:

- El Router puede ejecutar cualquier middleware de forma consistente.

## Carpeta public

### Que es src/public

Esta carpeta es el document root real de la aplicacion. Todo lo que debe ser accesible desde el navegador tiene que vivir aqui o ser servido desde aqui.

Por que existe asi:

- Evita exponer el codigo interno de la app, como core, models, controllers o database.
- Reduce riesgo de acceso directo a archivos PHP internos.
- Hace que el servidor web apunte a una sola superficie publica controlada.

### Que debe ir aqui

- index.php como front controller publico.
- css/ para estilos globales y assets estaticos expuestos.
- js/ para scripts del cliente y comportamientos de interfaz.
- favicon/ para iconos del sitio y recursos publicos ligeros.

### Que no debe ir aqui

- Lógica de negocio.
- Consultas SQL.
- Clases de core, modelos o middleware.
- Vistas parciales que no formen parte de la interfaz servida por el navegador.

### Por que css, js y otros assets van ahi

- Porque el navegador necesita accederlos por URL publica.
- Porque el servidor web solo deberia exponer archivos estaticos o el front controller.
- Porque mantener los assets dentro de public simplifica cacheo, rutas relativas y despliegue.

### Regla practica para el equipo

- Si el archivo debe ser descargado por el navegador, va en public.
- Si el archivo solo lo usa PHP para ejecutar la app, va fuera de public.

## Rutas, controladores, modelos y vistas

### Rutas en src/routes/web.php

Las rutas se registran con la forma:

```php
$router->get('/ruta', 'Controlador@metodo', ['MiddlewareOpcional']);
```

La app actualmente cubre dominios:

- Home.
- Autenticacion (login, registro, logout).
- Dashboard por rol.
- Perfil y solicitud de tecnico.
- Administracion de estado tecnico.
- CRUD de ejemplo en solicitudes.

### Controladores en src/controllers

Controladores principales:

- AuthController: login, registro, logout y redireccion inteligente por rol.
- DashboardController: enruta a panel cliente/tecnico/admin segun rol y estado.
- ProfileController: ver/editar perfil y postular como tecnico.
- AdminController: revisar solicitudes tecnicas y cambiar estado.
- EjemploController: CRUD de solicitudes para referencia de implementacion.
- ErrorController: vistas de error 404 y 500.

Como se conectan con Controller:

- Todos extienden la clase base Controller.
- Llaman $this->render('ruta/de/vista', ['variables' => ...]).
- Al renderizar, automaticamente reciben acceso a $errors, $old y $success.

### Modelos en src/models

Modelo clave de dominio:

- User: autenticacion, gestion de rol, perfil tecnico, estadisticas admin y actualizacion de perfil.

### Por qué un solo `models/User.php` y qué responsabilidades comparte

Por el estado actual del proyecto existe un único modelo `src/models/User.php` que concentra las operaciones relacionadas con cuentas de usuario: autenticación, búsqueda por correo/id, creación de cuentas cliente, gestión de rol, lectura/escritura del perfil técnico y estadísticas administrativas.

Razonamiento actual:

- Dominio reducido y pragmático: al comienzo es más rápido y claro mantener toda la lógica de cuenta en un único lugar cuando el proyecto es pequeño.
- Evita duplicar queries y responsabilidades cuando varias partes de la app (panel, perfil y administración) necesitan acceder al mismo dato `users` o `tecnico_perfiles`.
- Facilita cambios rápidos durante la etapa de prototipo sin diseñar una capa de servicios/ repositorios compleja.

Qué responsabilidades tienen los controladores que comparten `User`:

- `DashboardController`:
    - Propósito: decidir y mostrar el panel correcto según `$_SESSION['role']` y el estado del perfil técnico.
    - Usa `User` para leer datos necesarios para render (por ejemplo `findById`, `getTechnicianProfileByUserId`).
    - No debería realizar mutaciones de negocio complejas; su responsabilidad es presentación y navegación.

- `ProfileController`:
    - Propósito: gestión del propio usuario (ver/editar perfil, postular como técnico).
    - Usa `User` para leer/actualizar el perfil y para crear/actualizar la fila en `tecnico_perfiles` (a través de métodos como `upsertTechnicianApplication`).
    - Es la capa que recibe input del usuario y delega la persistencia al modelo.

- `AdminController`:
    - Propósito: acciones administrativas (listar solicitudes de técnicos, aprobar/rechazar, estadísticas).
    - Usa `User` para leer aplicaciones y para mutaciones transaccionales (`setTechnicianStatus`, `updateRole`).
    - Es la capa que realiza cambios de estado que afectan a otros actores del sistema.

Por qué están separados `DashboardController`, `ProfileController` y `AdminController`:

- Principio de responsabilidad única: cada controlador sirve un rol distinto (presentación, acciones del usuario, administración).
- Permisos y seguridad: `AdminController` está protegido por `AdminMiddleware`; separar facilita auditar y aplicar reglas de acceso.
- Mantenibilidad: dividir la UI (dashboard) de operaciones mutantes (admin) hace que las pruebas y la lectura sean más directas.

Cuándo considerar cambiar la organizacion (recomendaciones futuras):

- Si `User` crece mucho (muchos métodos y responsabilidades), extraer:
    - `TechnicianProfile` (nuevo modelo) para encapsular `tecnico_perfiles` y su lógica.
    - `UserService` o `UserRepository` para coordinar operaciones complejas que involucren varias tablas.
- Extraer las operaciones administrativas a servicios (`AdminService`) si las acciones empiezan a involucrar lógica de negocio compleja o integraciones externas.
- Si el area admin crece, trasladar controladores a un namespace/prefijo `src/controllers/admin/` y agrupar rutas con `/dashboard/admin/*`.

Guía práctica para desarrolladores nuevos:

- Antes de añadir un nuevo método a `User`, pregúntate si pertenece al concepto "usuario" (credenciales, perfil, rol) o si enlaza otra entidad (por ejemplo perfil técnico). Si es lo segundo, considera crear `TechnicianProfile`.
- Mantén las mutaciones (writes) en `AdminController` o en servicios, no en vistas ni en `DashboardController`.
- Documenta en un comentario al inicio del archivo `src/models/User.php` qué operaciones contiene y por qué; esto facilita futuras refactorizaciones.


Caracteristicas actuales:

- PDO con consultas preparadas.
- Uso de transacciones en operaciones sensibles, por ejemplo setTechnicianStatus.
- Acople consciente al esquema SQL (no ORM), lo cual simplifica depuracion y control de queries.

### Render de vistas en src/views

Estrategia de render:

- Vista concreta (por ejemplo dashboard/client/index.php) genera contenido.
- Ese contenido entra en src/views/layouts/app.php mediante $content.
- app.php incluye nav.php y footer.php.

Esto da consistencia visual y evita duplicar estructura HTML en cada vista.

## Autenticacion, autorizacion y middleware

### AuthController + User

Flujo de login:

1. Valida entrada con Validator.
2. Busca usuario por correo (User::findByEmail).
3. Verifica password con password_verify.
4. Verifica cuenta activa.
5. Regenera session id y guarda user_id, role y user_name.
6. Redirige por rol y estado tecnico.

Flujo de registro:

1. Valida campos.
2. Verifica coincidencia de contrasenas.
3. Verifica correo no duplicado.
4. Crea cliente con hash bcrypt.
5. Inicia sesion y redirige al dashboard cliente.

### Middleware por ruta

Archivos:

- src/middleware/AuthMiddleware.php
- src/middleware/RoleMiddleware.php
- src/middleware/AdminMiddleware.php
- src/middleware/TechnicianMiddleware.php

Funcionamiento:

- AuthMiddleware protege rutas autenticadas, sincroniza sesion con datos reales de DB y guarda intended_url para retorno post-login.
- RoleMiddleware::ensure($rol) bloquea acceso si el rol no coincide.
- AdminMiddleware y TechnicianMiddleware delegan en RoleMiddleware.

Por que esta estrategia:

- El control de acceso queda declarativo en cada ruta.
- Se evita duplicar validaciones de rol dentro de cada controlador.

### Que es un middleware

Un middleware es una capa intermedia entre la peticion HTTP y el controlador final.

Su trabajo es decidir una de estas cosas:

- Dejar pasar la peticion al siguiente paso.
- Bloquearla con una redireccion o un codigo de error.
- Preparar contexto adicional antes de llegar al controlador.

En este proyecto los middleware se usan para reglas transversales, no para logica de negocio.

### Por que tenemos los middleware que tenemos

- AuthMiddleware existe para asegurar que las rutas protegidas solo se ejecuten con una sesion valida y sincronizada con la base de datos.
- RoleMiddleware existe para aplicar una verificacion simple y reutilizable de rol sin repetir la misma condicion en varios controladores.
- AdminMiddleware existe para hacer explicita la proteccion de zonas administrativas.
- TechnicianMiddleware existe para separar acceso tecnico de acceso de cliente y mantener legible el enrutado por rol.

### Regla de uso para nuevos middleware

- Usalo solo si la validacion aplica a varias rutas.
- No metas consultas de negocio complejas si solo proteges acceso.
- Si una regla solo afecta a una accion puntual, probablemente pertenece al controlador o al modelo, no al middleware.

## Validacion, sanitizacion y errores

### Validator

Ubicacion: src/validators/Validator.php

Reglas implementadas:

- required
- min:N
- max:N
- integer
- email
- in:a,b,c
- regex:/.../

Si hay errores, lanza ValidationException con errores por campo.

### Sanitizacion

Ubicacion: src/helpers/sanitize.php

- sanitize_array aplica trim a entradas antes de validar/guardar.

### Manejo de errores

Errores de validacion:

- Se capturan globalmente en src/public/index.php.
- Si la request es AJAX, responde 422 JSON.
- Si no es AJAX, guarda errors y old en sesion y redirige al formulario previo.

Errores no controlados:

- Se captura Throwable.
- Se registra en error_log.
- Se responde 500 renderizando ErrorController::serverError().

## Base de datos y capa de datos

### Conexion

Archivo: src/config/database.php

- Implementa Database::getInstance() (Singleton PDO).
- Lee variables de entorno: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS.
- Usa utf8mb4 y opciones PDO seguras basicas (ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false).

### Esquema

Archivo: src/database/migrations.sql

Entidades principales:

- users
- tecnico_perfiles
- categorias
- tecnico_categorias
- solicitudes
- mensajes
- cotizaciones
- foto_trabajos
- calificaciones
- notificaciones

### Datos iniciales

Archivo: src/database/seeds.sql

- Crea usuarios de prueba por rol.
- Crea perfiles tecnicos de ejemplo.
- Crea una solicitud, una cotizacion y mensajes de ejemplo.

Credenciales de prueba relevantes:

- admin@servicios.com / password
- carlos@mail.com / password
- maria@mail.com / password
- luis@mail.com / password
- roberto@mail.com / password
- ana@mail.com / password

## Estructura del proyecto

```text
docker-compose.yml
docker/
    Dockerfile
src/
    config/
        database.php
    controllers/
        AdminController.php
        AuthController.php
        DashboardController.php
        EjemploController.php
        ErrorController.php
        HomeController.php
        ProfileController.php
    core/
        Controller.php
        MiddlewareInterface.php
        Model.php
        Request.php
        Router.php
        ValidationException.php
    database/
        migrate.php
        migrations.sql
        seeds.sql
    helpers/
        sanitize.php
    middleware/
        AdminMiddleware.php
        AuthMiddleware.php
        RoleMiddleware.php
        TechnicianMiddleware.php
    models/
        Ejemplo.php
        Home.php
        User.php
    public/
        index.php
        css/
            app.css
    routes/
        web.php
    validators/
        Validator.php
    views/
        auth/
        dashboard/
        ejemplo/
        errors/
        home/
        layouts/
        profile/
```

## Puesta en marcha con Docker

### Requisitos

- Docker
- Docker Compose

### 1) Crear .env en la raiz del proyecto

Ejemplo minimo:

```env
DB_HOST=mysql
DB_PORT=3306
DB_NAME=tutecnico
DB_USER=admin
DB_PASS=admin
DB_ROOT_PASS=root
```

### 2) Levantar servicios

```bash
docker-compose up -d --build
```

Servicios expuestos:

- App PHP/Apache: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- MySQL: 3306

### 3) Ejecutar migraciones y seed

```bash
docker-compose exec php php src/database/migrate.php --seed
```

Notas:

- El migrador lee .env manualmente.
- Si no usas --seed, solo aplica migrations.sql.

### 4) Apagar entorno

```bash
docker-compose down
```

Para eliminar volumen de DB:

```bash
docker-compose down -v
```

## Ejecucion sin Docker

Requisitos minimos:

- PHP 8.x
- Extensiones: pdo, pdo_mysql, mbstring
- MySQL 8.x
- Servidor web apuntando a src/public, o servidor embebido de PHP

Pasos:

1. Crear base de datos y usuario en MySQL.
2. Configurar variables de entorno (o .env para migrate.php).
3. Ejecutar:

```bash
php src/database/migrate.php --seed
```

4. Iniciar server local:

```bash
php -S localhost:8000 -t src/public
```

5. Abrir http://localhost:8000

## Como extender el sistema

### Agregar una nueva pagina MVC

1. Crear ruta en src/routes/web.php.
2. Crear metodo en controlador (extiende Controller).
3. Crear vista en src/views/<modulo>/... .php.
4. Si requiere DB, crear/actualizar modelo que extienda Model.
5. Agregar validacion con Validator::validate.

Ejemplo de ruta:

```php
$router->get('/tickets', 'TicketController@index', ['AuthMiddleware']);
```

### Agregar nuevo middleware

1. Crear clase en src/middleware que implemente MiddlewareInterface.
2. Implementar handle(Request $request, callable $next).
3. Adjuntarlo en rutas donde aplique.

Ejemplo:

```php
class BillingMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        // validacion previa
        return $next($request);
    }
}
```

### Agregar validaciones nuevas

1. Extender switch de reglas en src/validators/Validator.php.
2. Mantener mensajes consistentes por campo.
3. Evitar logica de negocio compleja dentro del validador.

## Convenciones de arquitectura

### Donde va cada cosa

- src/public: solo lo publico para el navegador.
- src/controllers: orquestacion de casos de uso y respuesta HTTP.
- src/models: acceso a datos y consultas.
- src/views: presentacion.
- src/core: primitivas compartidas del framework.
- src/middleware: reglas transversales de acceso o contexto.
- src/helpers: utilidades pequenas y reutilizables.

### Que no deberia pasar

- No poner SQL dentro de vistas.
- No renderizar HTML desde modelos.
- No poner logica de autorizacion repetida dentro de cada controlador.
- No guardar assets ejecutables o scripts internos fuera de su carpeta correspondiente.
- No mezclar la logica de render con la de negocio si puede evitarse.

### Regla mental para respetar la arquitectura

- Si responde al navegador, va en controlador o vista.
- Si consulta o modifica datos, va en modelo.
- Si valida acceso o contexto transversal, va en middleware.
- Si solo ayuda a reutilizar una tarea pequena, va en helper o core segun su alcance.

## Limitaciones actuales y siguientes pasos

Limitaciones conocidas:

- No hay CSRF token en formularios.
- No hay suite de tests automatizados.
- Seeds no son idempotentes (si los ejecutas multiples veces pueden fallar por duplicados).
- Parte del flujo depende de estado de sesion y redirecciones clasicas.

Mejoras sugeridas:

- Agregar CSRF y politicas de seguridad HTTP.
- Introducir pruebas para Router, Validator y Auth flow.
- Estandarizar respuestas JSON para endpoints AJAX.
- Incorporar auditoria/log estructurado para cambios admin.

## Resumen rapido

- src/core/Controller.php controla el render y conecta controladores con vistas/layout.
- src/core/Model.php conecta modelos con la instancia PDO compartida.
- src/core/Router.php conecta rutas con controladores y middleware.
- src/core/Request.php conecta la peticion HTTP con acciones del controlador de forma limpia.

Con estas piezas, el proyecto mantiene un MVC simple, entendible y listo para crecer por modulos.
