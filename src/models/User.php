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
}
