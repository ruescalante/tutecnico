<?php if (empty($solicitud)): ?>
    <p class="error">No hay datos de la solicitud.</p>
    <?php return; ?>
<?php endif; ?>
<div class="container">
    <h1>Editar Solicitud #<?= $solicitud['id'] ?></h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif ?>

    <form action="/ejemplo/update" method="POST" class="form">
        <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">

        <label>Título
            <input type="text" name="titulo"
                value="<?= htmlspecialchars($_POST['titulo'] ?? $solicitud['titulo']) ?>" required>
        </label>
        <label>Descripción
            <textarea name="descripcion" required><?= htmlspecialchars($_POST['descripcion'] ?? $solicitud['descripcion']) ?></textarea>
        </label>
        <label>Dirección
            <input type="text" name="direccion"
                value="<?= htmlspecialchars($_POST['direccion'] ?? $solicitud['direccion']) ?>" required>
        </label>
        <label>Estado
            <select name="estado">
                <?php foreach (['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($solicitud['estado'] === $opt ? 'selected' : '') ?>>
                        <?= $opt ?>
                    </option>
                <?php endforeach ?>
            </select>
        </label>
        <div class="form-actions">
            <a href="/ejemplo" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>