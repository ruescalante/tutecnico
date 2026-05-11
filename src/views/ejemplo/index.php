<?php //$extraCss = '<script src="https://cdn.tailwindcss.com"></script>' ?>
<div class="container">
    <div class="page-header">
        <h1>Solicitudes</h1>
        <a href="/ejemplo/create" class="btn btn-primary">+ Nueva solicitud</a>
    </div>

    <?php if (empty($solicitudes)): ?>
        <p class="empty">No hay solicitudes aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['titulo']) ?></td>
                        <td><?= htmlspecialchars($s['cliente']) ?></td>
                        <td><?= htmlspecialchars($s['direccion']) ?></td>
                        <td>
                            <span class="badge badge-<?= $s['estado'] ?>">
                                <?= $s['estado'] ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="/ejemplo/edit/<?= $s['id'] ?>" class="btn btn-edit">Editar</a>
                            <form action="/ejemplo/delete/<?= $s['id'] ?>" method="POST"
                                onsubmit="return confirm('¿Eliminar esta solicitud?')">
                                <button type="submit" class="btn btn-delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>