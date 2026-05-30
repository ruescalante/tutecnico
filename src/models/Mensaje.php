<?php

class Mensaje extends Model
{
    public static function findBySolicitud(int $solicitudId): array
    {
        $stmt = self::db()->prepare(
            'SELECT m.id, m.remitente_tipo, m.remitente_id, m.contenido, m.leido, m.fecha,
                    u.nombre AS remitente_nombre, u.foto_perfil AS remitente_foto
             FROM mensajes m
             JOIN users u ON u.id = m.remitente_id
             WHERE m.id_solicitud = :sid
             ORDER BY m.fecha ASC'
        );
        $stmt->execute([':sid' => $solicitudId]);
        return $stmt->fetchAll();
    }

    public static function create(int $solicitudId, string $remitenteType, int $remitenteId, string $contenido): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO mensajes (id_solicitud, remitente_tipo, remitente_id, contenido)
             VALUES (:sid, :tipo, :rid, :contenido)'
        );
        $stmt->execute([
            ':sid'      => $solicitudId,
            ':tipo'     => $remitenteType,
            ':rid'      => $remitenteId,
            ':contenido'=> $contenido,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function markReadForUser(int $solicitudId, string $myType): void
    {
        $stmt = self::db()->prepare(
            'UPDATE mensajes SET leido = 1
             WHERE id_solicitud = :sid AND remitente_tipo != :myType AND leido = 0'
        );
        $stmt->execute([':sid' => $solicitudId, ':myType' => $myType]);
    }
}
