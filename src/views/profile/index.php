<div class="container panel-cliente">

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <?php
        $nombre    = htmlspecialchars($user['nombre'] ?? 'Usuario');
        $correo    = htmlspecialchars($user['correo'] ?? '-');
        $telefono  = htmlspecialchars($user['telefono'] ?? '-');
        $rol       = htmlspecialchars($user['rol'] ?? 'cliente');
        $direccion = htmlspecialchars($user['direccion'] ?? '');
        $ciudad    = htmlspecialchars($user['ciudad'] ?? '');
        $pais      = htmlspecialchars($user['pais'] ?? 'El Salvador');
        $ubicacion = trim($ciudad . ($ciudad && $pais ? ', ' : '') . $pais);
        $foto      = $user['foto_perfil'] ?? null;
        $partes    = explode(' ', trim($nombre));
        $iniciales = strtoupper(substr($partes[0],0,1) . (isset($partes[1]) ? substr($partes[1],0,1) : ''));
        $role      = $user['rol'] ?? ($_SESSION['role'] ?? null);
    ?>

    <h1 class="panel-greeting">Mi Perfil</h1>
    <p class="panel-sub">Gestiona y actualiza tu información personal.</p>

    <div class="perfil-layout">

        <div class="perfil-main-card card">
            <div class="perfil-header">
                <div class="perfil-foto-wrap">
                    <?php if ($foto): ?>
                        <img src="<?= $foto ?>" alt="Foto de perfil" class="perfil-foto">
                    <?php else: ?>
                        <div class="perfil-foto perfil-foto-iniciales"><?= $iniciales ?></div>
                    <?php endif; ?>
                </div>
                <div class="perfil-header-info">
                    <h2 class="perfil-nombre"><?= $nombre ?></h2>
                    <span class="badge badge-activo">✓ <?= ucfirst($rol) ?></span>
                    <div >
                        <span class="perfil-campo-icon">📧</span>
                        <span class="perfil-campo-texto"><?= $correo ?></span>
                    </div>
                    <div>
                        <span class="perfil-campo-icon">📞</span>
                        <span class="perfil-campo-texto"><?= $telefono ?></span>
                    </div>
                </div>
            </div>

            <div class="card-divider"></div>

            <div class="perfil-detalle">
                <?php if ($direccion): ?>
                <div class="perfil-campo-detalle">
                    <span class="perfil-campo-icon">🏠</span>
                    <span class="perfil-campo-texto"><?= $direccion ?></span>
                </div>
                <?php endif; ?>
                <?php if ($ubicacion): ?>
                <div class="perfil-campo-detalle">
                    <span class="perfil-campo-icon">📍</span>
                    <span class="perfil-campo-texto"><?= $ubicacion ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="form-actions" style="margin-top:1.5rem">
                <a href="/perfil/editar" class="btn btn-primary">Editar perfil</a>
            </div>
        </div>

        <div class="perfil-side">
            <section class="card">
                <div class="card-icon">🔧</div>
                <h2>Postulación para Técnico</h2>

                <?php if ($role === 'admin'): ?>
                    <div class="status-pending">
                        <div class="status-dot" style="background:#9FE1CB"></div>
                        <p class="muted">Eres administrador del sistema; no necesitas postularte.</p>
                    </div>

                <?php elseif ($role === 'tecnico'): ?>
                    <div class="status-pending">
                        <div class="status-dot"></div>
                        <p class="muted">Tu usuario ya tiene rol de técnico.</p>
                    </div>

                <?php else: ?>
                    <?php if (!empty($techProfile)): ?>
                        <div class="perfil-campo">
                            <span class="field-label">Estado actual</span>
                            <span class="badge badge-<?= htmlspecialchars($techProfile['estado']) ?>">
                                <?= htmlspecialchars($techProfile['estado']) ?>
                            </span>
                        </div>
                        <p class="muted" style="margin-top:0.5rem">
                            <?= htmlspecialchars($techProfile['comentario_admin'] ?? 'Sin comentarios') ?>
                        </p>
                        <div class="card-divider"></div>
                    <?php endif; ?>

                    <div class="feature-list">
                        <div class="feature-item"><div class="feature-check">✓</div> Recibe solicitudes de clientes</div>
                        <div class="feature-item"><div class="feature-check">✓</div> Gestiona tu agenda fácilmente</div>
                        <div class="feature-item"><div class="feature-check">✓</div> Construye tu reputación con reseñas</div>
                    </div>
                    <div class="form-actions" style="margin-top:1.25rem">
                        <a href="/perfil/solicitud-tecnico" class="btn btn-primary">Ir a solicitud de técnico</a>
                    </div>
                <?php endif; ?>
            </section>
        </div>

    </div>
</div>
