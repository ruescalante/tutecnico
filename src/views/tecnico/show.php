<?php
    $nombre      = htmlspecialchars($tecnico['nombre'] ?? 'Técnico');
    $correo      = htmlspecialchars($tecnico['correo'] ?? '');
    $telefono    = htmlspecialchars($tecnico['telefono'] ?? '');
    $ciudad      = htmlspecialchars($tecnico['ciudad'] ?? '');
    $pais        = htmlspecialchars($tecnico['pais'] ?? 'El Salvador');
    $ubicacion   = trim($ciudad . ($ciudad && $pais ? ', ' : '') . $pais);
    $descripcion = htmlspecialchars($tecnico['descripcion'] ?? '');
    $zona        = htmlspecialchars($tecnico['zona_cobertura'] ?? '');
    $foto        = $tecnico['foto_perfil'] ?? null;
    $partes      = explode(' ', trim($nombre));
    $iniciales   = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
    $rating       = $avgRating > 0 ? $avgRating : null;
    $totalResenas = count($resenas);
?>

<main class="max-w-5xl mx-auto px-4 md:px-8 py-8 md:py-12 flex flex-col gap-8">

    <?php if (!empty($success)): ?>
    <div class="bg-[#e6f4f1] border border-[#00796b]/30 text-[#00796b] px-5 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
        <span class="font-body-md text-sm"><?= htmlspecialchars($success) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════
         Hero Section
    ════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- Main profile card -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 relative overflow-hidden"
             style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">
            <div class="absolute inset-0 bg-gradient-to-br from-surface-container-low to-transparent opacity-50 pointer-events-none"></div>

            <div class="relative flex flex-col md:flex-row gap-8 items-start">

                <!-- Avatar -->
                <?php if ($foto): ?>
                    <img src="<?= htmlspecialchars($foto) ?>"
                         alt="<?= $nombre ?>"
                         class="w-32 h-32 md:w-40 md:h-40 rounded-lg object-cover flex-shrink-0 shadow-sm border border-outline-variant/10">
                <?php else: ?>
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-lg flex-shrink-0 bg-[#e6f4f1] flex items-center justify-center text-[#00796b] font-bold text-4xl border border-outline-variant/10">
                        <?= $iniciales ?>
                    </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="flex-1 flex flex-col w-full justify-between gap-4">
                    <div>
                        <!-- Name + badge -->
                        <div class="flex items-center gap-2 mb-1">
                            <h1 class="font-headline-lg text-headline-lg text-on-surface"><?= $nombre ?></h1>
                            <span class="material-symbols-outlined text-[#00796b]"
                                  style="font-variation-settings:'FILL' 1;">check_circle</span>
                        </div>

                        <!-- Stars + rating -->
                        <div class="flex items-center gap-2 mb-3">
                            <?php if ($rating !== null): ?>
                                <div id="header-stars" class="flex text-amber-400 text-lg cursor-pointer"
                                     title="Ver reseñas">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="material-symbols-outlined header-star"
                                              data-value="<?= $i ?>"
                                              style="font-size:1.25rem;font-variation-settings:'FILL' <?= $i <= round($rating) ? '1' : '0' ?>;">star</span>
                                    <?php endfor; ?>
                                </div>
                                <span class="font-label-md text-[#00796b] font-semibold"><?= number_format($rating, 1) ?></span>
                                <span class="font-body-md text-on-surface-variant text-sm">(<?= $totalResenas ?> <?= $totalResenas === 1 ? 'reseña' : 'reseñas' ?>)</span>
                            <?php else: ?>
                                <span class="font-body-md text-on-surface-variant text-sm">Sin reseñas aún</span>
                            <?php endif; ?>
                            <span class="font-body-md text-on-surface-variant text-sm ml-1">· Verificado</span>
                        </div>

                        <!-- Specialty title from categories -->
                        <?php if (!empty($categorias)): ?>
                            <h2 class="font-headline-md text-lg font-semibold text-on-surface mb-3">
                                <?= implode(' y ', array_map(fn($c) => htmlspecialchars($c['nombre']), array_slice($categorias, 0, 2))) ?>
                            </h2>
                        <?php endif; ?>

                        <!-- Category chips -->
                        <div class="flex flex-wrap gap-2 mb-5">
                            <?php foreach ($categorias as $cat): ?>
                                <span class="bg-[#e6f4f1] text-[#00796b] px-3 py-1 rounded-md font-label-md text-sm">
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Contact + actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex flex-col gap-2 font-body-md text-sm text-on-surface-variant">
                            <?php if ($telefono): ?>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[#00796b] text-xl">call</span>
                                    <span><?= $telefono ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($correo): ?>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[#00796b] text-xl">mail</span>
                                    <span><?= $correo ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($ubicacion): ?>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[#00796b] text-xl">location_on</span>
                                    <span><?= $ubicacion ?></span>
                                </div>
                            <?php elseif ($zona): ?>
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[#00796b] text-xl">location_on</span>
                                    <span><?= $zona ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-col gap-3 w-full sm:w-auto items-end">
                            <button disabled
                                    class="bg-[#00796b] text-white w-full sm:w-48 px-6 py-3 rounded-lg font-label-md text-base font-semibold
                                           opacity-60 cursor-not-allowed shadow-sm"
                                    title="Disponible próximamente">
                                Solicitar Servicio
                            </button>
                            <div class="flex justify-end gap-2 text-[#00796b]">
                                <?php if ($correo): ?>
                                <a href="mailto:<?= $correo ?>"
                                   class="w-10 h-10 flex items-center justify-center bg-[#e6f4f1] rounded-lg hover:bg-surface-variant transition-colors">
                                    <span class="material-symbols-outlined text-lg">alternate_email</span>
                                </a>
                                <?php endif; ?>
                                <?php if ($telefono): ?>
                                <a href="tel:<?= $telefono ?>"
                                   class="w-10 h-10 flex items-center justify-center bg-[#e6f4f1] rounded-lg hover:bg-surface-variant transition-colors">
                                    <span class="material-symbols-outlined text-lg">call</span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side details card -->
        <div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/30 flex flex-col gap-6 self-stretch">
            <h3 class="font-headline-md text-xl font-semibold text-on-surface pb-3 border-b border-outline-variant/20">
                Detalles del Técnico
            </h3>
            <div class="flex flex-col gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">verified</span>
                    </div>
                    <span class="font-body-md text-base text-on-surface">Verificado</span>
                </div>
                <?php if ($zona): ?>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                        <span class="material-symbols-outlined">location_on</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-body-md text-base text-on-surface font-semibold"><?= $zona ?></span>
                        <span class="font-body-md text-sm text-outline">Zona de cobertura</span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                        <span class="material-symbols-outlined">event_available</span>
                    </div>
                    <span class="font-body-md text-base text-on-surface">
                        <?= $serviciosCount ?> <?= $serviciosCount === 1 ? 'servicio completado' : 'servicios completados' ?>
                    </span>
                </div>
                <?php if (!empty($categorias)): ?>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                        <span class="material-symbols-outlined">category</span>
                    </div>
                    <span class="font-body-md text-base text-on-surface">
                        <?= count($categorias) ?> <?= count($categorias) === 1 ? 'especialidad' : 'especialidades' ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if (!empty($fotos)): ?>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                        <span class="material-symbols-outlined">photo_library</span>
                    </div>
                    <span class="font-body-md text-base text-on-surface">
                        <?= count($fotos) ?> <?= count($fotos) === 1 ? 'foto de trabajo' : 'fotos de trabajos' ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         Sobre mí
    ════════════════════════════════════════════════ -->
    <?php if ($descripcion): ?>
    <section>
        <h3 class="font-headline-md text-xl font-semibold text-on-surface mb-3">Sobre mí</h3>
        <p class="font-body-md text-base text-on-surface-variant max-w-4xl leading-relaxed">
            <?= $descripcion ?>
        </p>
    </section>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════
         Fotos de trabajos
    ════════════════════════════════════════════════ -->
    <?php if (!empty($fotos)): ?>
    <section class="relative">
        <h3 class="font-headline-md text-xl font-semibold text-on-surface mb-4">Fotos de trabajos</h3>
        <div class="relative flex items-center">
            <button onclick="document.getElementById('gallery-scroll').scrollBy({left:-320,behavior:'smooth'})"
                    class="absolute left-0 z-10 w-9 h-9 rounded-full bg-surface-container-lowest shadow-sm border border-outline-variant/20
                           text-outline hover:text-[#00796b] flex items-center justify-center transition-colors -ml-4 hidden md:flex">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            <div id="gallery-scroll"
                 class="flex overflow-x-auto gap-4 pb-2 snap-x items-center w-full px-0 md:px-6"
                 style="-ms-overflow-style:none;scrollbar-width:none;">
                <?php foreach ($fotos as $f): ?>
                    <img src="<?= htmlspecialchars($f['url']) ?>"
                         alt="<?= htmlspecialchars($f['descripcion'] ?? 'Foto de trabajo') ?>"
                         data-modal-src="<?= htmlspecialchars($f['url']) ?>"
                         data-modal-alt="<?= htmlspecialchars($f['descripcion'] ?? 'Foto de trabajo') ?>"
                         class="gallery-img w-[280px] h-[180px] object-cover rounded-xl snap-center flex-shrink-0 border border-outline-variant/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-md cursor-pointer">
                <?php endforeach; ?>
            </div>
            <button onclick="document.getElementById('gallery-scroll').scrollBy({left:320,behavior:'smooth'})"
                    class="absolute right-0 z-10 w-9 h-9 rounded-full bg-surface-container-lowest shadow-sm border border-outline-variant/20
                           text-outline hover:text-[#00796b] flex items-center justify-center transition-colors -mr-4 hidden md:flex">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
        </div>
    </section>
    <style>#gallery-scroll::-webkit-scrollbar{display:none;}</style>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════════
         Reseñas
    ════════════════════════════════════════════════ -->
    <section id="resenas">
        <h3 class="font-headline-md text-xl font-semibold text-on-surface mb-4">
            Reseñas<?= $totalResenas > 0 ? ' (' . $totalResenas . ')' : '' ?>
        </h3>

        <?php
            $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
        ?>
        <?php if (!empty($resenas)): ?>
        <div class="relative flex items-center mb-8">
            <button id="reviews-prev"
                    class="absolute left-0 z-10 w-9 h-9 rounded-full bg-surface-container-lowest shadow-sm border border-outline-variant/20
                           text-outline hover:text-[#00796b] flex items-center justify-center transition-colors -ml-4 hidden md:flex">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            <div id="reviews-scroll"
                 class="flex overflow-x-auto gap-4 pb-2 snap-x w-full md:px-6"
                 style="-ms-overflow-style:none;scrollbar-width:none;">
                <?php foreach ($resenas as $r): ?>
                    <?php
                        $clienteNombre = htmlspecialchars($r['cliente_nombre'] ?? 'Cliente');
                        $clienteFoto   = $r['cliente_foto'] ?? null;
                        $partsC        = explode(' ', trim($clienteNombre));
                        $inicialesC    = strtoupper(substr($partsC[0], 0, 1) . (isset($partsC[1]) ? substr($partsC[1], 0, 1) : ''));
                        $fechaResena   = date('d M, Y', strtotime($r['fecha']));
                        $esMia         = ($currentUserId > 0 && (int) $r['id_cliente'] === $currentUserId);
                        $calId         = (int) $r['calificacion_id'];
                    ?>
                    <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20 flex flex-col gap-4 flex-shrink-0 snap-center w-[300px] md:w-[320px]"
                         style="box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.04)">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-[#e6f4f1] flex items-center justify-center font-bold overflow-hidden flex-shrink-0 text-[#00796b]">
                                    <?php if ($clienteFoto): ?>
                                        <img src="<?= htmlspecialchars($clienteFoto) ?>" alt="<?= $clienteNombre ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= $inicialesC ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <h4 class="font-label-md text-sm font-semibold text-on-surface"><?= $clienteNombre ?></h4>
                                    <div class="flex text-amber-400 mt-0.5" style="font-size:16px;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="material-symbols-outlined"
                                                  style="font-size:16px;font-variation-settings:'FILL' <?= $i <= $r['puntuacion'] ? '1' : '0' ?>;">star</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="font-body-md text-xs text-outline whitespace-nowrap"><?= $fechaResena ?></span>
                                <?php if ($esMia): ?>
                                <button class="review-dots-btn w-7 h-7 flex items-center justify-center rounded-full text-outline hover:text-on-surface hover:bg-surface-variant transition-colors"
                                        data-cal-id="<?= $calId ?>"
                                        data-tecnico-id="<?= (int) $tecnico['id'] ?>"
                                        title="Opciones">
                                    <span class="material-symbols-outlined" style="font-size:20px;">more_vert</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($r['comentario'])): ?>
                        <p class="font-body-md text-sm text-on-surface-variant leading-relaxed line-clamp-4">
                            <?= htmlspecialchars($r['comentario']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button id="reviews-next"
                    class="absolute right-0 z-10 w-9 h-9 rounded-full bg-surface-container-lowest shadow-sm border border-outline-variant/20
                           text-outline hover:text-[#00796b] flex items-center justify-center transition-colors -mr-4 hidden md:flex">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
        </div>
        <style>#reviews-scroll::-webkit-scrollbar{display:none;}</style>

        <!-- Dropdown global del menú de reseña (posicionado via JS) -->
        <div id="review-dropdown"
             class="hidden fixed z-50 bg-surface-container-lowest rounded-xl border border-outline-variant/20 py-1 min-w-[140px]"
             style="box-shadow:0 4px 20px rgba(0,0,0,.12)">
            <button id="review-edit-btn"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-variant transition-colors text-left">
                <span class="material-symbols-outlined text-[18px] text-[#00796b]">edit</span>
                Editar
            </button>
            <form id="review-delete-form" method="POST" class="m-0">
                <input type="hidden" name="calificacion_id" id="review-delete-cal-id">
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Eliminar
                </button>
            </form>
        </div>
        <?php else: ?>
        <p class="text-on-surface-variant font-body-md text-sm mb-8">Este técnico aún no tiene reseñas.</p>
        <?php endif; ?>

        <!-- ── Formulario de reseña ── -->
        <?php
            $isLoggedIn = !empty($_SESSION['user_id']);
            $canReview  = ($isLoggedIn
                && ($_SESSION['role'] ?? '') !== 'admin'
                && (int) ($_SESSION['user_id'] ?? 0) !== (int) $tecnico['id']);
        ?>

        <?php if (!$isLoggedIn): ?>
            <div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/20 text-center">
                <span class="material-symbols-outlined text-[#00796b] text-3xl block mb-2" style="font-variation-settings:'FILL' 1;">star</span>
                <p class="font-body-md text-on-surface-variant text-sm mb-3">¿Trabajaste con este técnico?</p>
                <a href="/login" class="inline-block bg-[#00796b] text-white px-5 py-2 rounded-lg font-label-md text-sm font-semibold hover:bg-[#006458] transition-colors">
                    Inicia sesión para calificar
                </a>
            </div>

        <?php elseif ($canReview && $unreviewedSolicitud): ?>
            <div id="form-resena" class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
                <h4 class="font-headline-md text-lg font-semibold text-on-surface mb-4">Deja tu reseña</h4>

                <?php if (!empty($errors['puntuacion'])): ?>
                    <p class="text-red-600 text-sm mb-3"><?= htmlspecialchars($errors['puntuacion']) ?></p>
                <?php endif; ?>

                <form method="POST" action="/tecnico/<?= (int) $tecnico['id'] ?>/resena">
                    <input type="hidden" name="id_solicitud" value="<?= (int) $unreviewedSolicitud['id'] ?>">
                    <input type="hidden" name="_back_url" value="/tecnico/<?= (int) $tecnico['id'] ?>#resenas">

                    <!-- Star picker -->
                    <div class="flex items-center gap-2 mb-5">
                        <span class="font-body-md text-sm text-on-surface-variant mr-1">Tu puntuación:</span>
                        <div id="star-picker" class="flex text-amber-400 cursor-pointer" style="font-size:28px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="material-symbols-outlined star-btn"
                                      data-value="<?= $i ?>"
                                      style="font-size:28px;font-variation-settings:'FILL' 0;transition:font-variation-settings .1s;">star</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="puntuacion" id="puntuacion-input" value="">
                    </div>

                    <textarea name="comentario"
                              rows="4"
                              placeholder="Comparte tu experiencia con este técnico (opcional)..."
                              class="w-full border border-outline-variant rounded-lg px-4 py-3 font-body-md text-sm text-on-surface bg-surface-container-low
                                     focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] resize-none transition-colors mb-4"
                              maxlength="1000"><?= htmlspecialchars($old['comentario'] ?? '') ?></textarea>

                    <button type="submit"
                            id="submit-resena"
                            disabled
                            class="bg-[#00796b] text-white px-6 py-3 rounded-lg font-label-md text-sm font-semibold
                                   hover:bg-[#006458] transition-colors active:scale-95 shadow-sm
                                   disabled:opacity-40 disabled:cursor-not-allowed">
                        Enviar reseña
                    </button>
                </form>
            </div>

        <?php elseif ($canReview && $yaCalificado && $miResena): ?>
            <div id="form-resena" class="hidden bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/20"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
                <h4 class="font-headline-md text-lg font-semibold text-on-surface mb-4">Tu reseña</h4>

                <?php if (!empty($errors['puntuacion'])): ?>
                    <p class="text-red-600 text-sm mb-3"><?= htmlspecialchars($errors['puntuacion']) ?></p>
                <?php endif; ?>

                <form method="POST" action="/tecnico/<?= (int) $tecnico['id'] ?>/resena/editar">
                    <input type="hidden" name="calificacion_id" value="<?= (int) $miResena['id'] ?>">
                    <input type="hidden" name="_back_url" value="/tecnico/<?= (int) $tecnico['id'] ?>#resenas">

                    <div class="flex items-center gap-2 mb-5">
                        <span class="font-body-md text-sm text-on-surface-variant mr-1">Tu puntuación:</span>
                        <div id="star-picker" class="flex text-amber-400 cursor-pointer" style="font-size:28px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="material-symbols-outlined star-btn"
                                      data-value="<?= $i ?>"
                                      style="font-size:28px;font-variation-settings:'FILL' <?= $i <= ($old['puntuacion'] ?? $miResena['puntuacion']) ? '1' : '0' ?>;">star</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="puntuacion" id="puntuacion-input"
                               value="<?= (int) ($old['puntuacion'] ?? $miResena['puntuacion']) ?>">
                    </div>

                    <textarea name="comentario"
                              rows="4"
                              placeholder="Comparte tu experiencia con este técnico (opcional)..."
                              class="w-full border border-outline-variant rounded-lg px-4 py-3 font-body-md text-sm text-on-surface bg-surface-container-low
                                     focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] resize-none transition-colors mb-4"
                              maxlength="1000"><?= htmlspecialchars($old['comentario'] ?? $miResena['comentario'] ?? '') ?></textarea>

                    <button type="submit"
                            id="submit-resena"
                            class="bg-[#00796b] text-white px-6 py-3 rounded-lg font-label-md text-sm font-semibold
                                   hover:bg-[#006458] transition-colors active:scale-95 shadow-sm">
                        Guardar cambios
                    </button>
                </form>
            </div>

        <?php elseif ($canReview): ?>
            <div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant/20 flex items-center gap-3">
                <span class="material-symbols-outlined text-outline">info</span>
                <p class="font-body-md text-sm text-on-surface-variant">
                    Podrás calificar a este técnico una vez que tengas un servicio completado con él.
                </p>
            </div>
        <?php endif; ?>

    </section>
</main>

<!-- ── Image modal ── -->
<div id="img-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
     role="dialog" aria-modal="true">
    <div class="relative max-w-4xl w-full flex items-center justify-center">
        <img id="img-modal-img" src="" alt=""
             class="max-h-[85vh] max-w-full rounded-xl shadow-2xl object-contain">
        <button id="img-modal-close"
                class="absolute -top-3 -right-3 w-9 h-9 rounded-full bg-surface-container-lowest border border-outline-variant/30
                       flex items-center justify-center text-on-surface hover:bg-[#e6f4f1] hover:text-[#00796b] transition-colors shadow-md"
                aria-label="Cerrar">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>
</div>

<script>
(function () {
    // ── Star picker ──
    var picker   = document.getElementById('star-picker');
    var input    = document.getElementById('puntuacion-input');
    var btn      = document.getElementById('submit-resena');
    var stars    = picker ? picker.querySelectorAll('.star-btn') : [];
    var selected = input ? parseInt(input.value) || 0 : 0;

    function paintStars(n) {
        stars.forEach(function (s, i) {
            s.style.fontVariationSettings = i < n ? "'FILL' 1" : "'FILL' 0";
        });
    }

    function selectStar(val) {
        selected = val;
        if (input) input.value = val;
        paintStars(val);
        if (btn) btn.disabled = false;
    }

    if (picker) {
        paintStars(selected); // paint initial state (edit pre-fill)
        picker.addEventListener('mouseover', function (e) {
            var s = e.target.closest('.star-btn');
            if (s) paintStars(parseInt(s.dataset.value));
        });
        picker.addEventListener('mouseleave', function () { paintStars(selected); });
        picker.addEventListener('click', function (e) {
            var s = e.target.closest('.star-btn');
            if (s) selectStar(parseInt(s.dataset.value));
        });
    }

    // ── Reviews slider ──
    var reviewsScroll = document.getElementById('reviews-scroll');
    var reviewsPrev   = document.getElementById('reviews-prev');
    var reviewsNext   = document.getElementById('reviews-next');
    if (reviewsScroll) {
        if (reviewsPrev) reviewsPrev.addEventListener('click', function () {
            reviewsScroll.scrollBy({ left: -340, behavior: 'smooth' });
        });
        if (reviewsNext) reviewsNext.addEventListener('click', function () {
            reviewsScroll.scrollBy({ left: 340, behavior: 'smooth' });
        });
    }

    // ── Header stars → scroll + pre-fill ──
    var headerStars = document.getElementById('header-stars');
    var resenasSection = document.getElementById('resenas');
    if (headerStars && resenasSection) {
        headerStars.style.cursor = 'pointer';
        headerStars.addEventListener('click', function (e) {
            resenasSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            var s = e.target.closest('.header-star');
            if (s && picker) selectStar(parseInt(s.dataset.value));
        });
    }

    // ── Menú de 3 puntos en tarjeta de reseña propia ──
    var dropdown    = document.getElementById('review-dropdown');
    var editBtn     = document.getElementById('review-edit-btn');
    var deleteForm  = document.getElementById('review-delete-form');
    var deleteCalId = document.getElementById('review-delete-cal-id');
    var formResena  = document.getElementById('form-resena');
    var activeDotsBtn = null;

    function closeDropdown() {
        if (dropdown) dropdown.classList.add('hidden');
        activeDotsBtn = null;
    }

    document.querySelectorAll('.review-dots-btn').forEach(function (dotsBtn) {
        dotsBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var calId    = dotsBtn.dataset.calId;
            var tecnicoId = dotsBtn.dataset.tecnicoId;

            if (activeDotsBtn === dotsBtn && !dropdown.classList.contains('hidden')) {
                closeDropdown();
                return;
            }
            activeDotsBtn = dotsBtn;

            // Configure edit button
            if (editBtn) {
                editBtn.onclick = function () {
                    closeDropdown();
                    if (formResena) {
                        formResena.classList.remove('hidden');
                        setTimeout(function () {
                            formResena.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 50);
                    }
                };
            }

            // Configure delete form
            if (deleteForm && deleteCalId) {
                deleteCalId.value = calId;
                deleteForm.action = '/tecnico/' + tecnicoId + '/resena/eliminar';
                deleteForm.onsubmit = function () {
                    return confirm('¿Estás seguro de que quieres eliminar tu reseña?');
                };
            }

            // Position dropdown near button
            // position:fixed → coordinates relative to viewport, NO scrollY/scrollX
            var rect = dotsBtn.getBoundingClientRect();
            dropdown.style.top  = (rect.bottom + 6) + 'px';
            dropdown.style.left = Math.max(8, rect.right - 148) + 'px';
            dropdown.classList.remove('hidden');
        });
    });

    document.addEventListener('click', function (e) {
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if (!dropdown.contains(e.target)) closeDropdown();
        }
    });

    // ── Gallery image modal ──
    var imgModal      = document.getElementById('img-modal');
    var imgModalImg   = document.getElementById('img-modal-img');
    var imgModalClose = document.getElementById('img-modal-close');

    document.querySelectorAll('.gallery-img').forEach(function (img) {
        img.addEventListener('click', function () {
            imgModalImg.src = img.dataset.modalSrc;
            imgModalImg.alt = img.dataset.modalAlt;
            imgModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeImgModal() {
        imgModal.classList.add('hidden');
        document.body.style.overflow = '';
        imgModalImg.src = '';
    }

    if (imgModalClose) imgModalClose.addEventListener('click', closeImgModal);
    imgModal.addEventListener('click', function (e) {
        if (e.target === imgModal) closeImgModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeImgModal();
    });
}());
</script>
