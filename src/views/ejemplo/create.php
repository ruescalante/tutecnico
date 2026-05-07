<div class="container">
    <h1>Nueva Solicitud</h1>

    <form action="/ejemplo" method="POST" class="form">
        <input type="hidden" name="_back_url" value="/ejemplo/create">
        <label>Título
            <input type="text" name="titulo" value="<?= htmlspecialchars($old['titulo'] ?? '') ?>" required>
            <?php if (!empty($errors['titulo'])): ?>
                <p class="error"><?= htmlspecialchars($errors['titulo'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Descripción
            <textarea name="descripcion" required><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
            <?php if (!empty($errors['descripcion'])): ?>
                <p class="error"><?= htmlspecialchars($errors['descripcion'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Dirección
            <input type="text" name="direccion" value="<?= htmlspecialchars($old['direccion'] ?? '') ?>" required>
            <?php if (!empty($errors['direccion'])): ?>
                <p class="error"><?= htmlspecialchars($errors['direccion'][0]) ?></p>
            <?php endif; ?>
        </label>

        <div class="form-actions">
            <a href="/ejemplo" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear</button>
        </div>

    </form>
</div>