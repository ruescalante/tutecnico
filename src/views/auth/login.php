<div class="container auth-box">
    <h1>Ingresar</h1>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <form action="/login" method="POST" class="form">
        <input type="hidden" name="_back_url" value="/login">

        <label>Correo electrónico
            <input type="email" name="correo" value="<?= htmlspecialchars($old['correo'] ?? '') ?>" required>
            <?php if (!empty($errors['correo'])): ?>
                <p class="error"><?= htmlspecialchars($errors['correo'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Contraseña
            <input type="password" name="contrasena" required>
            <?php if (!empty($errors['contrasena'])): ?>
                <p class="error"><?= htmlspecialchars($errors['contrasena'][0]) ?></p>
            <?php endif; ?>
        </label>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
            <a href="/registro" class="btn">Crear cuenta</a>
        </div>
    </form>
</div>
