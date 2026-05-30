<?php

class Solicitud extends Model
{
    public static function listAll(string $search = '', string $estado = ''): array
    {
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]                 = '(s.titulo LIKE :search_titulo OR c.nombre LIKE :search_nombre)';
            $params['search_titulo'] = '%' . $search . '%';
            $params['search_nombre'] = '%' . $search . '%';
        }
        if ($estado !== '') {
            $where[]          = 's.estado = :estado';
            $params['estado'] = $estado;
        }

        $sql = 'SELECT
                    s.id,
                    s.titulo,
                    s.estado,
                    s.fecha_creacion,
                    c.nombre  AS cliente_nombre,
                    c.correo  AS cliente_correo,
                    t.nombre  AS tecnico_nombre
                FROM solicitudes s
                JOIN users c ON c.id = s.id_cliente
                LEFT JOIN users t ON t.id = s.id_tecnico';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY s.fecha_creacion DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function create(int $clienteId, int $tecnicoId, string $titulo, string $descripcion, string $direccion): int
    {
        $stmt = self::db()->prepare(
            'INSERT INTO solicitudes (id_cliente, id_tecnico, titulo, descripcion, direccion)
             VALUES (:id_cliente, :id_tecnico, :titulo, :descripcion, :direccion)'
        );
        $stmt->execute([
            ':id_cliente'  => $clienteId,
            ':id_tecnico'  => $tecnicoId,
            ':titulo'      => $titulo,
            ':descripcion' => $descripcion,
            ':direccion'   => $direccion,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function findByCliente(int $userId, string $estado = ''): array
    {
        $where  = ['s.id_cliente = :userId'];
        $params = [':userId' => $userId];

        if ($estado !== '') {
            $where[]          = 's.estado = :estado';
            $params[':estado'] = $estado;
        }

        $sql = 'SELECT s.*,
                    t.nombre        AS tecnico_nombre,
                    t.foto_perfil   AS tecnico_foto,
                    c.id            AS cotizacion_id,
                    c.precio_estimado AS cotizacion_precio,
                    c.estado        AS cotizacion_estado,
                    c.descripcion   AS cotizacion_descripcion
                FROM solicitudes s
                LEFT JOIN users t ON t.id = s.id_tecnico
                LEFT JOIN cotizaciones c ON c.id_solicitud = s.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY s.fecha_creacion DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findByTecnico(int $userId, string $estado = ''): array
    {
        $where  = ['s.id_tecnico = :userId'];
        $params = [':userId' => $userId];

        if ($estado !== '') {
            $where[]          = 's.estado = :estado';
            $params[':estado'] = $estado;
        }

        $sql = 'SELECT s.*,
                    cl.nombre       AS cliente_nombre,
                    cl.foto_perfil  AS cliente_foto,
                    cl.telefono     AS cliente_telefono,
                    c.id            AS cotizacion_id,
                    c.precio_estimado AS cotizacion_precio,
                    c.estado        AS cotizacion_estado
                FROM solicitudes s
                JOIN users cl ON cl.id = s.id_cliente
                LEFT JOIN cotizaciones c ON c.id_solicitud = s.id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY s.fecha_creacion DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findWithParticipants(int $id): array|false
    {
        $stmt = self::db()->prepare(
            'SELECT s.*,
                    cl.nombre       AS cliente_nombre,
                    cl.foto_perfil  AS cliente_foto,
                    cl.telefono     AS cliente_telefono,
                    cl.correo       AS cliente_correo,
                    cl.direccion    AS cliente_direccion,
                    t.nombre        AS tecnico_nombre,
                    t.foto_perfil   AS tecnico_foto
             FROM solicitudes s
             JOIN users cl ON cl.id = s.id_cliente
             LEFT JOIN users t ON t.id = s.id_tecnico
             WHERE s.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function updateStatus(int $id, string $estado): void
    {
        $stmt = self::db()->prepare(
            'UPDATE solicitudes SET estado = :estado WHERE id = :id'
        );
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function cancel(int $id, string $canceladoPor): void
    {
        $stmt = self::db()->prepare(
            'UPDATE solicitudes SET estado = :estado, cancelado_por = :por WHERE id = :id'
        );
        $stmt->execute([':estado' => 'cancelada', ':por' => $canceladoPor, ':id' => $id]);
    }
}
