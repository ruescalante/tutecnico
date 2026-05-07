<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
#require_once BASE_PATH . '/core/Router.php';
#require_once BASE_PATH . '/routes/web.php';

$pdo = Database::getInstance();

$usuarios     = $pdo->query('SELECT id, nombre, correo, rol, activo FROM users')->fetchAll();
$categorias   = $pdo->query('SELECT * FROM categorias')->fetchAll();
$solicitudes  = $pdo->query('
    SELECT s.id, s.titulo, s.estado,
           c.nombre AS cliente,
           t.nombre AS tecnico
    FROM solicitudes s
    JOIN users c ON s.id_cliente = c.id
    LEFT JOIN users t ON s.id_tecnico = t.id
')->fetchAll();
$cotizaciones = $pdo->query('
    SELECT co.id, co.precio_estimado, co.estado,
           s.titulo AS solicitud
    FROM cotizaciones co
    JOIN solicitudes s ON co.id_solicitud = s.id
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios Técnicos — Panel</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f5f5f5; }
        h1   { color: #333; }
        h2   { color: #555; margin-top: 2rem; border-bottom: 2px solid #ddd; padding-bottom: .3rem; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 1rem; }
        th   { background: #4a90d9; color: white; padding: .6rem 1rem; text-align: left; }
        td   { padding: .5rem 1rem; border-bottom: 1px solid #eee; }
        tr:hover td { background: #f0f7ff; }
        .badge { padding: .2rem .6rem; border-radius: 999px; font-size: .8rem; font-weight: bold; }
        .admin   { background:#fce8e6; color:#c62828; }
        .tecnico { background:#e8f4fd; color:#1565c0; }
        .cliente { background:#e8f5e9; color:#2e7d32; }
        .activo  { background:#e8f5e9; color:#2e7d32; }
        .inactivo{ background:#fce8e6; color:#c62828; }
    </style>
</head>
<body>

<h1>📋 Panel de datos — Servicios Técnicos</h1>

<!-- USUARIOS -->
<h2>👥 Usuarios (<?= count($usuarios) ?>)</h2>
<table>
    <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th></tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['nombre']) ?></td>
        <td><?= htmlspecialchars($u['correo']) ?></td>
        <td><span class="badge <?= $u['rol'] ?>"><?= $u['rol'] ?></span></td>
        <td><span class="badge <?= $u['activo'] ? 'activo' : 'inactivo' ?>">
            <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
        </span></td>
    </tr>
    <?php endforeach; ?>