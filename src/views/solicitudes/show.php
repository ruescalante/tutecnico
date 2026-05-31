<?php
    $solId    = (int) $solicitud['id'];
    $estado   = $solicitud['estado'];
    $titulo   = htmlspecialchars($solicitud['titulo'] ?? '');
    $desc     = htmlspecialchars($solicitud['descripcion'] ?? '');
    $dir      = htmlspecialchars($solicitud['direccion'] ?? '');
    $fecha    = date('d M, Y', strtotime($solicitud['fecha_creacion']));

    $tecNombre = htmlspecialchars($solicitud['tecnico_nombre'] ?? 'Técnico');
    $tecFoto   = $solicitud['tecnico_foto'] ?? null;
    $tecId     = (int) ($solicitud['id_tecnico'] ?? 0);
    $tecPartes = explode(' ', trim($tecNombre));
    $tecInic   = strtoupper(substr($tecPartes[0], 0, 1) . (isset($tecPartes[1]) ? substr($tecPartes[1], 0, 1) : ''));

    $rating   = (float) ($tecnicoAvgRating ?? 0);
    $nResenas = (int) ($tecnicoTotalResenas ?? 0);
    $nComplet = (int) ($tecnicoCompletados ?? 0);

    $estadoBadge = [
        'pendiente'   => ['label' => 'Pendiente',            'classes' => 'bg-secondary-container text-on-surface'],
        'aceptada'    => ['label' => 'Cotización recibida',  'classes' => 'bg-primary-container/20 text-primary border border-primary/20'],
        'en_progreso' => ['label' => 'En progreso',          'classes' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant'],
        'completada'  => ['label' => 'Completada',           'classes' => 'bg-primary-container/10 text-primary'],
        'cancelada'   => ['label' => 'Cancelada',            'classes' => 'bg-error-container text-on-error-container'],
    ];
    $badge = $estadoBadge[$estado] ?? ['label' => ucfirst($estado), 'classes' => 'bg-surface-container text-on-surface'];

    $mostrarChat = ($estado !== 'cancelada');
?>

<div class="max-w-[1200px] mx-auto px-6 py-8 md:py-12">

    <!-- Back + flash messages -->
    <div class="mb-6 flex items-center gap-3">
        <a href="/dashboard/cliente"
           class="text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1 text-sm">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Mis solicitudes
        </a>
    </div>

    <?php if (!empty($success)): ?>
    <div class="mb-5 px-4 py-3 rounded-lg bg-primary-container/20 text-primary border border-primary/20 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">check_circle</span>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors['general'])): ?>
    <div class="mb-5 px-4 py-3 rounded-lg bg-error-container text-on-error-container text-sm">
        <?= htmlspecialchars($errors['general'][0]) ?>
    </div>
    <?php endif; ?>

    <!-- Grid principal -->
    <div class="md:grid md:grid-cols-12 gap-8">

        <!-- ═══════════════════════════════════
             Columna principal (izq)
        ════════════════════════════════════ -->
        <div class="md:col-span-7 lg:col-span-8 flex flex-col gap-6">

            <!-- Status alert -->
            <?php if ($estado === 'pendiente'): ?>
            <div class="bg-secondary-container text-on-surface rounded-xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-outline/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined">pending</span>
                </div>
                <div>
                    <h2 class="font-semibold text-sm">Esperando al técnico</h2>
                    <p class="text-sm opacity-80">Tu solicitud fue enviada. El técnico la revisará y te enviará una cotización pronto.</p>
                </div>
            </div>

            <?php elseif ($estado === 'aceptada'): ?>
            <div class="bg-primary-container text-on-primary-container rounded-xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-on-primary-container/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-on-primary-container" style="font-variation-settings:'FILL' 1;">task_alt</span>
                </div>
                <div>
                    <h2 class="font-semibold text-sm">Cotización Recibida</h2>
                    <p class="text-sm opacity-90">El técnico ha revisado tu solicitud y te ha enviado una propuesta.</p>
                </div>
            </div>

            <?php elseif ($estado === 'en_progreso'): ?>
            <div class="bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-on-tertiary-fixed/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">build_circle</span>
                </div>
                <div>
                    <h2 class="font-semibold text-sm">Servicio en progreso</h2>
                    <p class="text-sm opacity-80">Aceptaste la cotización. El técnico está coordinando el servicio.</p>
                </div>
            </div>

            <?php elseif ($estado === 'completada'): ?>
            <div class="bg-primary-container/20 text-primary rounded-xl p-4 flex items-center gap-4 shadow-sm border border-primary/20">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">verified</span>
                </div>
                <div>
                    <h2 class="font-semibold text-sm">Servicio completado</h2>
                    <p class="text-sm opacity-80">Este servicio fue marcado como completado exitosamente.</p>
                </div>
            </div>

            <?php elseif ($estado === 'cancelada'): ?>
            <div class="bg-error-container text-on-error-container rounded-xl p-4 flex items-center gap-4 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">cancel</span>
                </div>
                <div>
                    <h2 class="font-semibold text-sm">Solicitud cancelada</h2>
                    <p class="text-sm opacity-80">Esta solicitud fue cancelada<?= $solicitud['cancelado_por'] ? ' por ' . htmlspecialchars($solicitud['cancelado_por']) : '' ?>.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quote card: visible tras envío del técnico (aceptada, en_progreso, completada) -->
            <?php if ($cotizacion && in_array($estado, ['aceptada', 'en_progreso', 'completada'])): ?>
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-outline-variant/30 bg-surface">
                    <div class="flex justify-between items-start mb-5">
                        <h3 class="font-semibold text-xl text-primary">Propuesta de Servicio</h3>
                        <div class="text-right">
                            <span class="font-bold text-3xl text-on-surface block leading-none">
                                $<?= number_format((float) $cotizacion['precio_estimado'], 2) ?>
                            </span>
                            <span class="text-xs text-on-surface-variant">Precio Estimado (USD)</span>
                        </div>
                    </div>

                    <?php if (!empty($cotizacion['descripcion'])): ?>
                    <div class="bg-surface-container-low p-4 rounded-lg border border-outline-variant/30 mb-6">
                        <h4 class="font-semibold text-sm mb-2 flex items-center gap-2 text-primary">
                            <span class="material-symbols-outlined text-xl">notes</span>
                            Mensaje del Técnico
                        </h4>
                        <p class="text-sm text-on-surface-variant leading-relaxed">
                            <?= htmlspecialchars($cotizacion['descripcion']) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($estado === 'aceptada'): ?>
                    <div class="flex gap-4">
                        <form method="POST" action="/solicitudes/<?= $solId ?>/cotizacion/aceptar" class="flex-1">
                            <button type="submit"
                                    class="w-full bg-primary-container hover:bg-surface-tint text-on-primary-container font-semibold text-sm py-3 px-6 rounded-lg transition-all active:scale-[.98] shadow-sm flex justify-center items-center gap-2">
                                <span class="material-symbols-outlined text-xl">check_circle</span>
                                Aceptar Cotización
                            </button>
                        </form>
                        <form method="POST" action="/solicitudes/<?= $solId ?>/cotizacion/rechazar" class="flex-1"
                              onsubmit="return confirm('¿Seguro que quieres rechazar esta cotización? La solicitud se cancelará.')">
                            <button type="submit"
                                    class="w-full bg-surface hover:bg-error-container text-error border border-outline-variant hover:border-error font-semibold text-sm py-3 px-6 rounded-lg transition-all active:scale-[.98] flex justify-center items-center gap-2">
                                <span class="material-symbols-outlined text-xl">cancel</span>
                                Rechazar
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Detalles originales de la solicitud -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-6">
                <h3 class="font-semibold text-lg text-on-surface mb-5 flex items-center gap-2 border-b border-outline-variant/20 pb-3">
                    <span class="material-symbols-outlined text-primary">description</span>
                    Detalles Originales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <span class="block text-xs font-medium text-on-surface-variant mb-1 uppercase tracking-wider">Título del Problema</span>
                        <p class="text-sm font-semibold text-on-surface"><?= $titulo ?></p>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-on-surface-variant mb-1 uppercase tracking-wider">Estado</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $badge['classes'] ?>">
                            <?= $badge['label'] ?>
                        </span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs font-medium text-on-surface-variant mb-1 uppercase tracking-wider">Descripción Proporcionada</span>
                        <p class="text-sm text-on-surface bg-surface p-3 rounded-lg border border-outline-variant/20 leading-relaxed">
                            <?= $desc ?>
                        </p>
                    </div>
                    <div class="md:col-span-2 flex items-start gap-3 bg-surface-container-low p-3 rounded-lg">
                        <span class="material-symbols-outlined text-primary mt-0.5">location_on</span>
                        <div>
                            <span class="block text-xs font-medium text-on-surface-variant mb-0.5">Dirección del Servicio</span>
                            <p class="text-sm text-on-surface"><?= $dir ?></p>
                        </div>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-on-surface-variant mb-1 uppercase tracking-wider">Fecha de solicitud</span>
                        <p class="text-sm text-on-surface"><?= $fecha ?></p>
                    </div>
                </div>

                <?php if ($estado === 'pendiente'): ?>
                <div class="mt-5 pt-4 border-t border-outline-variant/20">
                    <form method="POST" action="/solicitudes/<?= $solId ?>/cancelar"
                          onsubmit="return confirm('¿Seguro que quieres cancelar esta solicitud?')">
                        <button type="submit"
                                class="text-error border border-error/30 hover:bg-error-container px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">cancel</span>
                            Cancelar solicitud
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- ═══════════════════════════════════
             Columna derecha (sidebar)
        ════════════════════════════════════ -->
        <div class="md:col-span-5 lg:col-span-4 mt-8 md:mt-0 flex flex-col gap-6">

            <!-- Technician profile card -->
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm overflow-hidden text-center p-6 relative">
                <div class="absolute top-4 right-4">
                    <span class="bg-primary-container/20 text-primary px-2 py-1 rounded text-xs font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        Verificado
                    </span>
                </div>

                <!-- Avatar -->
                <div class="w-24 h-24 mx-auto bg-surface-container rounded-full mb-4 border-4 border-surface overflow-hidden shadow-sm">
                    <?php if ($tecFoto): ?>
                        <img src="<?= htmlspecialchars($tecFoto) ?>" alt="<?= $tecNombre ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-[#e6f4f1] flex items-center justify-center text-[#00796b] font-bold text-2xl">
                            <?= $tecInic ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h3 class="font-semibold text-lg text-on-surface mb-1"><?= $tecNombre ?></h3>

                <!-- Stats row -->
                <div class="flex justify-center items-center gap-4 mt-3 mb-5">
                    <div class="flex flex-col items-center">
                        <?php if ($rating > 0): ?>
                        <span class="flex items-center text-primary font-bold text-sm">
                            <span class="material-symbols-outlined text-lg mr-1" style="font-variation-settings:'FILL' 1;">star</span>
                            <?= number_format($rating, 1) ?>
                        </span>
                        <span class="text-xs text-on-surface-variant"><?= $nResenas ?> <?= $nResenas === 1 ? 'reseña' : 'reseñas' ?></span>
                        <?php else: ?>
                        <span class="text-xs text-on-surface-variant">Sin reseñas</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($rating > 0): ?>
                    <div class="h-8 w-px bg-outline-variant"></div>
                    <?php endif; ?>

                    <div class="flex flex-col items-center">
                        <span class="font-bold text-on-surface text-sm"><?= $nComplet ?></span>
                        <span class="text-xs text-on-surface-variant"><?= $nComplet === 1 ? 'servicio' : 'servicios' ?> completados</span>
                    </div>
                </div>

                <?php if ($tecId > 0): ?>
                <a href="/tecnico/<?= $tecId ?>"
                   class="w-full inline-block py-2 border border-outline-variant text-on-surface hover:bg-surface-container-low hover:border-primary transition-colors rounded-lg text-sm font-medium">
                    Ver Perfil Completo
                </a>
                <?php endif; ?>
            </div>

            <!-- Chat section -->
            <?php if ($mostrarChat): ?>
            <div id="chat" class="flex flex-col overflow-hidden rounded-xl border border-outline-variant/30 shadow-sm"
                 style="height:420px;">

                <!-- Header -->
                <div class="bg-surface border-b border-outline-variant/30 p-4 flex items-center gap-3 flex-shrink-0">
                    <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1;">forum</span>
                    <h3 class="font-semibold text-sm text-on-surface">Chat con <?= $tecNombre ?></h3>
                    <span class="w-2 h-2 rounded-full bg-primary ml-auto flex-shrink-0"></span>
                </div>

                <!-- Mensajes -->
                <div id="chat-messages"
                     class="flex-1 bg-surface-container-low/30 overflow-y-auto p-4 flex flex-col gap-3">

                    <?php if (empty($mensajes)): ?>
                    <div class="flex-1 flex items-center justify-center h-full">
                        <div class="text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-3 block opacity-30">chat_bubble</span>
                            <p class="text-sm font-medium">Ningún mensaje aún</p>
                            <p class="text-xs mt-1 opacity-70">Envía el primero para coordinar los detalles</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($mensajes as $msg):
                        $esPropio = ($msg['remitente_tipo'] === 'cliente');
                        $nombre   = htmlspecialchars($msg['remitente_nombre']);
                        $texto    = htmlspecialchars($msg['contenido']);
                        $hora     = date('d M, H:i', strtotime($msg['fecha']));
                        $foto     = $msg['remitente_foto'] ?? null;
                        $partes   = explode(' ', trim($nombre));
                        $inic     = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
                    ?>
                    <div class="flex <?= $esPropio ? 'justify-end' : 'justify-start' ?> gap-2">

                        <?php if (!$esPropio): ?>
                        <div class="w-7 h-7 rounded-full overflow-hidden flex-shrink-0 self-end">
                            <?php if ($foto): ?>
                                <img src="<?= htmlspecialchars($foto) ?>" alt="<?= $nombre ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-secondary-container flex items-center justify-center text-on-surface-variant text-xs font-bold"><?= $inic ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="max-w-[75%] flex flex-col <?= $esPropio ? 'items-end' : 'items-start' ?>">
                            <div class="px-3 py-2 rounded-2xl text-sm leading-relaxed
                                <?= $esPropio
                                    ? 'bg-primary text-on-primary rounded-br-sm'
                                    : 'bg-surface-container-lowest text-on-surface border border-outline-variant/30 rounded-bl-sm' ?>">
                                <?= $texto ?>
                            </div>
                            <span class="text-xs text-on-surface-variant mt-1 px-1"><?= $hora ?></span>
                        </div>

                        <?php if ($esPropio): ?>
                        <div class="w-7 h-7 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0 self-end text-on-primary-container text-xs font-bold">
                            Yo
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Input -->
                <div class="bg-surface p-3 border-t border-outline-variant/30 flex-shrink-0">
                    <?php if (!empty($errors['contenido'])): ?>
                    <p class="text-error text-xs mb-2 px-1"><?= htmlspecialchars($errors['contenido'][0]) ?></p>
                    <?php endif; ?>
                    <form method="POST" action="/solicitudes/<?= $solId ?>/mensaje"
                          class="flex items-center gap-2">
                        <input type="text"
                               name="contenido"
                               maxlength="2000"
                               autocomplete="off"
                               placeholder="Escribe un mensaje..."
                               class="flex-1 bg-surface-container-low border border-outline-variant rounded-full py-2 px-4 text-sm text-on-surface focus:border-primary outline-none transition-colors">
                        <button type="submit"
                                class="w-10 h-10 bg-primary text-on-primary rounded-full flex items-center justify-center hover:bg-surface-tint transition-colors active:scale-95 flex-shrink-0">
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">send</span>
                        </button>
                    </form>
                </div>
            </div>
            <script>
            (function() { var el = document.getElementById('chat-messages'); if (el) el.scrollTop = el.scrollHeight; })();
            </script>
            <?php endif; ?>

        </div>
    </div>
</div>
