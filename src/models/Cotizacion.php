<?php

class Cotizacion extends Model
{
    public static function findBySolicitud(int $solicitudId): array|false
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM cotizaciones WHERE id_solicitud = :id ORDER BY fecha DESC LIMIT 1'
        );
        $stmt->execute([':id' => $solicitudId]);
        return $stmt->fetch();
    }

    public static function create(int $solicitudId, float $precio, ?string $descripcion): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO cotizaciones (id_solicitud, precio_estimado, descripcion)
             VALUES (:id_solicitud, :precio, :descripcion)'
        );
        $stmt->execute([
            ':id_solicitud' => $solicitudId,
            ':precio'       => $precio,
            ':descripcion'  => $descripcion,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function updateEstado(int $id, string $estado): void
    {
        $stmt = self::db()->prepare(
            'UPDATE cotizaciones SET estado = :estado WHERE id = :id'
        );
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function totalEarningsByTecnico(int $tecnicoId): float
    {
        $stmt = self::db()->prepare(
            'SELECT COALESCE(SUM(c.precio_estimado), 0)
             FROM cotizaciones c
             JOIN solicitudes s ON s.id = c.id_solicitud
             WHERE s.id_tecnico = :tecnicoId
               AND s.estado = \'completada\'
               AND c.estado = \'aceptada\''
        );
        $stmt->execute([':tecnicoId' => $tecnicoId]);
        return (float) $stmt->fetchColumn();
    }
}
