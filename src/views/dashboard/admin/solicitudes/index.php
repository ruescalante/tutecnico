<?php
$estadoConfig = [
    'pendiente'    => ['cls' => 'bg-secondary-container text-on-secondary-container border border-secondary-container/50',  'label' => 'Pendiente'],
    'aceptada'     => ['cls' => 'bg-primary-container/20 text-primary border border-primary/20',                           'label' => 'Aceptada'],
    'en_progreso'  => ['cls' => 'bg-tertiary-container/20 text-tertiary border border-tertiary/20',                        'label' => 'En progreso'],
    'completada'   => ['cls' => 'bg-surface-container text-on-surface-variant border border-outline-variant/50',           'label' => 'Completada'],
    'cancelada'    => ['cls' => 'bg-error-container text-on-error-container border border-error-container/50',             'label' => 'Cancelada'],
];
?>

<!-- Page header -->
<div class="mb-8">
    <h1 class="text-headline-lg font-headline-lg text-on-surface mb-1">Solicitudes de Servicio</h1>
    <p class="text-body-md font-body-md text-on-surface-variant">
        Historial de todas las solicitudes registradas en la plataforma
    </p>
</div>

<!-- Filtros -->
<form method="GET" action="/dashboard/admin/solicitudes"
      class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-6">
    <div class="flex flex-wrap items-center gap-3 w-full">

        <!-- Estado -->
        <select name="estado"
                onchange="this.form.submit()"
                class="px-4 pr-8 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md text-on-surface hover:bg-surface-container transition-colors shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <option value="" <?= ($estadoFilter ?? '') === '' ? 'selected' : '' ?>>Todos los estados</option>
            <?php foreach ([
                'pendiente'   => 'Pendiente',
                'aceptada'    => 'Aceptada',
                'en_progreso' => 'En progreso',
                'completada'  => 'Completada',
                'cancelada'   => 'Cancelada',
            ] as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($estadoFilter ?? '') === $val ? 'selected' : '' ?>>
                <?= $lbl ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Búsqueda -->
        <div class="relative flex-1 min-w-[200px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
            <input name="q"
                   type="text"
                   value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Buscar por título o cliente..."
                   class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm placeholder:text-outline">
        </div>

        <button type="submit"
                class="px-5 py-2 bg-primary text-on-primary text-label-md font-label-md rounded-lg hover:brightness-95 active:scale-[0.98] transition-all shadow-sm">
            Buscar
        </button>

        <?php if (!empty($search) || !empty($estadoFilter)): ?>
        <a href="/dashboard/admin/solicitudes"
           class="px-5 py-2 bg-surface-container text-on-surface-variant text-label-md font-label-md rounded-lg hover:bg-surface-container-high transition-all shadow-sm">
            Limpiar
        </a>
        <?php endif; ?>
    </div>
</form>

<?php if (empty($solicitudes)): ?>
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-12 text-center shadow-sm">
    <span class="material-symbols-outlined text-5xl text-outline block mb-3">assignment</span>
    <p class="text-body-md font-body-md text-on-surface-variant">No hay solicitudes registradas todavía.</p>
</div>
<?php else: ?>

<!-- Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant/50 text-label-md font-label-md text-on-surface-variant">
                <th class="py-4 px-5 font-medium">#</th>
                <th class="py-4 px-5 font-medium">Título</th>
                <th class="py-4 px-5 font-medium">Cliente</th>
                <th class="py-4 px-5 font-medium">Técnico</th>
                <th class="py-4 px-5 font-medium">Estado</th>
                <th class="py-4 px-5 font-medium">Fecha</th>
            </tr>
        </thead>
        <tbody class="text-body-md font-body-md text-on-surface divide-y divide-outline-variant/30">
            <?php foreach ($solicitudes as $sol): ?>
            <tr class="hover:bg-surface-container/30 transition-colors">
                <td class="py-3 px-5 text-on-surface-variant text-label-md"><?= (int) $sol['id'] ?></td>

                <td class="py-3 px-5 font-medium max-w-[220px] truncate"
                    title="<?= htmlspecialchars($sol['titulo']) ?>">
                    <?= htmlspecialchars($sol['titulo']) ?>
                </td>

                <td class="py-3 px-5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-secondary-container flex items-center justify-center flex-shrink-0">
                            <span class="text-on-secondary-container font-bold" style="font-size:10px;">
                                <?= mb_strtoupper(mb_substr($sol['cliente_nombre'], 0, 1)) ?>
                            </span>
                        </div>
                        <span class="text-on-surface text-label-md"><?= htmlspecialchars($sol['cliente_nombre']) ?></span>
                    </div>
                </td>

                <td class="py-3 px-5 text-on-surface-variant text-label-md">
                    <?= $sol['tecnico_nombre'] ? htmlspecialchars($sol['tecnico_nombre']) : '<span class="text-outline italic">Sin asignar</span>' ?>
                </td>

                <td class="py-3 px-5">
                    <?php
                    $cfg = $estadoConfig[$sol['estado']] ?? ['cls' => 'bg-surface-container text-on-surface-variant', 'label' => $sol['estado']];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-label-md font-label-md <?= $cfg['cls'] ?>">
                        <?= $cfg['label'] ?>
                    </span>
                </td>

                <td class="py-3 px-5 text-on-surface-variant text-label-md">
                    <?= htmlspecialchars(date('d/m/Y', strtotime($sol['fecha_creacion']))) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="px-5 py-3 border-t border-outline-variant/30 text-label-md font-label-md text-on-surface-variant">
        <?= count($solicitudes) ?> solicitud(es) encontrada(s)
    </div>
</div>
<?php endif; ?>
