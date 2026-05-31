<?php
/**
 * Buscar Técnicos — listado público con filtros y paginación.
 *
 * @var array  $tecnicos
 * @var array  $categorias
 * @var array  $filters     ['q','zona','categorias','disponible','min_rating','orden']
 * @var int    $total
 * @var int    $page
 * @var int    $totalPages
 * @var int    $perPage
 */

$selectedCats = $filters['categorias'] ?? [];
$ratingActive = (float) ($filters['min_rating'] ?? 0);
$ordenActual  = $filters['orden'] ?? 'rating';

$desde = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
$hasta = min($page * $perPage, $total);

// Construye un query string conservando los filtros actuales (con overrides).
$buildQs = function (array $overrides = []) use ($filters, $page): string {
    $base = [
        'q'          => $filters['q'],
        'zona'       => $filters['zona'],
        'categorias' => $filters['categorias'],
        'disponible' => $filters['disponible'],
        'min_rating' => $filters['min_rating'],
        'orden'      => $filters['orden'],
        'page'       => $page,
    ];
    $merged = array_merge($base, $overrides);
    $clean  = [];
    foreach ($merged as $k => $v) {
        if ($v === '' || $v === 0 || $v === 0.0 || $v === '0' || $v === [] || $v === null) continue;
        $clean[$k] = $v;
    }
    $qs = http_build_query($clean);
    return $qs === '' ? '/tecnicos' : '/tecnicos?' . $qs;
};

$tieneFiltros = $filters['q'] !== '' || $filters['zona'] !== '' || !empty($selectedCats)
    || !empty($filters['disponible']) || $ratingActive > 0;

// Renderiza una fila de 5 estrellas según una puntuación 0..5.
$renderEstrellas = function (float $valor, string $size = 'text-[16px]') {
    $html  = '';
    $valor = max(0, min(5, $valor));
    for ($i = 1; $i <= 5; $i++) {
        if ($valor >= $i) {
            $html .= '<span class="material-symbols-outlined ' . $size . ' text-yellow-500" style="font-variation-settings:\'FILL\' 1;">star</span>';
        } elseif ($valor >= $i - 0.5) {
            $html .= '<span class="material-symbols-outlined ' . $size . ' text-yellow-500" style="font-variation-settings:\'FILL\' 1;">star_half</span>';
        } else {
            $html .= '<span class="material-symbols-outlined ' . $size . ' text-outline-variant">star</span>';
        }
    }
    return $html;
};
?>

