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

    public static function listTechnicianApplications(): array
    {
        $stmt = self::db()->query(
            'SELECT tp.*, u.nombre, u.correo, u.telefono, u.rol, u.activo
             FROM tecnico_perfiles tp
             JOIN users u ON u.id = tp.user_id
             ORDER BY tp.fecha_estado_cambio DESC, tp.id DESC'
        );
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
             SET nombre = :nombre,
                 correo = :correo,
                 telefono = :telefono
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $userId,
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
        ]);
    }
}
