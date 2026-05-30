<?php
    $tecnicoId   = (int) $tecnico['id'];
    $nombre      = htmlspecialchars($tecnico['nombre'] ?? 'Técnico');
    $foto        = $tecnico['foto_perfil'] ?? null;
    $rating      = isset($tecnico['avg_rating']) && $tecnico['avg_rating'] > 0
                       ? number_format((float) $tecnico['avg_rating'], 1)
                       : null;
    $totalRes    = (int) ($tecnico['total_resenas'] ?? 0);
    $partes      = explode(' ', trim($nombre));
    $iniciales   = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

    $oldTitulo      = htmlspecialchars($old['titulo'] ?? '');
    $oldDescripcion = htmlspecialchars($old['descripcion'] ?? '');
    $oldDireccion   = htmlspecialchars($old['direccion'] ?? '');
?>

<div class="max-w-3xl mx-auto px-4 md:px-8 py-8 md:py-12 flex flex-col gap-8">

    <!-- Back nav + título -->
    <div class="flex items-center gap-3">
        <a href="/tecnico/<?= $tecnicoId ?>"
           class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors flex items-center">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="font-semibold text-xl text-on-surface">Solicitar Servicio</h1>
            <p class="text-sm text-on-surface-variant">a <?= $nombre ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

    <!-- Card del técnico -->
    <div class="md:col-span-1 space-y-6">
        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 flex flex-col items-center text-center"
             style="box-shadow:0 1px 3px rgba(0,0,0,.08)">
            <div class="relative mb-4">
                <?php if ($foto): ?>
                    <img src="<?= htmlspecialchars($foto) ?>"
                         alt="<?= $nombre ?>"
                         class="w-24 h-24 rounded-full object-cover border-4 border-surface-container">
                <?php else: ?>
                    <div class="w-24 h-24 rounded-full bg-[#e6f4f1] flex items-center justify-center text-[#00796b] font-bold text-3xl border-4 border-surface-container">
                        <?= $iniciales ?>
                    </div>
                <?php endif; ?>
                <span class="material-symbols-outlined text-[#00796b] absolute bottom-1 right-1 bg-surface-container-lowest rounded-full p-0.5 text-lg"
                      style="font-variation-settings:'FILL' 1;">verified</span>
            </div>

            <h2 class="font-headline-md text-lg font-semibold text-on-surface mb-1"><?= $nombre ?></h2>

            <?php if (!empty($categorias)): ?>
                <p class="text-sm text-on-surface-variant mb-3">
                    <?= htmlspecialchars(implode(', ', array_column(array_slice($categorias, 0, 2), 'nombre'))) ?>
                </p>
            <?php endif; ?>

            <?php if ($rating !== null): ?>
                <div class="flex items-center gap-1 justify-center">
                    <span class="material-symbols-outlined text-amber-400 text-base" style="font-variation-settings:'FILL' 1;">star</span>
                    <span class="font-semibold text-on-surface text-sm"><?= $rating ?></span>
                    <?php if ($totalRes > 0): ?>
                        <span class="text-xs text-on-surface-variant">(<?= $totalRes ?> <?= $totalRes === 1 ? 'reseña' : 'reseñas' ?>)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Resumen de cotización -->
        <div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/20">
            <h3 class="font-semibold text-on-surface mb-4 flex items-center gap-2 text-base">
                <span class="material-symbols-outlined text-primary text-xl">receipt_long</span>
                Resumen
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-2 border-b border-outline-variant/20">
                    <span class="text-on-surface-variant">Precio Estimado</span>
                    <span class="bg-surface-container text-on-surface-variant px-2 py-0.5 rounded text-xs font-medium">Por definir</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-outline-variant/20">
                    <span class="text-on-surface-variant">Estado</span>
                    <span class="bg-secondary-container text-on-surface-variant px-2 py-0.5 rounded text-xs font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">pending</span> Pendiente
                    </span>
                </div>
                <p class="text-xs text-on-surface-variant text-center italic mt-2">
                    El técnico enviará una cotización basada en tu solicitud.
                </p>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="md:col-span-2">
        <form method="POST" action="/solicitudes/crear"
              class="bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 space-y-6"
              style="box-shadow:0 1px 3px rgba(0,0,0,.08)">

            <h2 class="font-semibold text-xl text-on-surface border-b border-outline-variant/20 pb-4">
                Detalles de la Solicitud
            </h2>

            <input type="hidden" name="id_tecnico" value="<?= $tecnicoId ?>">
            <input type="hidden" name="_back_url" value="/solicitudes/crear/<?= $tecnicoId ?>">

            <!-- Título -->
            <div>
                <label class="block text-sm font-medium text-on-surface mb-2" for="titulo">
                    Título del Problema <span class="text-error">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">build</span>
                    <input type="text"
                           id="titulo"
                           name="titulo"
                           value="<?= $oldTitulo ?>"
                           placeholder="Ej. Reparación de grifo que gotea"
                           maxlength="200"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border <?= !empty($errors['titulo']) ? 'border-error' : 'border-outline-variant' ?> bg-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container/40 outline-none transition-colors text-sm text-on-surface">
                </div>
                <?php if (!empty($errors['titulo'])): ?>
                    <p class="text-error text-xs mt-1"><?= htmlspecialchars($errors['titulo'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-medium text-on-surface mb-2" for="descripcion">
                    Descripción Detallada <span class="text-error">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-4 text-on-surface-variant">description</span>
                    <textarea id="descripcion"
                              name="descripcion"
                              rows="4"
                              placeholder="Describe el problema con el mayor detalle posible..."
                              class="w-full pl-10 pr-4 py-3 rounded-lg border <?= !empty($errors['descripcion']) ? 'border-error' : 'border-outline-variant' ?> bg-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container/40 outline-none transition-colors text-sm text-on-surface resize-none"><?= $oldDescripcion ?></textarea>
                </div>
                <?php if (!empty($errors['descripcion'])): ?>
                    <p class="text-error text-xs mt-1"><?= htmlspecialchars($errors['descripcion'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Dirección -->
            <div>
                <label class="block text-sm font-medium text-on-surface mb-2" for="direccion">
                    Dirección del Servicio <span class="text-error">*</span>
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">location_on</span>
                    <input type="text"
                           id="direccion"
                           name="direccion"
                           value="<?= $oldDireccion ?>"
                           placeholder="Ingresa la dirección donde necesitas el servicio"
                           maxlength="300"
                           class="w-full pl-10 pr-4 py-3 rounded-lg border <?= !empty($errors['direccion']) ? 'border-error' : 'border-outline-variant' ?> bg-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container/40 outline-none transition-colors text-sm text-on-surface">
                </div>
                <?php if (!empty($errors['direccion'])): ?>
                    <p class="text-error text-xs mt-1"><?= htmlspecialchars($errors['direccion'][0]) ?></p>
                <?php endif; ?>
            </div>

            <!-- Acciones -->
            <div class="pt-4 border-t border-outline-variant/20 flex flex-col sm:flex-row gap-3 justify-end">
                <a href="/tecnico/<?= $tecnicoId ?>"
                   class="px-6 py-3 rounded-lg font-semibold text-sm text-primary bg-secondary-container hover:bg-surface-variant transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-8 py-3 rounded-lg font-semibold text-sm text-white bg-primary-container hover:bg-primary transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-xl">send</span>
                    Enviar Solicitud
                </button>
            </div>
        </form>
    </div>

</div>
