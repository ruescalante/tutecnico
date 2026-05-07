<?php if (empty($solicitud)): ?>
    <p class="error">No hay datos de la solicitud.</p>
    <?php return; ?>
<?php endif; ?>
<?php
$old = $old ?? [];
?>
<div class="container">
    <h1>Editar Solicitud #<?= $solicitud['id'] ?></h1>

    <form action="/ejemplo/update" method="POST" class="form">
        <input type="hidden" name="_back_url" value="/ejemplo/edit?id=<?= $solicitud['id'] ?>">
        <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">

        <label>Título
            <input type="text" name="titulo"
                value="<?= htmlspecialchars($old['titulo'] ?? $solicitud['titulo']) ?>" required>
            <?php if (!empty($errors['titulo'])): ?>
                <p class="error"><?= htmlspecialchars($errors['titulo'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Descripción
            <textarea name="descripcion" required><?= htmlspecialchars($old['descripcion'] ?? $solicitud['descripcion']) ?></textarea>
            <?php if (!empty($errors['descripcion'])): ?>
                <p class="error"><?= htmlspecialchars($errors['descripcion'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Dirección
            <input type="text" name="direccion"
                value="<?= htmlspecialchars($old['direccion'] ?? $solicitud['direccion']) ?>" required>
            <?php if (!empty($errors['direccion'])): ?>
                <p class="error"><?= htmlspecialchars($errors['direccion'][0]) ?></p>
            <?php endif; ?>
        </label>

        <label>Estado
            <select name="estado">
                <?php
                $estadoActual = $old['estado'] ?? $solicitud['estado'];
                foreach (['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($estadoActual === $opt ? 'selected' : '') ?>>
                        <?= $opt ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['estado'])): ?>
                <p class="error"><?= htmlspecialchars($errors['estado'][0]) ?></p>
            <?php endif; ?>
        </label>

        <div class="form-actions">
            <a href="/ejemplo" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>