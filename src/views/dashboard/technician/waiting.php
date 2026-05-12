<div class="container">
    <h1>Verificación de Técnico</h1>

    <div class="card">
        <p>
            Estado actual:
            <span class="badge badge-<?= htmlspecialchars($profile['estado'] ?? 'pendiente') ?>">
                <?= htmlspecialchars($profile['estado'] ?? 'pendiente') ?>
            </span>
        </p>
        <p class="muted">Comentario admin: <?= htmlspecialchars($profile['comentario_admin'] ?? 'Sin comentarios') ?></p>

        <?php if (($profile['estado'] ?? '') === 'pendiente'): ?>
            <p>Tu perfil está pendiente de revisión por un administrador.</p>
        <?php elseif (($profile['estado'] ?? '') === 'rechazado'): ?>
            <p>No fue posible aprobar tu perfil técnico. Puedes actualizar tu información y volver a postularte.</p>
            <a class="btn" href="/perfil">Actualizar postulación</a>
        <?php elseif (($profile['estado'] ?? '') === 'suspendido'): ?>
            <p>Tu acceso técnico está suspendido temporalmente.</p>
        <?php endif; ?>
    </div>
</div>
