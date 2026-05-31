<?php

class User extends Model
{
    public static function findByEmail(string $email): array|false
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $email]);
        return $stmt->fetch();
    }

    public static function findById(int $id): array|false
    {
        $stmt = self::db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function existsByEmail(string $email): bool
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) AS total FROM users WHERE correo = :correo');
        $stmt->execute(['correo' => $email]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0) > 0;
    }

    public static function createClient(array $data): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO users (nombre, correo, contrasena, telefono, rol, activo)
             VALUES (:nombre, :correo, :contrasena, :telefono, :rol, :activo)'
        );

        $stmt->execute([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'contrasena' => $data['contrasena'],
            'telefono' => $data['telefono'] ?? null,
            'rol' => 'cliente',
            'activo' => 1,
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function getTechnicianProfileByUserId(int $userId): array|false
    {
        $stmt = self::db()->prepare('SELECT * FROM tecnico_perfiles WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }

    public static function upsertTechnicianApplication(int $userId, array $data): void
    {
        $profile = self::getTechnicianProfileByUserId($userId);

        if ($profile) {
            $stmt = self::db()->prepare(
                'UPDATE tecnico_perfiles
                 SET zona_cobertura = :zona_cobertura,
                     descripcion = :descripcion,
                     documentos_verificacion = :documentos_verificacion,
                     disponibilidad = 1,
                     estado = :estado,
                     comentario_admin = :comentario_admin,
                     fecha_estado_cambio = CURRENT_TIMESTAMP
                 WHERE user_id = :user_id'
            );

            $stmt->execute([
                'user_id' => $userId,
                'zona_cobertura' => $data['zona_cobertura'],
                'descripcion' => $data['descripcion'],
                'documentos_verificacion' => $data['documentos_verificacion'],
                'estado' => 'pendiente',
                'comentario_admin' => 'Solicitud actualizada por usuario',
            ]);
            return;
        }

        $stmt = self::db()->prepare(
            'INSERT INTO tecnico_perfiles
             (user_id, zona_cobertura, descripcion, documentos_verificacion, disponibilidad, estado, comentario_admin)
             VALUES (:user_id, :zona_cobertura, :descripcion, :documentos_verificacion, :disponibilidad, :estado, :comentario_admin)'
        );

        $stmt->execute([
            'user_id' => $userId,
            'zona_cobertura' => $data['zona_cobertura'],
            'descripcion' => $data['descripcion'],
            'documentos_verificacion' => $data['documentos_verificacion'],
            'disponibilidad' => 1,
            'estado' => 'pendiente',
            'comentario_admin' => 'Pendiente de validacion',
        ]);
    }

    public static function listTechnicianApplications(
        string $search = '',
        string $estado = '',
        string $zona   = ''
    ): array {
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]                 = '(u.nombre LIKE :search_nombre OR u.correo LIKE :search_correo)';
            $params['search_nombre'] = '%' . $search . '%';
            $params['search_correo'] = '%' . $search . '%';
        }
        if ($estado !== '') {
            $where[]          = 'tp.estado = :estado';
            $params['estado'] = $estado;
        }
        if ($zona !== '') {
            $where[]        = 'tp.zona_cobertura LIKE :zona';
            $params['zona'] = '%' . $zona . '%';
        }

        $sql = 'SELECT tp.*, u.nombre, u.correo, u.telefono, u.rol, u.activo
                FROM tecnico_perfiles tp
                JOIN users u ON u.id = tp.user_id';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY tp.fecha_estado_cambio DESC, tp.id DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function setTechnicianStatus(int $userId, string $status, ?string $comment): void
    {
        $pdo = self::db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'UPDATE tecnico_perfiles
                 SET estado = :estado,
                     comentario_admin = :comentario_admin,
                     fecha_estado_cambio = CURRENT_TIMESTAMP
                 WHERE user_id = :user_id'
            );

            $stmt->execute([
                'user_id' => $userId,
                'estado' => $status,
                'comentario_admin' => $comment,
            ]);

            if ($status === 'activo' || $status === 'suspendido') {
                self::updateRole($userId, 'tecnico');
            } elseif ($status === 'pendiente' || $status === 'rechazado') {
                self::updateRole($userId, 'cliente');
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function updateRole(int $userId, string $role): void
    {
        $stmt = self::db()->prepare('UPDATE users SET rol = :rol WHERE id = :id');
        $stmt->execute([
            'id' => $userId,
            'rol' => $role,
        ]);
    }

    public static function countByRole(string $role): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) AS total FROM users WHERE rol = :rol');
        $stmt->execute(['rol' => $role]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public static function updateUser(int $userId, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE users
            SET nombre    = :nombre,
                correo    = :correo,
                telefono  = :telefono,
                direccion = :direccion,
                ciudad    = :ciudad,
                pais      = :pais,
                foto_perfil = :foto_perfil
            WHERE id = :id'
        );

        $stmt->execute([
            'id'        => $userId,
            'nombre'    => $data['nombre'],
            'correo'    => $data['correo'],
            'telefono'  => $data['telefono']  ?? null,
            'direccion' => $data['direccion'] ?? null,
            'ciudad'    => $data['ciudad']    ?? null,
            'pais'      => $data['pais']      ?? 'El Salvador',
            'foto_perfil' => $data['foto_perfil'] ?? null,
        ]);
    }

    // Categorías
    public static function getAllCategorias(): array
    {
        $stmt = self::db()->query('SELECT * FROM categorias ORDER BY nombre ASC');
        return $stmt->fetchAll();
    }

    public static function getCategoriasbyTecnico(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT id_categoria FROM tecnico_categorias WHERE user_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);
        return array_column($stmt->fetchAll(), 'id_categoria');
    }

    public static function syncCategoriasTecnico(int $userId, array $categoriaIds): void
    {
        $pdo = self::db();
        $pdo->prepare('DELETE FROM tecnico_categorias WHERE user_id = :user_id')
            ->execute(['user_id' => $userId]);

        if (empty($categoriaIds)) return;

        $stmt = $pdo->prepare(
            'INSERT INTO tecnico_categorias (user_id, id_categoria) VALUES (:user_id, :id_categoria)'
        );
        foreach ($categoriaIds as $catId) {
            $stmt->execute(['user_id' => $userId, 'id_categoria' => (int) $catId]);
        }
    }

    // Fotos de trabajos
    public static function getFotosTrabajo(int $tecnicoPerfilId): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM foto_trabajos WHERE tecnico_perfil_id = :id ORDER BY fecha DESC'
        );
        $stmt->execute(['id' => $tecnicoPerfilId]);
        return $stmt->fetchAll();
    }

    public static function addFotoTrabajo(int $tecnicoPerfilId, string $url, ?string $descripcion): void
    {
        $stmt = self::db()->prepare(
            'INSERT INTO foto_trabajos (tecnico_perfil_id, url, descripcion)
            VALUES (:tecnico_perfil_id, :url, :descripcion)'
        );
        $stmt->execute([
            'tecnico_perfil_id' => $tecnicoPerfilId,
            'url'               => $url,
            'descripcion'       => $descripcion,
        ]);
    }

    public static function deleteFotoTrabajo(int $fotoId, int $tecnicoPerfilId): void
    {
        $stmt = self::db()->prepare(
            'DELETE FROM foto_trabajos WHERE id = :id AND tecnico_perfil_id = :tecnico_perfil_id'
        );
        $stmt->execute(['id' => $fotoId, 'tecnico_perfil_id' => $tecnicoPerfilId]);
    }

    // actualizar tec
    public static function updateTechnicianProfile(int $userId, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE tecnico_perfiles
            SET descripcion    = :descripcion,
                zona_cobertura = :zona_cobertura
            WHERE user_id = :user_id'
        );
        $stmt->execute([
            'user_id'        => $userId,
            'descripcion'    => $data['descripcion'],
            'zona_cobertura' => $data['zona_cobertura'],
        ]);
    }

    public static function listAllUsers(string $search = '', string $status = ''): array
    {
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]                 = '(nombre LIKE :search_nombre OR correo LIKE :search_correo)';
            $params['search_nombre'] = '%' . $search . '%';
            $params['search_correo'] = '%' . $search . '%';
        }
        if ($status === 'activo') {
            $where[] = 'activo = 1';
        } elseif ($status === 'inactivo') {
            $where[] = 'activo = 0';
        }

        $sql = 'SELECT id, nombre, correo, telefono, rol, activo, fecha_registro FROM users';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY fecha_registro DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function toggleActive(int $userId): void
    {
        $stmt = self::db()->prepare(
            'UPDATE users SET activo = IF(activo = 1, 0, 1) WHERE id = :id'
        );
        $stmt->execute(['id' => $userId]);
    }

    public static function deleteUser(int $userId): void
    {
        $stmt = self::db()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    // --- Perfil público del técnico ---

    public static function getTechnicianPublicProfile(int $userId): array|false
    {
        $stmt = self::db()->prepare(
            'SELECT u.id, u.nombre, u.correo, u.telefono, u.foto_perfil, u.ciudad, u.pais,
                    tp.id AS perfil_id, tp.zona_cobertura, tp.descripcion, tp.disponibilidad,
                    tp.estado, tp.cancelaciones
             FROM users u
             JOIN tecnico_perfiles tp ON tp.user_id = u.id
             WHERE u.id = :id AND tp.estado = \'activo\'
             LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    }

    public static function getTechnicianCategoriasConNombres(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT c.id, c.nombre FROM categorias c
             JOIN tecnico_categorias tc ON tc.id_categoria = c.id
             WHERE tc.user_id = :user_id
             ORDER BY c.nombre ASC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function getFotosTrabajoPorUserId(int $userId): array
    {
        $profile = self::getTechnicianProfileByUserId($userId);
        if (!$profile) return [];
        return self::getFotosTrabajo((int) $profile['id']);
    }

    public static function getResenasByTecnico(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT cal.id AS calificacion_id, cal.puntuacion, cal.comentario, cal.fecha,
                    s.id_cliente,
                    u.nombre AS cliente_nombre, u.foto_perfil AS cliente_foto
             FROM calificaciones cal
             JOIN solicitudes s ON s.id = cal.id_solicitud
             JOIN users u ON u.id = s.id_cliente
             WHERE s.id_tecnico = :tecnico_id
             ORDER BY cal.fecha DESC'
        );
        $stmt->execute(['tecnico_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function getResenaByClient(int $clienteId, int $tecnicoId): array|false
    {
        $stmt = self::db()->prepare(
            'SELECT cal.id, cal.puntuacion, cal.comentario
             FROM calificaciones cal
             JOIN solicitudes s ON s.id = cal.id_solicitud
             WHERE s.id_cliente = :cliente AND s.id_tecnico = :tecnico
             LIMIT 1'
        );
        $stmt->execute(['cliente' => $clienteId, 'tecnico' => $tecnicoId]);
        return $stmt->fetch();
    }

    public static function updateResena(int $calificacionId, int $clienteId, int $tecnicoId, int $puntuacion, ?string $comentario): void
    {
        $stmt = self::db()->prepare(
            'UPDATE calificaciones
             SET puntuacion = :puntuacion, comentario = :comentario
             WHERE id = :id
               AND id_solicitud IN (
                   SELECT id FROM solicitudes
                   WHERE id_cliente = :cliente AND id_tecnico = :tecnico
               )'
        );
        $stmt->execute([
            'id'          => $calificacionId,
            'puntuacion'  => $puntuacion,
            'comentario'  => $comentario,
            'cliente'     => $clienteId,
            'tecnico'     => $tecnicoId,
        ]);
    }

    public static function deleteResena(int $calificacionId, int $clienteId, int $tecnicoId): void
    {
        $stmt = self::db()->prepare(
            'DELETE FROM calificaciones
             WHERE id = :id
               AND id_solicitud IN (
                   SELECT id FROM solicitudes
                   WHERE id_cliente = :cliente AND id_tecnico = :tecnico
               )'
        );
        $stmt->execute([
            'id'      => $calificacionId,
            'cliente' => $clienteId,
            'tecnico' => $tecnicoId,
        ]);
    }

    public static function getAvgRating(int $userId): float
    {
        $stmt = self::db()->prepare(
            'SELECT AVG(cal.puntuacion) AS promedio
             FROM calificaciones cal
             JOIN solicitudes s ON s.id = cal.id_solicitud
             WHERE s.id_tecnico = :tecnico_id'
        );
        $stmt->execute(['tecnico_id' => $userId]);
        $row = $stmt->fetch();
        return round((float) ($row['promedio'] ?? 0), 1);
    }

    public static function countCompletedServices(int $userId): int
    {
        $stmt = self::db()->prepare(
            'SELECT COUNT(*) AS total FROM solicitudes
             WHERE id_tecnico = :id AND estado = \'completada\''
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public static function getUnreviewedSolicitudForClient(int $clienteId, int $tecnicoId): array|false
    {
        $stmt = self::db()->prepare(
            'SELECT s.id FROM solicitudes s
             LEFT JOIN calificaciones cal ON cal.id_solicitud = s.id
             WHERE s.id_cliente = :cliente_id
               AND s.id_tecnico = :tecnico_id
               AND s.estado = \'completada\'
               AND cal.id IS NULL
             LIMIT 1'
        );
        $stmt->execute(['cliente_id' => $clienteId, 'tecnico_id' => $tecnicoId]);
        return $stmt->fetch();
    }

    public static function createResena(int $solicitudId, int $puntuacion, ?string $comentario): void
    {
        $stmt = self::db()->prepare(
            'INSERT INTO calificaciones (id_solicitud, puntuacion, comentario)
             VALUES (:id_solicitud, :puntuacion, :comentario)'
        );
        $stmt->execute([
            'id_solicitud' => $solicitudId,
            'puntuacion'   => $puntuacion,
            'comentario'   => $comentario,
        ]);
    }

    public static function hasCompletedServiceWith(int $clienteId, int $tecnicoId): bool
    {
        $stmt = self::db()->prepare(
            'SELECT COUNT(*) AS total FROM solicitudes
             WHERE id_cliente = :cliente AND id_tecnico = :tecnico AND estado = \'completada\''
        );
        $stmt->execute(['cliente' => $clienteId, 'tecnico' => $tecnicoId]);
        $row = $stmt->fetch();
        return ((int) ($row['total'] ?? 0)) > 0;
    }

    public static function verifySolicitudOwnership(int $solicitudId, int $clienteId, int $tecnicoId): bool
    {
        $stmt = self::db()->prepare(
            'SELECT id FROM solicitudes
             WHERE id = :id AND id_cliente = :cliente AND id_tecnico = :tecnico AND estado = \'completada\'
             LIMIT 1'
        );
        $stmt->execute(['id' => $solicitudId, 'cliente' => $clienteId, 'tecnico' => $tecnicoId]);
        return (bool) $stmt->fetch();
    }

    public static function adminUpdateUser(int $userId, array $data): void
    {
        $stmt = self::db()->prepare(
            'UPDATE users
             SET nombre   = :nombre,
                 correo   = :correo,
                 telefono = :telefono,
                 rol      = :rol
             WHERE id = :id'
        );

        $stmt->execute([
            'id'       => $userId,
            'nombre'   => $data['nombre'],
            'correo'   => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'rol'      => $data['rol'],
        ]);
    }

    public static function getTopTechnicians(int $limit = 3): array
    {
        $stmt = self::db()->prepare(
            'SELECT u.id, u.nombre, u.foto_perfil, tp.disponibilidad,
                    COALESCE(ROUND(AVG(cal.puntuacion), 1), 0) AS avg_rating,
                    COUNT(DISTINCT s.id) AS service_count,
                    GROUP_CONCAT(DISTINCT cat.nombre ORDER BY cat.nombre SEPARATOR \', \') AS categorias
             FROM users u
             JOIN tecnico_perfiles tp ON tp.user_id = u.id
             LEFT JOIN tecnico_categorias tc ON tc.user_id = u.id
             LEFT JOIN categorias cat ON cat.id = tc.id_categoria
             LEFT JOIN solicitudes s ON s.id_tecnico = u.id AND s.estado = \'completada\'
             LEFT JOIN calificaciones cal ON cal.id_solicitud = s.id
             WHERE u.rol = \'tecnico\' AND tp.estado = \'activo\' AND u.activo = 1
             GROUP BY u.id, u.nombre, u.foto_perfil, tp.disponibilidad
             ORDER BY avg_rating DESC, service_count DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getLatestReviews(int $limit = 3): array
    {
        $stmt = self::db()->prepare(
            'SELECT cal.puntuacion, cal.comentario,
                    u_c.nombre AS cliente_nombre, u_c.foto_perfil AS cliente_foto,
                    u_t.nombre AS tecnico_nombre
             FROM calificaciones cal
             JOIN solicitudes s ON s.id = cal.id_solicitud
             JOIN users u_c ON u_c.id = s.id_cliente
             JOIN users u_t ON u_t.id = s.id_tecnico
             WHERE cal.comentario IS NOT NULL AND cal.comentario != \'\'
             ORDER BY cal.fecha DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // --- Búsqueda pública de técnicos ---

    /**
     * Construye las cláusulas WHERE/HAVING y los binds a partir de los filtros.
     * Como las prepared statements no usan emulación, cada placeholder es único.
     *
     * @return array{0: string[], 1: string[], 2: array<string, array{0: mixed, 1: int}>}
     */
    private static function buildTechnicianSearch(array $f): array
    {
        $where  = ["u.rol = 'tecnico'", "tp.estado = 'activo'", 'u.activo = 1'];
        $having = [];
        $binds  = [];

        $q = trim((string) ($f['q'] ?? ''));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $where[] = '(u.nombre LIKE :q1
                         OR tp.zona_cobertura LIKE :q2
                         OR u.ciudad LIKE :q3
                         OR EXISTS (SELECT 1 FROM tecnico_categorias tcs
                                    JOIN categorias cs ON cs.id = tcs.id_categoria
                                    WHERE tcs.user_id = u.id AND cs.nombre LIKE :q4))';
            $binds[':q1'] = [$like, \PDO::PARAM_STR];
            $binds[':q2'] = [$like, \PDO::PARAM_STR];
            $binds[':q3'] = [$like, \PDO::PARAM_STR];
            $binds[':q4'] = [$like, \PDO::PARAM_STR];
        }

        $cats = array_values(array_filter(array_map('intval', (array) ($f['categorias'] ?? []))));
        if ($cats) {
            $placeholders = [];
            foreach ($cats as $i => $catId) {
                $key                  = ':cat' . $i;
                $placeholders[]       = $key;
                $binds[$key]          = [$catId, \PDO::PARAM_INT];
            }
            $where[] = 'EXISTS (SELECT 1 FROM tecnico_categorias tcf
                                WHERE tcf.user_id = u.id
                                  AND tcf.id_categoria IN (' . implode(', ', $placeholders) . '))';
        }

        $zona = trim((string) ($f['zona'] ?? ''));
        if ($zona !== '') {
            $where[]         = '(tp.zona_cobertura LIKE :zona1 OR u.ciudad LIKE :zona2)';
            $binds[':zona1'] = ['%' . $zona . '%', \PDO::PARAM_STR];
            $binds[':zona2'] = ['%' . $zona . '%', \PDO::PARAM_STR];
        }

        if (!empty($f['disponible'])) {
            $where[] = 'tp.disponibilidad = 1';
        }

        $minRating = (float) ($f['min_rating'] ?? 0);
        if ($minRating > 0) {
            $having[]             = 'avg_rating >= :min_rating';
            $binds[':min_rating'] = [$minRating, \PDO::PARAM_STR];
        }

        return [$where, $having, $binds];
    }

    public static function searchTechnicians(array $f, int $limit, int $offset): array
    {
        [$where, $having, $binds] = self::buildTechnicianSearch($f);

        $orderMap = [
            'rating'    => 'avg_rating DESC, review_count DESC, service_count DESC',
            'servicios' => 'service_count DESC, avg_rating DESC',
            'resenas'   => 'review_count DESC, avg_rating DESC',
            'nombre'    => 'u.nombre ASC',
        ];
        $order = $orderMap[$f['orden'] ?? 'rating'] ?? $orderMap['rating'];

        $sql = 'SELECT u.id, u.nombre, u.foto_perfil, u.ciudad, u.pais,
                       tp.zona_cobertura, tp.disponibilidad, tp.descripcion,
                       COALESCE(ROUND(AVG(cal.puntuacion), 1), 0) AS avg_rating,
                       COUNT(DISTINCT cal.id) AS review_count,
                       COUNT(DISTINCT CASE WHEN s.estado = \'completada\' THEN s.id END) AS service_count,
                       GROUP_CONCAT(DISTINCT cat.nombre ORDER BY cat.nombre SEPARATOR \', \') AS categorias
                FROM users u
                JOIN tecnico_perfiles tp ON tp.user_id = u.id
                LEFT JOIN tecnico_categorias tc ON tc.user_id = u.id
                LEFT JOIN categorias cat ON cat.id = tc.id_categoria
                LEFT JOIN solicitudes s ON s.id_tecnico = u.id
                LEFT JOIN calificaciones cal ON cal.id_solicitud = s.id
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY u.id, u.nombre, u.foto_perfil, u.ciudad, u.pais,
                         tp.zona_cobertura, tp.disponibilidad, tp.descripcion';

        if ($having) {
            $sql .= ' HAVING ' . implode(' AND ', $having);
        }

        $sql .= ' ORDER BY ' . $order . ' LIMIT :limit OFFSET :offset';

        $stmt = self::db()->prepare($sql);
        foreach ($binds as $key => [$value, $type]) {
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countTechnicians(array $f): int
    {
        [$where, $having, $binds] = self::buildTechnicianSearch($f);

        $inner = 'SELECT u.id,
                         COALESCE(ROUND(AVG(cal.puntuacion), 1), 0) AS avg_rating
                  FROM users u
                  JOIN tecnico_perfiles tp ON tp.user_id = u.id
                  LEFT JOIN solicitudes s ON s.id_tecnico = u.id
                  LEFT JOIN calificaciones cal ON cal.id_solicitud = s.id
                  WHERE ' . implode(' AND ', $where) . '
                  GROUP BY u.id';

        if ($having) {
            $inner .= ' HAVING ' . implode(' AND ', $having);
        }

        $sql  = 'SELECT COUNT(*) AS total FROM (' . $inner . ') sub';
        $stmt = self::db()->prepare($sql);
        foreach ($binds as $key => [$value, $type]) {
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }
}
