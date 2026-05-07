<div class="container">
    <h1>Nueva Solicitud</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif ?>

    <form action="/ejemplo" method="POST" class="form">
        <label>Título
            <input type="text" name="titulo" value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>" required>
        </label>
        <label>Descripción
            <textarea name="descripcion" required><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
        </label>
        <label>Dirección
            <input type="text" name="direccion" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>" required>
        </label>
        <div class="form-actions">
            <a href="/ejemplo" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear</button>
        </div>
    </form>
</div>