<div class="container">
    <h1>Editar Perfil</h1>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
        <p class="error"><?= htmlspecialchars($errors['general'][0]) ?></p>
    <?php endif; ?>

    <section class="card" style="max-width: 500px;">
        <form action="/perfil/editar" method="POST" class="form">
            <label>Nombre completo
                <input type="text" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? $user['nombre'] ?? '') ?>" required>
                <?php if (!empty($errors['nombre'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['nombre'][0]) ?></p>
                <?php endif; ?>
            </label>

            <label>Correo electrónico
                <input type="email" name="correo" value="<?= htmlspecialchars($old['correo'] ?? $user['correo'] ?? '') ?>" required>
                <?php if (!empty($errors['correo'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['correo'][0]) ?></p>
                <?php endif; ?>
            </label>

            <label>Teléfono
                <input type="tel" name="telefono" value="<?= htmlspecialchars($old['telefono'] ?? $user['telefono'] ?? '') ?>">
                <?php if (!empty($errors['telefono'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['telefono'][0]) ?></p>
                <?php endif; ?>
            </label>

            <div class="form-actions">
                <a href="/perfil" class="btn">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </section>
</div>
