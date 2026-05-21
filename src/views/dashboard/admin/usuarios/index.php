<?php
$rolLabels = ['cliente' => 'Cliente', 'tecnico' => 'Técnico', 'admin' => 'Admin'];
?>

<!-- Page header -->
<div class="mb-8">
    <h1 class="text-headline-lg font-headline-lg text-on-surface mb-1">Panel de Administrador</h1>
    <p class="text-body-md font-body-md text-on-surface-variant">Administra usuarios, técnicos y solicitudes de cotización</p>
</div>

<!-- Stat cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 px-6 py-5 shadow-sm">
        <p class="text-label-md font-label-md text-on-surface-variant mb-1">Clientes</p>
        <p class="text-headline-lg font-headline-lg text-on-surface"><?= (int) ($stats['clientes'] ?? 0) ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 px-6 py-5 shadow-sm">
        <p class="text-label-md font-label-md text-on-surface-variant mb-1">Técnicos</p>
        <p class="text-headline-lg font-headline-lg text-on-surface"><?= (int) ($stats['tecnicos'] ?? 0) ?></p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 px-6 py-5 shadow-sm">
        <p class="text-label-md font-label-md text-on-surface-variant mb-1">Admins</p>
        <p class="text-headline-lg font-headline-lg text-on-surface"><?= (int) ($stats['admins'] ?? 0) ?></p>
    </div>
</div>

