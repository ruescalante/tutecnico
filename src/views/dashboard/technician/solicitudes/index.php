<?php
    $tabs = [
        ''           => 'Todas',
        'pendiente'  => 'Pendientes',
        'aceptada'   => 'Cotizadas',
        'en_progreso'=> 'En progreso',
        'completada' => 'Completadas',
        'cancelada'  => 'Canceladas',
    ];

    $estadoBadge = [
        'pendiente'   => 'bg-secondary-container text-on-surface',
        'aceptada'    => 'bg-primary-container/20 text-primary',
        'en_progreso' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        'completada'  => 'bg-primary-container/10 text-primary',
        'cancelada'   => 'bg-error-container text-on-error-container',
    ];

    $estadoLabel = [
        'pendiente'   => 'Pendiente',
        'aceptada'    => 'Cotizada',
        'en_progreso' => 'En progreso',
        'completada'  => 'Completada',
        'cancelada'   => 'Cancelada',
    ];
?>

<!-- Título -->
<div class="mb-8">
    <h1 class="font-headline-lg text-2xl font-bold text-on-surface mb-1">Mis Solicitudes</h1>
    <p class="text-on-surface-variant text-sm">Gestiona las solicitudes de servicio que te han enviado los clientes.</p>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 flex items-center gap-4"
         style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div class="w-12 h-12 rounded-full bg-error-container text-on-error-container flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined">pending_actions</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-medium">Pendientes</p>
            <p class="text-3xl font-bold text-on-surface"><?= $pendientes ?></p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 flex items-center gap-4"
         style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div class="w-12 h-12 rounded-full bg-tertiary-fixed/50 text-on-tertiary-fixed-variant flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined">work</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-medium">Activas</p>
            <p class="text-3xl font-bold text-on-surface"><?= $activas ?></p>
        </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 flex items-center gap-4"
         style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div class="w-12 h-12 rounded-full bg-primary-container/20 text-primary flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined">task_alt</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant uppercase tracking-wider font-medium">Completadas</p>
            <p class="text-3xl font-bold text-on-surface"><?= $completadas ?></p>
        </div>
    </div>
    <div class="bg-primary-container rounded-xl p-6 flex items-center gap-4 relative overflow-hidden"
         style="box-shadow:0 1px 3px rgba(0,121,107,.12)">
        <div class="absolute -right-3 -bottom-3 opacity-10">
            <span class="material-symbols-outlined text-[100px] text-white">payments</span>
        </div>
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 z-10">
            <span class="material-symbols-outlined text-white">payments</span>
        </div>
        <div class="z-10">
            <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Ganancias Totales</p>
            <p class="text-2xl font-bold text-white leading-tight">$<?= number_format($ganancias, 2) ?></p>
        </div>
    </div>
</div>

<!-- Tabs de filtro -->
<div class="flex overflow-x-auto gap-2 pb-1 mb-6" style="-ms-overflow-style:none;scrollbar-width:none;">
    <?php foreach ($tabs as $val => $label): ?>
        <a href="/dashboard/tecnico/solicitudes<?= $val !== '' ? '?estado=' . urlencode($val) : '' ?>"
           class="whitespace-nowrap px-4 py-2 rounded-full text-sm font-medium transition-colors
                  <?= $estadoFiltro === $val
                      ? 'bg-primary-container text-white shadow-sm'
                      : 'bg-surface-container-lowest border border-outline-variant text-on-surface-variant hover:bg-secondary-container' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Tabla de solicitudes -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 overflow-hidden"
     style="box-shadow:0 1px 3px rgba(0,0,0,.06)">

    <?php if (empty($solicitudes)): ?>
    <div class="p-16 text-center">
        <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-3 block">assignment</span>
        <p class="text-on-surface-variant text-sm">No tienes solicitudes<?= $estadoFiltro !== '' ? ' con este estado' : ' aún' ?>.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/20 text-xs text-on-surface-variant uppercase tracking-wider">
                    <th class="px-5 py-3 font-medium">Cliente</th>
                    <th class="px-5 py-3 font-medium">Solicitud</th>
                    <th class="px-5 py-3 font-medium hidden md:table-cell">Dirección</th>
                    <th class="px-5 py-3 font-medium hidden sm:table-cell">Fecha</th>
                    <th class="px-5 py-3 font-medium">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 text-sm">
                <?php foreach ($solicitudes as $sol): ?>
                <?php
                    $clNombre = htmlspecialchars($sol['cliente_nombre'] ?? 'Cliente');
                    $clFoto   = $sol['cliente_foto'] ?? null;
                    $clPartes = explode(' ', trim($clNombre));
                    $clInic   = strtoupper(substr($clPartes[0], 0, 1) . (isset($clPartes[1]) ? substr($clPartes[1], 0, 1) : ''));
                    $solEs    = $sol['estado'];
                    $bClass   = $estadoBadge[$solEs] ?? 'bg-surface-container text-on-surface';
                    $bLabel   = $estadoLabel[$solEs] ?? ucfirst($solEs);
                ?>
                <tr class="hover:bg-surface-container-low transition-colors cursor-pointer"
                    onclick="window.location='/dashboard/tecnico/solicitudes/<?= (int) $sol['id'] ?>'">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center font-bold text-xs text-on-surface-variant flex-shrink-0 overflow-hidden">
                                <?php if ($clFoto): ?>
                                    <img src="<?= htmlspecialchars($clFoto) ?>" alt="<?= $clNombre ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= $clInic ?>
                                <?php endif; ?>
                            </div>
                            <span class="font-medium text-on-surface"><?= $clNombre ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-on-surface font-medium max-w-[200px] truncate">
                        <?= htmlspecialchars($sol['titulo']) ?>
                    </td>
                    <td class="px-5 py-4 text-on-surface-variant hidden md:table-cell max-w-[180px] truncate">
                        <?= htmlspecialchars($sol['direccion']) ?>
                    </td>
                    <td class="px-5 py-4 text-on-surface-variant whitespace-nowrap hidden sm:table-cell">
                        <?= date('d M, Y', strtotime($sol['fecha_creacion'])) ?>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $bClass ?>">
                            <?= $bLabel ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
