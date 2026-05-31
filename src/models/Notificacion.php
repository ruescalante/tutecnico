<?php

class Notificacion extends Model
{
    public static function create(int $userId, string $tipo, ?int $referenciaId): void
    {
        $stmt = self::db()->prepare(
            "INSERT INTO notificaciones (user_id, tipo, referencia_id) VALUES (:uid, :tipo, :ref)"
        );
        $stmt->execute([':uid' => $userId, ':tipo' => $tipo, ':ref' => $referenciaId]);
    }

    public static function getUnreadCount(int $userId): int
    {
        $stmt = self::db()->prepare(
            "SELECT COUNT(*) FROM notificaciones WHERE user_id = :uid AND leida = 0"
        );
        $stmt->execute([':uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getRecent(int $userId, int $limit = 10): array
    {
        $stmt = self::db()->prepare(
            "SELECT n.id, n.tipo, n.referencia_id, n.leida, n.fecha,
                    s.titulo  AS solicitud_titulo,
                    uc.nombre AS cliente_nombre,
                    ut.nombre AS tecnico_nombre
             FROM notificaciones n
             LEFT JOIN solicitudes s ON s.id = n.referencia_id
                 AND n.tipo IN ('solicitud_nueva','solicitud_cancelada','solicitud_completada',
                                'cotizacion_nueva','cotizacion_aceptada','cotizacion_rechazada')
             LEFT JOIN users uc ON uc.id = s.id_cliente
             LEFT JOIN users ut ON ut.id = s.id_tecnico
             WHERE n.user_id = :uid
             ORDER BY n.fecha DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function markAllRead(int $userId): void
    {
        $stmt = self::db()->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE user_id = :uid AND leida = 0"
        );
        $stmt->execute([':uid' => $userId]);
    }
}
