<?php
class Ejemplo extends Model
{
    public static function all(): array
    {
        return self::db()->query('
            SELECT s.*, 
                   c.nombre AS cliente,
                   t.nombre AS tecnico
            FROM solicitudes s
            JOIN users c ON s.id_cliente = c.id
            LEFT JOIN users t ON s.id_tecnico = t.id
            ORDER BY s.fecha_creacion DESC
        ')->fetchAll();
    }

    public static function find(int $id): array|false
    {
        $stmt = self::db()->prepare('SELECT * FROM solicitudes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function create(array $data): void
    {
        $stmt = self::db()->prepare('
            INSERT INTO solicitudes (id_cliente, titulo, descripcion, direccion)
            VALUES (:id_cliente, :titulo, :descripcion, :direccion)
        ');
        $stmt->execute($data);
    }

    public static function update(int $id, array $data): void
    {
        $stmt = self::db()->prepare('
            UPDATE solicitudes
            SET titulo      = :titulo,
                descripcion = :descripcion,
                direccion   = :direccion,
                estado      = :estado
            WHERE id = :id
        ');
        $stmt->execute([...$data, 'id' => $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = self::db()->prepare('DELETE FROM solicitudes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
