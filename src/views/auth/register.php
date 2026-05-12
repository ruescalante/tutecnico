<div class="container auth-box">
    <h1>Registro</h1>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <form action="/registro" method="POST" class="form">
        <input type="hidden" name="_back_url" value="/registro">

        <label>Nombre
            <input type="text" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" required>
            <?php if (!empty($errors['nombre'])): ?>
                <p class="error"><?= htmlspecialchars($errors['nombre'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Correo electrónico
            <input type="email" name="correo" value="<?= htmlspecialchars($old['correo'] ?? '') ?>" required>
            <?php if (!empty($errors['correo'])): ?>
                <p class="error"><?= htmlspecialchars($errors['correo'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Teléfono (opcional)
            <input type="text" name="telefono" value="<?= htmlspecialchars($old['telefono'] ?? '') ?>">
        </label>

        <label>Contraseña
            <input type="password" name="contrasena" required>
            <?php if (!empty($errors['contrasena'])): ?>
                <p class="error"><?= htmlspecialchars($errors['contrasena'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Confirmar contraseña
            <input type="password" name="contrasena_confirmacion" required>
            <?php if (!empty($errors['contrasena_confirmacion'])): ?>
                <p class="error"><?= htmlspecialchars($errors['contrasena_confirmacion'][0]) ?></p>
            <?php endif; ?>
        </label>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear cuenta</button>
            <a href="/login" class="btn">Ya tengo cuenta</a>
        </div>
    </form>
</div>