<!-- Filters & search -->
<form method="GET" action="/dashboard/admin/usuarios" class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
    <div class="flex flex-wrap items-center gap-3 w-full">
        <select name="estado"
                onchange="this.form.submit()"
                class="px-4 pr-8 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md text-on-surface hover:bg-surface-container transition-colors shadow-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <option value="" <?= ($statusFilter ?? '') === '' ? 'selected' : '' ?>>Todos los estados</option>
            <option value="activo"   <?= ($statusFilter ?? '') === 'activo'   ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= ($statusFilter ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>

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

        <?php if (!empty($search) || !empty($statusFilter)): ?>
        <a href="/dashboard/admin/usuarios"
           class="px-5 py-2 bg-surface-container text-on-surface-variant text-label-md font-label-md rounded-lg hover:bg-surface-container-high transition-all shadow-sm">
            Limpiar
        </a>
        <?php endif; ?>
    </div>
</form>

<!-- Table header -->
<div class="flex justify-between items-center mb-4">
    <h2 class="text-headline-md font-headline-md text-on-surface">Usuarios Registrados</h2>
    <span class="text-label-md font-label-md text-on-surface-variant"><?= count($users) ?> usuario(s)</span>
</div>

<!-- Users table -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-visible relative">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant/50 text-label-md font-label-md text-on-surface-variant">
                    <th class="py-4 px-6 font-medium">Nombre</th>
                    <th class="py-4 px-6 font-medium">Email</th>
                    <th class="py-4 px-6 font-medium">Teléfono</th>
                    <th class="py-4 px-6 font-medium">Registro</th>
                    <th class="py-4 px-6 font-medium">Rol</th>
                    <th class="py-4 px-6 font-medium">Estado</th>
                    <th class="py-4 px-6 font-medium text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-body-md font-body-md text-on-surface divide-y divide-outline-variant/30">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" class="py-12 text-center text-on-surface-variant text-label-md font-label-md">
                        <span class="material-symbols-outlined text-4xl block mb-2 text-outline">group_off</span>
                        No se encontraron usuarios
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-surface-container/30 transition-colors group">
                    <!-- Nombre con avatar inicial -->
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center">
                                    <span class="text-on-primary-container font-bold text-xs">
                                        <?= mb_strtoupper(mb_substr($user['nombre'], 0, 1)) ?>
                                    </span>
                                </div>
                                <?php if ($user['activo']): ?>
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-primary rounded-full border-2 border-surface-container-lowest"></div>
                                <?php endif; ?>
                            </div>
                            <span class="font-medium group-hover:text-primary transition-colors">
                                <?= htmlspecialchars($user['nombre']) ?>
                            </span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-on-surface-variant"><?= htmlspecialchars($user['correo']) ?></td>
                    <td class="py-3 px-6 text-on-surface-variant"><?= htmlspecialchars($user['telefono'] ?? '—') ?></td>
                    <td class="py-3 px-6 text-on-surface-variant">
                        <?= htmlspecialchars(date('d/m/Y', strtotime($user['fecha_registro']))) ?>
                    </td>
                    <!-- Rol badge -->
                    <td class="py-3 px-6">
                        <?php
                        $rolCls = match($user['rol']) {
                            'admin'   => 'bg-tertiary-container/20 text-tertiary border border-tertiary/20',
                            'tecnico' => 'bg-secondary-container text-on-secondary-container border border-secondary-container',
                            default   => 'bg-surface-container text-on-surface-variant border border-outline-variant/50',
                        };
                        ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-label-md font-label-md <?= $rolCls ?>">
                            <?= htmlspecialchars($rolLabels[$user['rol']] ?? $user['rol']) ?>
                        </span>
                    </td>
                    <!-- Estado badge -->
                    <td class="py-3 px-6">
                        <?php if ($user['activo']): ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-label-md font-label-md bg-primary-container/20 text-primary border border-primary/20">
                            Activo
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-label-md font-label-md bg-error-container text-on-error-container border border-error-container/50">
                            Inactivo
                        </span>
                        <?php endif; ?>
                    </td>
                    <!-- Actions dropdown -->
                    <td class="py-3 px-6 text-right relative">
                        <button data-dropdown-toggle="dd-<?= (int) $user['id'] ?>"
                                class="p-1 rounded hover:bg-surface-container text-on-surface-variant transition-colors">
                            <span class="material-symbols-outlined">more_horiz</span>
                        </button>

                        <div id="dd-<?= (int) $user['id'] ?>"
                             data-dropdown
                             class="hidden absolute right-10 top-2 mt-1 w-48 bg-surface-container-lowest rounded-lg shadow-[0_8px_24px_rgba(0,32,27,0.12)] border border-outline-variant/30 py-1 z-30 flex-col text-left">

                            <a href="/dashboard/admin/usuarios/<?= (int) $user['id'] ?>/editar"
                               class="w-full text-left px-4 py-2 text-label-md font-label-md text-on-surface flex items-center gap-2 hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-sm text-on-surface-variant">edit</span>
                                Editar
                            </a>

                            <form action="/dashboard/admin/usuarios/<?= (int) $user['id'] ?>/suspender" method="POST">
                                <input type="hidden" name="_back_url" value="/dashboard/admin/usuarios">
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-label-md font-label-md text-on-surface flex items-center gap-2 hover:bg-surface-container transition-colors">
                                    <span class="material-symbols-outlined text-sm text-on-surface-variant"
                                          style="font-variation-settings: 'FILL' 1;">
                                        <?= $user['activo'] ? 'block' : 'check_circle' ?>
                                    </span>
                                    <?= $user['activo'] ? 'Suspender' : 'Reactivar' ?>
                                </button>
                            </form>

                            <form action="/dashboard/admin/usuarios/<?= (int) $user['id'] ?>/eliminar" method="POST"
                                  onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars(addslashes($user['nombre'])) ?>? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="_back_url" value="/dashboard/admin/usuarios">
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-label-md font-label-md text-error flex items-center gap-2 hover:bg-surface-container transition-colors">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">delete</span>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer de tabla -->
    <?php if (!empty($users)): ?>
    <div class="px-6 py-3 bg-surface-container-lowest border-t border-outline-variant/30 text-label-md font-label-md text-on-surface-variant">
        Mostrando <?= count($users) ?> usuario(s)
    </div>
    <?php endif; ?>
</div>
