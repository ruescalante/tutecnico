<div class="container">
    <h1>Mi Perfil</h1>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <div class="grid-two">
        <section class="card">
            <h2>Información básica</h2>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($user['nombre'] ?? '-') ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($user['correo'] ?? '-') ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($user['telefono'] ?? '-') ?></p>
            <p>
                <strong>Rol actual:</strong>
                <span class="badge"><?= htmlspecialchars($user['rol'] ?? 'cliente') ?></span>
            </p>
        </section>

        <section class="card">
            <h2>Postulación para Técnico</h2>

            <?php $role = $user['rol'] ?? ($_SESSION['role'] ?? null); ?>

            <?php if ($role === 'admin'): ?>
                <p class="muted">Eres administrador del sistema; no necesitas postularte como técnico.</p>
            <?php elseif ($role === 'tecnico'): ?>
                <p class="muted">Tu usuario ya tiene rol de técnico.</p>
            <?php else: ?>

                <?php if (!empty($techProfile)): ?>
                    <p>
                        Estado actual:
                        <span class="badge badge-<?= htmlspecialchars($techProfile['estado']) ?>">
                            <?= htmlspecialchars($techProfile['estado']) ?>
                        </span>
                    </p>
                    <p class="muted">Comentario admin: <?= htmlspecialchars($techProfile['comentario_admin'] ?? 'Sin comentarios') ?></p>
                <?php endif; ?>

                <p>Para postularte como técnico, completa el formulario dedicado:</p>
                <a href="/perfil/solicitud-tecnico" class="btn btn-primary">Ir a solicitud de técnico</a>

            <?php endif; ?>
        </section>
    </div>
</div>
