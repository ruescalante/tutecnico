<div class="container">
    <h1>Panel Técnico</h1>

    <div class="card">
        <p><strong>Zona de cobertura:</strong> <?= htmlspecialchars($profile['zona_cobertura'] ?? '-') ?></p>
        <p><strong>Descripción:</strong> <?= htmlspecialchars($profile['descripcion'] ?? '-') ?></p>
        <p>
            <strong>Estado:</strong>
            <span class="badge badge-<?= htmlspecialchars($profile['estado'] ?? 'pendiente') ?>">
                <?= htmlspecialchars($profile['estado'] ?? 'pendiente') ?>
            </span>
        </p>
        <p><strong>Cancelaciones:</strong> <?= (int) ($profile['cancelaciones'] ?? 0) ?></p>
    </div>
</div>
