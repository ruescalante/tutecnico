<div class="container panel-cliente">

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <?php
        $nombre = htmlspecialchars($user['nombre'] ?? 'Usuario');
        $correo = htmlspecialchars($user['correo'] ?? '-');
        $rol    = htmlspecialchars($user['rol'] ?? 'cliente');
        // Iniciales del avatar
        $partes   = explode(' ', trim($nombre));
        $iniciales = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
    ?>

    <h1 class="panel-greeting">¡Bienvenido/a, <?= $partes[0] ?>!</h1>
    <p class="panel-sub">Aquí puedes gestionar tu cuenta y servicios en TuTécnico.</p>

    <div class="grid-two">
        <section class="card">
            <div class="card-icon">👤</div>
            <h2>Tu cuenta</h2>
            <div class="user-row">
                <div class="user-avatar"><?= $iniciales ?></div>
                <div class="user-info">
                    <h3><?= $nombre ?></h3>
                    <p><?= $correo ?></p>
                </div>
            </div>
            <div class="card-divider"></div>
            <p class="field-label">Rol</p>
            <p><span class="badge badge-activo">✓ <?= ucfirst($rol) ?> verificado</span></p>
            <div class="form-actions">
                <a href="/ejemplo" class="btn btn-primary"> Ver solicitudes</a>
                <a href="/perfil" class="btn"> Editar perfil</a>
            </div>
        </section>

        <section class="card">
            <div class="card-icon">🔧</div>
            <h2>Estado como técnico</h2>
            <?php if (empty($techProfile)): ?>
                <div class="status-pending">
                    <div class="status-dot"></div>
                    <p class="muted">Aún no has solicitado ser técnico.</p>
                </div>
                <div class="card-divider"></div>
                <div class="feature-list">
                    <div class="feature-item"><div class="feature-check">✓</div> Recibe solicitudes de clientes</div>
                    <div class="feature-item"><div class="feature-check">✓</div> Gestiona tu agenda fácilmente</div>
                    <div class="feature-item"><div class="feature-check">✓</div> Construye tu reputación con reseñas</div>
                </div>
                <div class="form-actions">
                    <a href="/perfil" class="btn btn-primary">Postularme como técnico</a>
                </div>
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