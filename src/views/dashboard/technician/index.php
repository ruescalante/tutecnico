<?php
    $nombre    = htmlspecialchars($user['nombre'] ?? 'Técnico');
    $correo    = htmlspecialchars($user['correo'] ?? '');
    $telefono  = htmlspecialchars($user['telefono'] ?? '');
    $ciudad    = htmlspecialchars($user['ciudad'] ?? '');
    $pais      = htmlspecialchars($user['pais'] ?? 'El Salvador');
    $ubicacion = trim($ciudad . ($ciudad && $pais ? ', ' : '') . $pais);
    $foto      = $user['foto_perfil'] ?? null;
    $partes    = explode(' ', trim($nombre));
    $iniciales = strtoupper(substr($partes[0],0,1) . (isset($partes[1]) ? substr($partes[1],0,1) : ''));
    $descripcion   = htmlspecialchars($profile['descripcion'] ?? '');
    $zonaCobertura = htmlspecialchars($profile['zona_cobertura'] ?? '');
?>

<div class="tecnico-perfil-wrap">

    <!-- Header -->
    <div class="tecnico-header card">
        <div class="tecnico-header-inner">
            <div class="tecnico-foto-wrap">
                <?php if ($foto): ?>
                    <img src="<?= htmlspecialchars($foto) ?>" class="tecnico-foto" alt="Foto">
                <?php else: ?>
                    <div class="tecnico-foto tecnico-foto-iniciales"><?= $iniciales ?></div>
                <?php endif; ?>
            </div>
            <div class="tecnico-header-info">
                <div class="tecnico-nombre-row">
                    <h1 class="tecnico-nombre"><?= $nombre ?></h1>
                    <span class="badge badge-activo">✓ Verificado</span>
                </div>
                <div class="tecnico-categorias-row">
                    <?php foreach ($misCategsNombres as $cat): ?>
                        <span class="tecnico-categ-tag"><?= htmlspecialchars($cat['nombre']) ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="tecnico-contacto-row">
                    <?php if ($telefono): ?>
                        <span class="tecnico-dato">📞 <?= $telefono ?></span>
                    <?php endif; ?>
                    <?php if ($correo): ?>
                        <span class="tecnico-dato">📧 <?= $correo ?></span>
                    <?php endif; ?>
                    <?php if ($ubicacion): ?>
                        <span class="tecnico-dato">📍 <?= $ubicacion ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="tecnico-body">

        <!-- Columna principal -->
        <div class="tecnico-main">

            <!-- Sobre mí -->
            <?php if ($descripcion): ?>
            <section class="card">
                <h2 class="tecnico-section-title">Sobre mí</h2>
                <p class="tecnico-descripcion"><?= $descripcion ?></p>
            </section>
            <?php endif; ?>

            <!-- Fotos de trabajos -->
            <?php if (!empty($fotosTrabajo)): ?>
            <section class="card">
                <h2 class="tecnico-section-title">Fotos de trabajos</h2>
                <div class="tecnico-fotos-grid">
                    <?php foreach ($fotosTrabajo as $ft): ?>
                        <div class="tecnico-foto-item">
                            <img src="<?= htmlspecialchars($ft['url']) ?>"
                                 alt="<?= htmlspecialchars($ft['descripcion'] ?? '') ?>">
                            <?php if ($ft['descripcion']): ?>
                                <p class="tecnico-foto-desc"><?= htmlspecialchars($ft['descripcion']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

        </div>

        <!-- Columna lateral -->
        <aside class="tecnico-aside">
            <section class="card">
                <h2 class="tecnico-section-title">Detalles</h2>
                <div class="tecnico-detalle-list">
                    <div class="tecnico-detalle-item">
                        <span class="tecnico-detalle-icon">✅</span>
                        <span>Verificado</span>
                    </div>
                    <?php if ($zonaCobertura): ?>
                    <div class="tecnico-detalle-item">
                        <span class="tecnico-detalle-icon">📍</span>
                        <span><?= $zonaCobertura ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="tecnico-detalle-item">
                        <span class="tecnico-detalle-icon">🗂️</span>
                        <span><?= count($misCategsNombres) ?> categorías</span>
                    </div>
                    <div class="tecnico-detalle-item">
                        <span class="tecnico-detalle-icon">📸</span>
                        <span><?= count($fotosTrabajo) ?> fotos de trabajos</span>
                    </div>
                </div>
            </section>
        </aside>

    </div>

</div>