<div class="max-w-container-max-width mx-auto w-full px-margin-mobile md:px-margin-desktop py-8 md:py-10">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Buscar Técnicos</h1>
            <p class="font-body-md text-on-surface-variant mt-1">
                Encuentra al profesional ideal para tu proyecto.
            </p>
        </div>
        <span class="text-sm text-on-surface-variant whitespace-nowrap">
            Mostrando <span class="font-bold text-on-surface"><?= $total ?></span>
            <?= $total === 1 ? 'técnico' : 'técnicos' ?>
        </span>
    </div>

    <form method="GET" action="/tecnicos" id="filtrosForm">
        <?php if ($ordenActual !== ''): ?>
            <input type="hidden" name="orden" id="ordenInput" value="<?= htmlspecialchars($ordenActual) ?>">
        <?php endif; ?>

        <!-- Barra de búsqueda -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-3 md:p-4 shadow-sm mb-6">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">search</span>
                    <input type="text" name="q" value="<?= htmlspecialchars($filters['q']) ?>"
                           placeholder="¿Qué necesitas? Ej. electricista, plomería..."
                           class="w-full bg-surface border-none rounded-xl py-3 pl-12 pr-4 text-on-surface placeholder:text-on-surface-variant focus:ring-2 focus:ring-primary" />
                </div>
                <div class="relative md:w-64">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">location_on</span>
                    <input type="text" name="zona" value="<?= htmlspecialchars($filters['zona']) ?>"
                           placeholder="Ubicación o zona"
                           class="w-full bg-surface border-none rounded-xl py-3 pl-12 pr-4 text-on-surface placeholder:text-on-surface-variant focus:ring-2 focus:ring-primary" />
                </div>
                <button type="submit"
                        class="bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md px-8 py-3 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                    Buscar
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

            <!-- Sidebar de filtros -->
            <aside class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 lg:sticky lg:top-24">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-headline-md text-lg text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">tune</span> Filtros
                    </h2>
                    <?php if ($tieneFiltros): ?>
                        <a href="/tecnicos" class="text-xs font-medium text-primary hover:underline">Restablecer</a>
                    <?php endif; ?>
                </div>

                <!-- Categorías -->
                <div class="border-t border-outline-variant pt-4">
                    <h3 class="font-label-md text-label-md text-on-surface mb-3 uppercase tracking-wide text-xs">Categoría</h3>
                    <div class="flex flex-col gap-2 max-h-64 overflow-y-auto pr-1">
                        <?php foreach ($categorias as $cat):
                            $checked = in_array((int) $cat['id'], $selectedCats, true); ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="categorias[]" value="<?= (int) $cat['id'] ?>"
                                       class="filtro-auto rounded border-outline-variant text-primary focus:ring-primary w-4 h-4"
                                       <?= $checked ? 'checked' : '' ?> />
                                <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Calificación mínima -->
                <div class="border-t border-outline-variant pt-4 mt-4">
                    <h3 class="font-label-md text-label-md text-on-surface mb-3 uppercase tracking-wide text-xs">Calificación mínima</h3>
                    <div class="flex flex-col gap-1">
                        <?php foreach ([5, 4, 3, 0] as $r): ?>
                            <label class="flex items-center gap-3 cursor-pointer group py-1">
                                <input type="radio" name="min_rating" value="<?= $r ?>"
                                       class="filtro-auto text-primary focus:ring-primary w-4 h-4 border-outline-variant"
                                       <?= ((float) $r === $ratingActive) ? 'checked' : '' ?> />
                                <?php if ($r > 0): ?>
                                    <span class="flex items-center gap-1 text-sm text-on-surface-variant group-hover:text-on-surface">
                                        <span class="material-symbols-outlined text-[16px] text-yellow-500" style="font-variation-settings:'FILL' 1;">star</span>
                                        <?= $r ?> o más
                                    </span>
                                <?php else: ?>
                                    <span class="text-sm text-on-surface-variant group-hover:text-on-surface">Cualquiera</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Disponibilidad -->
                <div class="border-t border-outline-variant pt-4 mt-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-primary">bolt</span>
                            Disponibilidad inmediata
                        </span>
                        <input type="checkbox" name="disponible" value="1"
                               class="filtro-auto rounded border-outline-variant text-primary focus:ring-primary w-5 h-5"
                               <?= !empty($filters['disponible']) ? 'checked' : '' ?> />
                    </label>
                </div>

                <button type="submit"
                        class="w-full mt-5 bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md py-2.5 rounded-xl transition-colors active:scale-95">
                    Aplicar filtros
                </button>
            </aside>

            <!-- Resultados -->
            <div>
                <!-- Barra de orden -->
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-on-surface-variant">
                        <?php if ($total > 0): ?>
                            Resultados <span class="font-semibold text-on-surface"><?= $desde ?>–<?= $hasta ?></span> de <?= $total ?>
                        <?php else: ?>
                            Sin resultados
                        <?php endif; ?>
                    </p>
                    <label class="flex items-center gap-2 text-sm text-on-surface-variant">
                        <span class="hidden sm:inline">Ordenar por</span>
                        <select id="ordenSelect"
                                class="bg-surface-container-lowest border border-outline-variant rounded-lg py-1.5 pl-3 pr-8 text-sm text-on-surface focus:ring-2 focus:ring-primary">
                            <option value="rating"    <?= $ordenActual === 'rating'    ? 'selected' : '' ?>>Mejor calificados</option>
                            <option value="resenas"   <?= $ordenActual === 'resenas'   ? 'selected' : '' ?>>Más reseñas</option>
                            <option value="servicios" <?= $ordenActual === 'servicios' ? 'selected' : '' ?>>Más servicios</option>
                            <option value="nombre"    <?= $ordenActual === 'nombre'    ? 'selected' : '' ?>>Nombre (A-Z)</option>
                        </select>
                    </label>
                </div>

                <?php if (empty($tecnicos)): ?>
                    <!-- Estado vacío -->
                    <div class="bg-surface-container-lowest border border-dashed border-outline-variant rounded-2xl py-16 px-6 flex flex-col items-center text-center">
                        <span class="material-symbols-outlined text-6xl text-outline mb-4">person_search</span>
                        <h3 class="font-headline-md text-lg text-on-surface mb-2">No encontramos técnicos</h3>
                        <p class="text-sm text-on-surface-variant max-w-sm mb-6">
                            Intenta ajustar o quitar algunos filtros para ampliar tu búsqueda.
                        </p>
                        <a href="/tecnicos" class="bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md px-6 py-2.5 rounded-xl transition-colors active:scale-95">
                            Ver todos los técnicos
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($tecnicos as $tec):
                            $cats      = $tec['categorias'] ? explode(', ', $tec['categorias']) : [];
                            $rating    = number_format((float) $tec['avg_rating'], 1);
                            $reviews   = (int) $tec['review_count'];
                            $servicios = (int) $tec['service_count'];
                            $inicial   = mb_strtoupper(mb_substr($tec['nombre'], 0, 1));
                            $ubicacion = $tec['ciudad'] ?: ($tec['zona_cobertura'] ?: $tec['pais']);
                        ?>
                        <article class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 md:p-5 flex flex-col sm:flex-row gap-4 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                            <!-- Foto -->
                            <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden flex-shrink-0 mx-auto sm:mx-0">
                                <?php if (!empty($tec['foto_perfil'])): ?>
                                    <img alt="Foto de <?= htmlspecialchars($tec['nombre']) ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars($tec['foto_perfil']) ?>" />
                                <?php else: ?>
                                    <div class="w-full h-full bg-primary-container flex items-center justify-center text-on-primary text-2xl font-bold"><?= $inicial ?></div>
                                <?php endif; ?>
                                <span class="absolute top-1 right-1 bg-surface-container-lowest/90 rounded-full p-0.5 shadow-sm">
                                    <span class="material-symbols-outlined text-primary text-[16px]" style="font-variation-settings:'FILL' 1;">verified</span>
                                </span>
                            </div>

                            <!-- Info -->
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h3 class="font-headline-md text-body-lg font-bold text-on-surface truncate"><?= htmlspecialchars($tec['nombre']) ?></h3>
                                    <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings:'FILL' 1;" title="Verificado">verified</span>
                                </div>

                                <div class="flex items-center gap-2 mt-1 mb-2">
                                    <div class="flex items-center"><?= $renderEstrellas((float) $tec['avg_rating']) ?></div>
                                    <span class="text-sm font-bold text-on-surface"><?= $rating ?></span>
                                    <span class="text-xs text-on-surface-variant"><?= $reviews ?> <?= $reviews === 1 ? 'reseña' : 'reseñas' ?></span>
                                </div>

                                <p class="text-sm text-on-surface-variant mb-3">
                                    <?= $cats ? htmlspecialchars(implode(' · ', array_slice($cats, 0, 2))) : 'Técnico profesional' ?>
                                </p>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-on-surface-variant">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                                        <?= htmlspecialchars($ubicacion) ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">task_alt</span>
                                        <?= $servicios ?> <?= $servicios === 1 ? 'servicio' : 'servicios' ?>
                                    </span>
                                    <?php if (!empty($tec['disponibilidad'])): ?>
                                        <span class="flex items-center gap-1 text-primary font-semibold">
                                            <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                                            Disponible
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Acción -->
                            <div class="flex sm:flex-col justify-between sm:justify-center items-stretch sm:items-end gap-2 sm:w-36 flex-shrink-0 sm:border-l sm:border-outline-variant sm:pl-4">
                                <a href="/tecnico/<?= (int) $tec['id'] ?>"
                                   class="flex-1 sm:flex-none sm:w-full text-center bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md py-2.5 px-4 rounded-xl transition-all active:scale-95 shadow-sm">
                                    Ver Perfil
                                </a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Paginación -->
                    <?php if ($totalPages > 1): ?>
                    <nav class="flex items-center justify-center gap-1.5 mt-8" aria-label="Paginación">
                        <?php if ($page > 1): ?>
                            <a href="<?= htmlspecialchars($buildQs(['page' => $page - 1])) ?>"
                               class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-low hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <span class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-outline-variant cursor-not-allowed">
                                <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                            </span>
                        <?php endif; ?>

                        <?php
                        $start = max(1, $page - 2);
                        $end   = min($totalPages, $start + 4);
                        $start = max(1, $end - 4);
                        for ($p = $start; $p <= $end; $p++):
                            $isCurrent = $p === $page; ?>
                            <a href="<?= htmlspecialchars($buildQs(['page' => $p])) ?>"
                               class="min-w-10 h-10 px-3 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $isCurrent
                                   ? 'bg-primary text-on-primary'
                                   : 'border border-outline-variant text-on-surface-variant hover:bg-surface-container-low hover:text-primary' ?>"
                               <?= $isCurrent ? 'aria-current="page"' : '' ?>>
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?= htmlspecialchars($buildQs(['page' => $page + 1])) ?>"
                               class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-low hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <span class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-outline-variant cursor-not-allowed">
                                <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                            </span>
                        <?php endif; ?>
                    </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    var form = document.getElementById('filtrosForm');
    if (!form) return;

    // Auto-enviar al cambiar checkboxes/radios de la barra lateral.
    form.querySelectorAll('.filtro-auto').forEach(function (el) {
        el.addEventListener('change', function () { form.submit(); });
    });

    // El <select> de orden sincroniza el input oculto y reenvía.
    var ordenSelect = document.getElementById('ordenSelect');
    var ordenInput  = document.getElementById('ordenInput');
    if (ordenSelect) {
        ordenSelect.addEventListener('change', function () {
            if (ordenInput) {
                ordenInput.value = ordenSelect.value;
            } else {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'orden';
                hidden.value = ordenSelect.value;
                form.appendChild(hidden);
            }
            form.submit();
        });
    }
})();
</script>
