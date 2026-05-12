<div class="container">
    <h1>Panel de Cliente</h1>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <div class="grid-two">
        <section class="card">
            <h2>Tu cuenta</h2>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?></p>
            <p><strong>Correo:</strong> <?= htmlspecialchars($user['correo'] ?? '-') ?></p>
            <p><strong>Rol:</strong> <?= htmlspecialchars($user['rol'] ?? 'cliente') ?></p>
            <div class="form-actions">
                <a href="/ejemplo" class="btn btn-primary">Ver solicitudes</a>
                <a href="/perfil" class="btn">Editar perfil</a>
            </div>
        </section>

        <section class="card">
            <h2>Estado como técnico</h2>
            <?php if (empty($techProfile)): ?>
                <p class="muted">Aún no has solicitado ser técnico.</p>
                <a href="/perfil" class="btn btn-primary">Postularme como técnico</a>
            <?php else: ?>
                <p>
                    Estado:
                    <span class="badge badge-<?= htmlspecialchars($techProfile['estado']) ?>">
                        <?= htmlspecialchars($techProfile['estado']) ?>
                    </span>
                </p>
                <p class="muted">Comentario admin: <?= htmlspecialchars($techProfile['comentario_admin'] ?? 'Sin comentarios') ?></p>
                <?php if (($techProfile['estado'] ?? '') !== 'activo'): ?>
                    <a href="/perfil" class="btn">Actualizar solicitud técnica</a>
                <?php else: ?>
                    <a href="/dashboard/tecnico" class="btn btn-primary">Ir a panel técnico</a>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</div>
