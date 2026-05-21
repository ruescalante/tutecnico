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
}
