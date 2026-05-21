<?php
$estadoConfig = [
    'pendiente'  => ['cls' => 'bg-secondary-container text-on-secondary-container border border-secondary-container/50', 'label' => 'Pendiente'],
    'activo'     => ['cls' => 'bg-primary-container/20 text-primary border border-primary/20',                          'label' => 'Aprobado'],
    'suspendido' => ['cls' => 'bg-error-container text-on-error-container border border-error-container/50',            'label' => 'Suspendido'],
    'rechazado'  => ['cls' => 'bg-surface-container text-on-surface-variant border border-outline-variant/50',          'label' => 'Rechazado'],
];
?>

<!-- Page header -->
<div class="mb-6">
    <h1 class="text-headline-lg font-headline-lg text-on-surface mb-1">Gestión de Técnicos</h1>
    <p class="text-body-md font-body-md text-on-surface-variant">
        Revisa y aprueba las solicitudes de técnicos
    </p>
</div>

<!-- Filtros -->
<form method="GET" action="/dashboard/admin/tecnicos"
      class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 mb-6">
    <div class="flex flex-wrap items-center gap-3 w-full">

        <!-- Estado -->
        <select name="estado"
                onchange="this.form.submit()"
                class="px-4 pr-8 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md text-on-surface hover:bg-surface-container transition-colors shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <option value="" <?= ($estadoFilter ?? '') === '' ? 'selected' : '' ?>>Todos los estados</option>
            <?php foreach (['pendiente' => 'Pendiente', 'activo' => 'Aprobado', 'suspendido' => 'Suspendido', 'rechazado' => 'Rechazado'] as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($estadoFilter ?? '') === $val ? 'selected' : '' ?>>
                <?= $lbl ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Zona / Departamento -->
        <input name="zona"
               type="text"
               value="<?= htmlspecialchars($zonaFilter ?? '') ?>"
               placeholder="Zona o departamento..."
               class="px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm placeholder:text-outline w-48">

        <!-- Búsqueda nombre/correo -->
        <div class="relative flex-1 min-w-[200px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
            <input name="q"
                   type="text"
                   value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Buscar por nombre o correo..."
                   class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm placeholder:text-outline">
        </div>

        <button type="submit"
                class="px-5 py-2 bg-primary text-on-primary text-label-md font-label-md rounded-lg hover:brightness-95 active:scale-[0.98] transition-all shadow-sm">
            Buscar
        </button>

        <?php if (!empty($search) || !empty($estadoFilter) || !empty($zonaFilter)): ?>
        <a href="/dashboard/admin/tecnicos"
           class="px-5 py-2 bg-surface-container text-on-surface-variant text-label-md font-label-md rounded-lg hover:bg-surface-container-high transition-all shadow-sm">
            Limpiar
        </a>
        <?php endif; ?>
    </div>
</form>

<?php if (empty($applications)): ?>
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 p-12 text-center shadow-sm">
    <span class="material-symbols-outlined text-5xl text-outline block mb-3">engineering</span>
    <p class="text-body-md font-body-md text-on-surface-variant">No hay solicitudes de técnicos por revisar.</p>
</div>
<?php else: ?>

<!-- Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-low border-b border-outline-variant/50 text-label-md font-label-md text-on-surface-variant">
                <th class="py-4 px-5 font-medium">Solicitante</th>
                <th class="py-4 px-5 font-medium">Estado actual</th>
                <th class="py-4 px-5 font-medium">Zona de cobertura</th>
                <th class="py-4 px-5 font-medium">Documentos</th>
                <th class="py-4 px-5 font-medium">Comentario</th>
                <th class="py-4 px-5 font-medium">Actualizar</th>
            </tr>
        </thead>
        <tbody class="text-body-md font-body-md text-on-surface divide-y divide-outline-variant/30">
            <?php foreach ($applications as $app): ?>
            <tr class="hover:bg-surface-container/30 transition-colors">
                <!-- Solicitante -->
                <td class="py-4 px-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
                            <span class="text-on-primary-container font-bold text-xs">
                                <?= mb_strtoupper(mb_substr($app['nombre'], 0, 1)) ?>
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-on-surface"><?= htmlspecialchars($app['nombre']) ?></p>
                            <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($app['correo']) ?></p>
                            <?php if ($app['telefono']): ?>
                            <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($app['telefono']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <!-- Estado badge -->
                <td class="py-4 px-5">
                    <?php
                    $cfg = $estadoConfig[$app['estado']] ?? ['cls' => 'bg-surface-container text-on-surface-variant', 'label' => $app['estado']];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-label-md font-label-md <?= $cfg['cls'] ?>">
                        <?= $cfg['label'] ?>
                    </span>
                </td>
                <!-- Zona -->
                <td class="py-4 px-5 text-on-surface-variant text-label-md">
                    <?= htmlspecialchars($app['zona_cobertura'] ?? '—') ?>
                </td>
                <!-- Documentos -->
                <td class="py-4 px-5 text-on-surface-variant text-label-md max-w-[200px] truncate">
                    <?= htmlspecialchars($app['documentos_verificacion'] ?? '—') ?>
                </td>
                <!-- Comentario admin actual -->
                <td class="py-4 px-5 text-on-surface-variant text-label-md max-w-[180px] truncate"
                    title="<?= htmlspecialchars($app['comentario_admin'] ?? '') ?>">
                    <?= htmlspecialchars($app['comentario_admin'] ?? '—') ?>
                </td>
                <!-- Form de actualización -->
                <td class="py-4 px-5">
                    <form action="/dashboard/admin/tecnicos/<?= (int) $app['user_id'] ?>/estado" method="POST"
                          class="flex flex-col gap-2 min-w-[200px]">
                        <input type="hidden" name="_back_url" value="/dashboard/admin/tecnicos">

                        <select name="estado"
                                class="px-3 py-1.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-label-md font-label-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            <?php foreach (['pendiente', 'activo', 'suspendido', 'rechazado'] as $estado): ?>
                            <option value="<?= $estado ?>"
                                <?= (($app['estado'] ?? '') === $estado ? 'selected' : '') ?>>
                                <?= ucfirst($estado) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text"
                               name="comentario_admin"
                               value="<?= htmlspecialchars($app['comentario_admin'] ?? '') ?>"
                               placeholder="Comentario (opcional)"
                               maxlength="500"
                               class="px-3 py-1.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-label-md font-label-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder:text-outline">

                        <button type="submit"
                                class="px-4 py-1.5 bg-primary text-on-primary text-label-md font-label-md rounded-lg hover:brightness-95 active:scale-[0.98] transition-all">
                            Actualizar
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="px-5 py-3 border-t border-outline-variant/30 text-label-md font-label-md text-on-surface-variant">
        <?= count($applications) ?> solicitud(es) encontrada(s)
    </div>
</div>
<?php endif; ?>
