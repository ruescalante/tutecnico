<div class="container panel-cliente">
    <h1 class="panel-greeting">Solicitud para ser Técnico </h1>
    <p class="panel-sub">Completa el formulario para postularte como técnico en TuTécnico.</p>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <section class="card solicitud-card">
        <?php if (!empty($techProfile)): ?>
            <div class="perfil-campo" style="margin-bottom:1rem">
                <span class="field-label">Estado actual</span>
                <span class="badge badge-<?= htmlspecialchars($techProfile['estado']) ?>">
                    <?= htmlspecialchars($techProfile['estado']) ?>
                </span>
            </div>
            <p class="muted" style="margin-bottom:1rem">
                 <?= htmlspecialchars($techProfile['comentario_admin'] ?? 'Sin comentarios') ?>
            </p>
            <div class="card-divider"></div>
        <?php endif; ?>

        <form action="/perfil/solicitud-tecnico" method="POST" class="form">
            <input type="hidden" name="_back_url" value="/perfil">

            <label>Zona de cobertura
                <input type="text" name="zona_cobertura" value="<?= htmlspecialchars($old['zona_cobertura'] ?? $techProfile['zona_cobertura'] ?? '') ?>" required>
                <?php if (!empty($errors['zona_cobertura'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['zona_cobertura'][0]) ?></p>
                <?php endif; ?>
            </label>

            <label>Descripción de experiencia
                <textarea name="descripcion" required><?= htmlspecialchars($old['descripcion'] ?? $techProfile['descripcion'] ?? '') ?></textarea>
                <?php if (!empty($errors['descripcion'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['descripcion'][0]) ?></p>
                <?php endif; ?>
            </label>

            <label>Ruta o referencia de documentos
                <input type="text" name="documentos_verificacion" value="<?= htmlspecialchars($old['documentos_verificacion'] ?? $techProfile['documentos_verificacion'] ?? '') ?>" required>
                <?php if (!empty($errors['documentos_verificacion'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['documentos_verificacion'][0]) ?></p>
                <?php endif; ?>
            </label>

            <div class="form-actions">
                <a href="/perfil" class="btn">Volver al perfil</a>
                <button type="submit" class="btn btn-primary">Enviar / Actualizar solicitud</button>
            </div>
        </form>
    </section>
</div>
