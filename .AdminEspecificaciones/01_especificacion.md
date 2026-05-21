# Especificación: Filtros y Búsqueda del Panel Admin

> Iteración 2 del panel admin. Ver `00_especificacion.md` para la base del panel.

---

## Problema Resuelto

La búsqueda de usuarios daba un **error 500** porque PDO con `ATTR_EMULATE_PREPARES = false` no permite usar el mismo named placeholder dos veces en una query.

**Query rota:**
```sql
WHERE (nombre LIKE :search OR correo LIKE :search)
-- ❌ :search aparece dos veces → PDOException
```

**Fix aplicado:**
```sql
WHERE (nombre LIKE :search_nombre OR correo LIKE :search_correo)
-- ✅ placeholders únicos, dos bindings en $params
```

---

## Cambios Implementados

### `src/models/User.php`

**1. `listAllUsers()`** — Fix placeholder duplicado:
```php
$where[]                 = '(nombre LIKE :search_nombre OR correo LIKE :search_correo)';
$params['search_nombre'] = '%' . $search . '%';
$params['search_correo'] = '%' . $search . '%';
```

**2. `listTechnicianApplications()`** — Nuevos parámetros:
```php
public static function listTechnicianApplications(
    string $search = '',   // busca en u.nombre y u.correo
    string $estado = '',   // filtra por tp.estado (ENUM exacto)
    string $zona   = ''    // LIKE sobre tp.zona_cobertura
): array
```
Construye WHERE dinámico con array `$where` + `$params`, igual que `listAllUsers()`.

### `src/models/Solicitud.php`

**`listAll()`** — Nuevos parámetros:
```php
public static function listAll(
    string $search = '',   // busca en s.titulo y c.nombre (cliente)
    string $estado = ''    // filtra por s.estado (ENUM exacto)
): array
```
Usa `s.` y `c.` como alias (JOINs ya existentes), construye WHERE dinámico.

### `src/controllers/AdminController.php`

**`tecnicos()`** — Lee GET params y los pasa al modelo:
```php
$search = trim($_GET['q']    ?? '');
$zona   = trim($_GET['zona'] ?? '');
$estadosValidos = ['pendiente', 'activo', 'suspendido', 'rechazado'];
$estado = in_array($_GET['estado'] ?? '', $estadosValidos) ? $_GET['estado'] : '';
$applications = User::listTechnicianApplications($search, $estado, $zona);
// Pasa $search, $estadoFilter, $zonaFilter a la vista
```

**`solicitudes()`** — Lee GET params y los pasa al modelo:
```php
$search = trim($_GET['q'] ?? '');
$estadosValidos = ['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada'];
$estado = in_array($_GET['estado'] ?? '', $estadosValidos) ? $_GET['estado'] : '';
$solicitudes = Solicitud::listAll($search, $estado);
// Pasa $search, $estadoFilter a la vista
```

> Los valores de estado se validan con `in_array` — valores inválidos en la URL se tratan como vacío (sin filtro). Previene inyección de valores arbitrarios al SQL.

### `src/views/dashboard/admin/tecnicos/index.php`

Nueva barra de filtros (GET form) con:
- `<select name="estado">` — Todos / Pendiente / Aprobado / Suspendido / Rechazado
- `<input name="zona">` — texto libre, LIKE sobre zona_cobertura
- `<input name="q">` — búsqueda por nombre/correo del técnico
- Botón "Buscar"
- Link "Limpiar" (visible solo cuando hay algún filtro activo)

### `src/views/dashboard/admin/solicitudes/index.php`

Nueva barra de filtros (GET form) con:
- `<select name="estado">` — Todos / Pendiente / Aceptada / En progreso / Completada / Cancelada
- `<input name="q">` — búsqueda por título o nombre del cliente
- Botón "Buscar"
- Link "Limpiar" (visible solo cuando hay algún filtro activo)

---

## Comportamiento de Filtros

### Usuarios (`/dashboard/admin/usuarios`)
| Param GET | Comportamiento |
|-----------|---------------|
| `q=texto` | Busca en `nombre` y `correo` (LIKE `%texto%`) |
| `estado=activo` | Solo usuarios con `activo = 1` |
| `estado=inactivo` | Solo usuarios con `activo = 0` |
| Sin params | Lista completa |

### Técnicos (`/dashboard/admin/tecnicos`)
| Param GET | Comportamiento |
|-----------|---------------|
| `q=texto` | Busca en `u.nombre` y `u.correo` |
| `estado=pendiente\|activo\|suspendido\|rechazado` | Filtra por `tp.estado` exacto |
| `zona=texto` | LIKE sobre `tp.zona_cobertura` |
| Combinados | Intersección (AND) |
| Valor inválido en `estado` | Ignorado — muestra todos |

### Solicitudes (`/dashboard/admin/solicitudes`)
| Param GET | Comportamiento |
|-----------|---------------|
| `q=texto` | Busca en `s.titulo` y `c.nombre` (cliente) |
| `estado=pendiente\|aceptada\|en_progreso\|completada\|cancelada` | Filtra por `s.estado` exacto |
| Combinados | Intersección (AND) |
| Valor inválido en `estado` | Ignorado — muestra todos |

---

## Nota sobre `zona_cobertura`

El campo `zona_cobertura` en `tecnico_perfiles` es texto libre (ej: `"San Salvador, Soyapango, Ilopango"`). El filtro de zona usa `LIKE %texto%`, lo que permite buscar por cualquier ciudad o departamento que aparezca en el campo. No existe un campo estructurado de departamento en el schema actual.
