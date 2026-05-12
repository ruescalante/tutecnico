<?php
$isLogged = !empty($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;
?>
<nav>
    <a href="/">Inicio</a>

    <?php if ($isLogged): ?>
        <a href="/dashboard">Dashboard</a>
        <a href="/perfil">Perfil</a>
        <a href="/ejemplo">Solicitudes</a>
        <?php if ($role === 'admin'): ?>
            <a href="/dashboard/admin">Admin</a>
        <?php endif; ?>

        <span class="nav-user">
            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
            (<?= htmlspecialchars($role ?? 'cliente') ?>)
        </span>
        <form action="/logout" method="POST" class="nav-inline-form">
            <button type="submit" class="btn btn-delete">Salir</button>
        </form>
    <?php else: ?>
        <a href="/login">Ingresar</a>
        <a href="/registro">Registrarse</a>
    <?php endif; ?>
</nav>