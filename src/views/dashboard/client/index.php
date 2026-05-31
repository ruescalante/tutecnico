<?php
    $tabs = [
        ''           => 'Todas',
        'pendiente'  => 'Pendientes',
        'aceptada'   => 'En Proceso',
        'en_progreso'=> 'Aceptadas',
        'completada' => 'Finalizadas',
        'cancelada'  => 'Canceladas',
    ];

    $estadoBadge = [
        'pendiente'   => ['label' => 'Pendiente',        'classes' => 'bg-secondary-container text-on-surface'],
        'aceptada'    => ['label' => 'Cotización recibida','classes' => 'bg-primary-container/20 text-primary border border-primary/20'],
        'en_progreso' => ['label' => 'En progreso',      'classes' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant'],
        'completada'  => ['label' => 'Completada',       'classes' => 'bg-primary-container/10 text-primary'],
        'cancelada'   => ['label' => 'Cancelada',        'classes' => 'bg-error-container text-on-error-container'],
    ];

    $currentEstado = $_GET['estado'] ?? '';
?>

<div class="max-w-5xl mx-auto px-4 md:px-8 py-8 md:py-12 flex flex-col gap-6">

<?php if (!empty($success)): ?>
<div class="mb-5 px-4 py-3 rounded-lg bg-primary-container/20 text-primary border border-primary/20 text-sm flex items-center gap-2">
    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">check_circle</span>
    <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<!-- Header -->
<section class="mb-6">
    <h1 class="font-bold text-2xl text-on-surface mb-1">Mis Solicitudes</h1>
    <p class="text-on-surface-variant text-sm">
        Gestiona el estado de tus solicitudes de servicio técnico y revisa las cotizaciones.
    </p>
</section>

<!-- Tabs de filtro -->
<div class="flex overflow-x-auto gap-2 pb-2 mb-6" style="-ms-overflow-style:none;scrollbar-width:none;">
    <?php foreach ($tabs as $val => $label): ?>
        <a href="/dashboard/cliente<?= $val !== '' ? '?estado=' . urlencode($val) : '' ?>"
           class="whitespace-nowrap px-5 py-2 rounded-full text-sm font-medium transition-colors
                  <?= $currentEstado === $val
                      ? 'bg-secondary-container text-on-surface font-semibold shadow-sm'
                      : 'bg-transparent text-on-surface-variant border border-outline-variant hover:bg-surface-container' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Grid de solicitudes -->
<?php if (empty($solicitudes)): ?>
<div class="text-center py-20">
    <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">assignment</span>
    <h3 class="font-semibold text-on-surface mb-2">No tienes solicitudes aún</h3>
    <p class="text-sm text-on-surface-variant mb-6">Encuentra un técnico y solicita tu primer servicio.</p>
    <a href="/"
       class="inline-block bg-primary-container text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-primary transition-colors">
        Buscar técnicos
    </a>
</div>
<?php else: ?>
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($solicitudes as $sol): ?>
    <?php
        $solId     = (int) $sol['id'];
        $solEs     = $sol['estado'];
        $badgeInfo = $estadoBadge[$solEs] ?? ['label' => ucfirst($solEs), 'classes' => 'bg-surface-container text-on-surface'];

        $tecNombre = htmlspecialchars($sol['tecnico_nombre'] ?? 'Por asignar');
        $tecFoto   = $sol['tecnico_foto'] ?? null;
        $tecPartes = explode(' ', trim($tecNombre));
        $tecInic   = strtoupper(substr($tecPartes[0], 0, 1) . (isset($tecPartes[1]) ? substr($tecPartes[1], 0, 1) : ''));

        $fechaSol  = date('d M, Y', strtotime($sol['fecha_creacion']));
        $tieneCot  = !empty($sol['cotizacion_id']);
        $precio    = $tieneCot ? '$' . number_format((float) $sol['cotizacion_precio'], 2) : null;
    ?>
    <article class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-5 flex flex-col gap-4 hover:shadow-md transition-shadow cursor-pointer"
             onclick="window.location='/solicitudes/<?= $solId ?>'"
             style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden
                            <?= $tecFoto ? '' : 'bg-secondary-container flex items-center justify-center' ?>">
                    <?php if ($tecFoto): ?>
                        <img src="<?= htmlspecialchars($tecFoto) ?>" alt="<?= $tecNombre ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-on-surface-variant font-bold text-sm"><?= $tecInic ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <h4 class="font-semibold text-on-surface text-sm"><?= $tecNombre ?></h4>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $badgeInfo['classes'] ?>">
                <?= $badgeInfo['label'] ?>
            </span>
        </div>

        <div>
            <h3 class="font-semibold text-on-surface text-base leading-snug mb-1">
                <?= htmlspecialchars($sol['titulo']) ?>
            </h3>
            <div class="flex items-center gap-1 text-on-surface-variant text-xs">
                <span class="material-symbols-outlined text-sm">calendar_today</span>
                <span><?= $fechaSol ?></span>
            </div>
        </div>

        <hr class="border-outline-variant/20">

        <div class="flex items-center justify-between">
            <div>
                <?php if ($tieneCot && $precio): ?>
                    <p class="text-[10px] text-on-surface-variant uppercase tracking-tight font-medium">Cotización</p>
                    <p class="font-bold text-primary text-lg leading-tight"><?= $precio ?></p>
                <?php elseif ($solEs === 'pendiente'): ?>
                    <p class="text-[10px] text-on-surface-variant uppercase tracking-tight font-medium">Cotización</p>
                    <p class="text-outline text-sm italic">En espera...</p>
                <?php else: ?>
                    <span class="text-xs text-on-surface-variant">—</span>
                <?php endif; ?>
            </div>
            <a href="/solicitudes/<?= $solId ?>"
               onclick="event.stopPropagation()"
               class="px-3 py-1.5 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-secondary-container transition-colors">
                Ver Detalles
            </a>
        </div>
    </article>
    <?php endforeach; ?>
</section>
<?php endif; ?>

</div>
