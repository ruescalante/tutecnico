<div class="container">
    <h1>Dashboard Admin</h1>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
        <p class="error"><?= htmlspecialchars($errors['auth'][0]) ?></p>
    <?php endif; ?>

    <section class="stats-grid">
        <div class="card">
            <p class="muted">Clientes</p>
            <p class="stat-value"><?= (int) ($stats['clientes'] ?? 0) ?></p>
        </div>
        <div class="card">
            <p class="muted">Técnicos</p>
            <p class="stat-value"><?= (int) ($stats['tecnicos'] ?? 0) ?></p>
        </div>
        <div class="card">
            <p class="muted">Admins</p>
            <p class="stat-value"><?= (int) ($stats['admins'] ?? 0) ?></p>
        </div>
    </section>

    <section class="card">
        <h2>Validación de técnicos</h2>

        <?php if (empty($applications)): ?>
            <p class="muted">No hay solicitudes técnicas.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Documentos</th>
                        <th>Comentario</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($app['nombre']) ?></strong><br>
                                <span class="muted">ID usuario #<?= (int) $app['user_id'] ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($app['correo']) ?><br>
                                <span class="muted"><?= htmlspecialchars($app['telefono'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($app['estado']) ?>">
                                    <?= htmlspecialchars($app['estado']) ?>
                                </span>
                            </td>
                            <td class="muted"><?= htmlspecialchars($app['documentos_verificacion'] ?? '-') ?></td>
                            <td class="muted"><?= htmlspecialchars($app['comentario_admin'] ?? '-') ?></td>
                            <td>
                                <form action="/dashboard/admin/tecnicos/<?= (int) $app['user_id'] ?>/estado" method="POST" class="form">
                                    <input type="hidden" name="_back_url" value="/dashboard/admin">
                                    <label>Estado
                                        <select name="estado">
                                            <?php foreach (['pendiente', 'activo', 'suspendido', 'rechazado'] as $estado): ?>
                                                <option value="<?= $estado ?>" <?= (($app['estado'] ?? '') === $estado ? 'selected' : '') ?>>
                                                    <?= $estado ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label>Comentario
                                        <input type="text" name="comentario_admin" value="<?= htmlspecialchars($app['comentario_admin'] ?? '') ?>" maxlength="500">
                                    </label>
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>
