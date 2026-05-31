<?php
    $solId    = (int) $solicitud['id'];
    $estado   = $solicitud['estado'];
    $titulo   = htmlspecialchars($solicitud['titulo'] ?? '');
    $desc     = htmlspecialchars($solicitud['descripcion'] ?? '');
    $dir      = htmlspecialchars($solicitud['direccion'] ?? '');
    $fecha    = date('d M, Y', strtotime($solicitud['fecha_creacion']));

    $clNombre  = htmlspecialchars($solicitud['cliente_nombre'] ?? 'Cliente');
    $clFoto    = $solicitud['cliente_foto'] ?? null;
    $clTel     = htmlspecialchars($solicitud['cliente_telefono'] ?? '');
    $clCorreo  = htmlspecialchars($solicitud['cliente_correo'] ?? '');
    $clPartes  = explode(' ', trim($clNombre));
    $clInic    = strtoupper(substr($clPartes[0], 0, 1) . (isset($clPartes[1]) ? substr($clPartes[1], 0, 1) : ''));

    $estadoBadge = [
        'pendiente'   => ['label' => 'Pendiente',   'classes' => 'bg-secondary-container text-on-surface'],
        'aceptada'    => ['label' => 'Cotizada',     'classes' => 'bg-primary-container/20 text-primary'],
        'en_progreso' => ['label' => 'En progreso',  'classes' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant'],
        'completada'  => ['label' => 'Completada',   'classes' => 'bg-primary-container/10 text-primary'],
        'cancelada'   => ['label' => 'Cancelada',    'classes' => 'bg-error-container text-on-error-container'],
    ];
    $badge = $estadoBadge[$estado] ?? ['label' => ucfirst($estado), 'classes' => 'bg-surface-container text-on-surface'];

    $oldPrecio  = htmlspecialchars($old['precio_estimado'] ?? '');
    $oldDesc    = htmlspecialchars($old['descripcion'] ?? '');
    $mostrarChat = ($estado !== 'cancelada');
?>

<!-- Header -->
<div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
    <div class="flex items-center gap-4">
        <a href="/dashboard/tecnico/solicitudes"
           class="p-2 text-on-surface-variant hover:bg-surface-container rounded-full transition-colors flex items-center justify-center">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="font-bold text-xl text-on-surface">Solicitud #<?= $solId ?></h1>
            <p class="text-sm text-on-surface-variant">Recibida el <?= $fecha ?></p>
        </div>
    </div>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?= $badge['classes'] ?>">
        <?= $badge['label'] ?>
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Columna izquierda: detalles -->
    <div class="lg:col-span-2 flex flex-col gap-6">

        <!-- Info del cliente -->
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 flex flex-col sm:flex-row gap-5 items-start"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
            <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 border-2 border-surface-container">
                <?php if ($clFoto): ?>
                    <img src="<?= htmlspecialchars($clFoto) ?>" alt="<?= $clNombre ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full bg-secondary-container flex items-center justify-center text-on-surface-variant font-bold text-xl">
                        <?= $clInic ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-1">
                <h2 class="font-semibold text-lg text-on-surface mb-3"><?= $clNombre ?></h2>
                <div class="flex flex-col gap-2 text-sm text-on-surface-variant">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-primary">location_on</span>
                        <span><?= $dir ?></span>
                    </div>
                    <?php if ($clTel): ?>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-primary">phone</span>
                        <a href="tel:<?= $clTel ?>" class="hover:text-primary transition-colors"><?= $clTel ?></a>
                    </div>
                    <?php endif; ?>
                    <?php if ($clCorreo): ?>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-primary">mail</span>
                        <a href="mailto:<?= $clCorreo ?>" class="hover:text-primary transition-colors"><?= $clCorreo ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Detalles del problema -->
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
            <h3 class="font-semibold text-lg text-on-surface mb-4"><?= $titulo ?></h3>
            <p class="text-sm text-on-surface-variant leading-relaxed mb-5"><?= $desc ?></p>

            <div class="bg-surface-container-low rounded-lg p-4 border border-outline-variant/20">
                <p class="text-xs text-on-surface-variant font-medium uppercase tracking-wider mb-1">Dirección del servicio</p>
                <p class="text-sm text-on-surface font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">location_on</span>
                    <?= $dir ?>
                </p>
            </div>
        </section>

        <!-- Si ya hay cotización: mostrarla -->
        <?php if ($cotizacion): ?>
        <section class="bg-primary-container/10 border border-primary/20 rounded-xl p-6"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.06)">
            <h3 class="font-semibold text-base text-primary mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">task_alt</span>
                Cotización enviada
            </h3>
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-on-surface-variant">Precio propuesto:</span>
                <span class="font-bold text-xl text-on-surface">$<?= number_format((float) $cotizacion['precio_estimado'], 2) ?></span>
            </div>
            <?php if (!empty($cotizacion['descripcion'])): ?>
            <p class="text-sm text-on-surface-variant bg-surface rounded-lg p-3 leading-relaxed">
                <?= htmlspecialchars($cotizacion['descripcion']) ?>
            </p>
            <?php endif; ?>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-medium text-on-surface-variant">Estado de la cotización:</span>
                <?php
                    $cEstado = $cotizacion['estado'];
                    $cClass  = [
                        'pendiente' => 'bg-secondary-container text-on-surface',
                        'aceptada'  => 'bg-primary-container/20 text-primary',
                        'rechazada' => 'bg-error-container text-on-error-container',
                    ][$cEstado] ?? 'bg-surface-container text-on-surface';
                ?>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $cClass ?>">
                    <?= ucfirst($cEstado) ?>
                </span>
            </div>
        </section>
        <?php endif; ?>

        <!-- Chat -->
        <?php if ($mostrarChat): ?>
        <section id="chat" class="flex flex-col overflow-hidden rounded-xl border border-outline-variant/20"
                 style="height:420px; box-shadow:0 1px 3px rgba(0,0,0,.06)">

            <!-- Header -->
            <div class="bg-surface border-b border-outline-variant/30 p-4 flex items-center gap-3 flex-shrink-0">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1;">forum</span>
                <h3 class="font-semibold text-sm text-on-surface">Chat con <?= $clNombre ?></h3>
                <span class="w-2 h-2 rounded-full bg-primary ml-auto flex-shrink-0"></span>
            </div>

            <!-- Mensajes -->
            <div id="chat-messages"
                 class="flex-1 bg-surface-container-low/20 overflow-y-auto p-4 flex flex-col gap-3">

                <?php if (empty($mensajes)): ?>
                <div class="flex-1 flex items-center justify-center h-full">
                    <div class="text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-3 block opacity-30">chat_bubble</span>
                        <p class="text-sm font-medium">Ningún mensaje aún</p>
                        <p class="text-xs mt-1 opacity-70">Coordina los detalles con el cliente</p>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach ($mensajes as $msg):
                    $esPropio = ($msg['remitente_tipo'] === 'tecnico');
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
                <form method="POST" action="/dashboard/tecnico/solicitudes/<?= $solId ?>/mensaje"
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
        </section>
        <script>
        (function() { var el = document.getElementById('chat-messages'); if (el) el.scrollTop = el.scrollHeight; })();
        </script>
        <?php endif; ?>

    </div>

    <!-- Columna derecha: formulario de cotización -->
    <div class="lg:col-span-1">
        <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 sticky top-6"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.06)">

            <?php if ($estado === 'pendiente'): ?>

            <h3 class="font-semibold text-lg text-on-surface mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">request_quote</span>
                Enviar Cotización
            </h3>

            <form method="POST" action="/dashboard/tecnico/solicitudes/<?= $solId ?>/cotizar"
                  class="flex flex-col gap-5">

                <!-- Precio -->
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-2" for="precio_estimado">
                        Monto de la Cotización ($) <span class="text-error">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant material-symbols-outlined">payments</span>
                        <input type="number"
                               id="precio_estimado"
                               name="precio_estimado"
                               value="<?= $oldPrecio ?>"
                               step="0.01"
                               min="0.01"
                               placeholder="Ej. 45.00"
                               class="w-full pl-10 pr-4 py-3 rounded-lg border <?= !empty($errors['precio_estimado']) ? 'border-error' : 'border-outline-variant' ?> bg-surface-container-lowest text-on-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container/40 outline-none transition-all text-sm">
                    </div>
                    <?php if (!empty($errors['precio_estimado'])): ?>
                        <p class="text-error text-xs mt-1"><?= htmlspecialchars($errors['precio_estimado'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Mensaje -->
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-2" for="descripcion">
                        Mensaje / Términos
                    </label>
                    <textarea id="descripcion"
                              name="descripcion"
                              rows="4"
                              placeholder="Explica tu propuesta, materiales incluidos, disponibilidad..."
                              maxlength="1000"
                              class="w-full p-3 rounded-lg border <?= !empty($errors['descripcion']) ? 'border-error' : 'border-outline-variant' ?> bg-surface-container-lowest text-on-surface focus:border-primary-container focus:ring-1 focus:ring-primary-container/40 outline-none transition-all text-sm resize-none"><?= $oldDesc ?></textarea>
                    <?php if (!empty($errors['descripcion'])): ?>
                        <p class="text-error text-xs mt-1"><?= htmlspecialchars($errors['descripcion'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex flex-col gap-3 mt-2">
                    <button type="submit"
                            class="w-full py-3 bg-primary-container text-white rounded-lg font-semibold text-sm hover:bg-primary transition-colors shadow-sm flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">send</span>
                        Enviar Cotización
                    </button>
                    <form method="POST" action="/dashboard/tecnico/solicitudes/<?= $solId ?>/rechazar"
                          onsubmit="return confirm('¿Seguro que quieres rechazar esta solicitud?')">
                        <button type="submit"
                                class="w-full py-3 bg-surface-container-lowest text-on-surface-variant border border-outline-variant rounded-lg text-sm font-medium hover:bg-error-container hover:text-on-error-container hover:border-error transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">close</span>
                            Rechazar Solicitud
                        </button>
                    </form>
                </div>
            </form>

            <?php elseif (in_array($estado, ['cancelada', 'completada'])): ?>

            <div class="text-center py-4">
                <span class="material-symbols-outlined text-3xl text-on-surface-variant mb-2 block">
                    <?= $estado === 'cancelada' ? 'cancel' : 'verified' ?>
                </span>
                <p class="text-sm text-on-surface-variant">
                    Esta solicitud está <?= $estado === 'cancelada' ? 'cancelada' : 'completada' ?>. No se pueden realizar más acciones.
                </p>
            </div>

            <?php elseif ($estado === 'en_progreso'): ?>

            <div class="flex flex-col gap-3">
                <div class="text-center py-3">
                    <span class="material-symbols-outlined text-3xl text-primary mb-1 block" style="font-variation-settings:'FILL' 1;">build_circle</span>
                    <p class="text-sm font-medium text-on-surface mb-0.5">Servicio en progreso</p>
                    <p class="text-xs text-on-surface-variant">El cliente aceptó tu cotización.</p>
                </div>
                <form method="POST" action="/dashboard/tecnico/solicitudes/<?= $solId ?>/completar"
                      onsubmit="return confirm('¿Confirmas que el servicio fue completado satisfactoriamente?')">
                    <button type="submit"
                            class="w-full py-3 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-surface-tint transition-colors shadow-sm flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">task_alt</span>
                        Marcar como Completada
                    </button>
                </form>
            </div>

            <?php else: ?>

            <div class="text-center py-4">
                <span class="material-symbols-outlined text-3xl text-primary mb-2 block" style="font-variation-settings:'FILL' 1;">hourglass_top</span>
                <p class="text-sm font-medium text-on-surface mb-1">Esperando respuesta del cliente</p>
                <p class="text-xs text-on-surface-variant">El cliente revisará tu cotización y decidirá si aceptarla.</p>
            </div>

            <?php endif; ?>

        </section>
    </div>
</div>
