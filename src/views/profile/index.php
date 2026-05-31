<div class="max-w-5xl mx-auto px-4 md:px-8 py-8 md:py-12 flex flex-col gap-8">

    <?php if (!empty($success)): ?>
    <div class="bg-[#e6f4f1] border border-[#00796b]/30 text-[#00796b] px-5 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
        <span class="text-sm"><?= htmlspecialchars($success) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors['auth'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
        <?= htmlspecialchars($errors['auth'][0]) ?>
    </div>
    <?php endif; ?>

    <?php
        $nombre    = htmlspecialchars($user['nombre'] ?? 'Usuario');
        $correo    = htmlspecialchars($user['correo'] ?? '-');
        $telefono  = htmlspecialchars($user['telefono'] ?? '-');
        $rol       = htmlspecialchars($user['rol'] ?? 'cliente');
        $direccion = htmlspecialchars($user['direccion'] ?? '');
        $ciudad    = htmlspecialchars($user['ciudad'] ?? '');
        $pais      = htmlspecialchars($user['pais'] ?? 'El Salvador');
        $ubicacion = trim($ciudad . ($ciudad && $pais ? ', ' : '') . $pais);
        $foto      = $user['foto_perfil'] ?? null;
        $partes    = explode(' ', trim($nombre));
        $iniciales = strtoupper(substr($partes[0],0,1) . (isset($partes[1]) ? substr($partes[1],0,1) : ''));
        $role      = $user['rol'] ?? ($_SESSION['role'] ?? null);
    ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- Tarjeta principal -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-8 border border-outline-variant/20 relative overflow-hidden"
             style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">

            <div class="absolute inset-0 bg-gradient-to-br from-surface-container-low to-transparent opacity-50 pointer-events-none"></div>

            <div class="relative flex flex-col md:flex-row gap-8 items-start">

                <!-- Avatar -->
                <?php if ($foto): ?>
                    <img src="<?= $foto ?>" alt="Foto de perfil"
                         class="w-32 h-32 md:w-40 md:h-40 rounded-lg object-cover flex-shrink-0 shadow-sm border border-outline-variant/10">
                <?php else: ?>
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-lg flex-shrink-0 bg-[#e6f4f1] flex items-center justify-center text-[#00796b] font-bold text-4xl border border-outline-variant/10">
                        <?= $iniciales ?>
                    </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="flex-1 flex flex-col gap-4 min-w-0 perfil-card-info">

                    <!-- Nombre y badge -->
                    <div>
                        <h1  class="text-2xl font-bold text-on-surface leading-tight"><?= $nombre ?></h1>
                        <div class="flex items-center gap-5 mt-1">
                            <span class="inline-flex items-center gap-1 bg-[#e6f4f1] text-[#00796b] text-s font-bold px-3 py-1 rounded-full">
                                <span class="material-symbols-outlined" style="font-size:.85rem;font-variation-settings:'FILL' 1;">verified</span>
                                <?= ucfirst($rol) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Campos de contacto -->
                    <div class="flex flex-col gap-2 text-sm text-on-surface-variant">
                        <?php if ($correo): ?>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.2rem">mail</span>
                            <span class="truncate"><?= $correo ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($telefono): ?>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.2rem">call</span>
                            <span><?= $telefono ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($direccion): ?>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.2rem">home</span>
                            <span><?= $direccion ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($ubicacion): ?>
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.2rem">location_on</span>
                            <span><?= $ubicacion ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botón -->
                    <div class="flex items-center gap-4 pt-1">
                        <a href="/perfil/editar"
                           class="inline-flex items-center gap-2 bg-[#00796b] text-white px-5 py-2.5 rounded-lg
                                  text-sm font-semibold hover:bg-[#006458] transition-colors shadow-sm">
                            <span class="material-symbols-outlined" style="font-size:1rem">edit</span>
                            Editar perfil
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Tarjeta lateral -->
        <div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/30 flex flex-col gap-6 self-stretch">

            <h3 class="font-headline-md text-xl font-semibold text-on-surface pb-3 border-b border-outline-variant/20">
                Postulación para Técnico
            </h3>

            <div class="flex flex-col gap-5">

                <?php if ($role === 'admin'): ?>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">admin_panel_settings</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-body-md text-base text-on-surface font-semibold">Administrador</span>
                            <span class="text-sm text-outline">No necesitas postularte</span>
                        </div>
                    </div>

                <?php elseif ($role === 'tecnico'): ?>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">verified</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-body-md text-base text-on-surface font-semibold">Técnico activo</span>
                            <span class="text-sm text-outline">Ya tienes rol de técnico</span>
                        </div>
                    </div>

                <?php else: ?>
                    <?php if (!empty($techProfile)): ?>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                            <span class="material-symbols-outlined">pending</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="perfil-badge-estado badge-<?= htmlspecialchars($techProfile['estado']) ?>">
                                <?= htmlspecialchars($techProfile['estado']) ?>
                            </span>
                            <span class="text-sm text-outline">
                                <?= htmlspecialchars($techProfile['comentario_admin'] ?? 'Sin comentarios') ?>
                            </span>
                        </div>
                    </div>
                    <div class="h-px bg-outline-variant/20"></div>
                    <?php endif; ?>

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                            <span class="material-symbols-outlined">person_search</span>
                        </div>
                        <span class="text-base text-on-surface">Recibe solicitudes</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                            <span class="material-symbols-outlined">event_available</span>
                        </div>
                        <span class="text-base text-on-surface">Gestiona tu agenda</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b]">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">star</span>
                        </div>
                        <span class="text-base text-on-surface">Construye tu reputación</span>
                    </div>

                    <a href="/perfil/solicitud-tecnico"
                       class="inline-flex items-center justify-center gap-2 w-full bg-[#00796b] text-white
                              px-5 py-3 rounded-lg text-sm font-semibold hover:bg-[#006458] transition-colors shadow-sm mt-auto">
                        Ir a solicitud de técnico
                    </a>
                <?php endif; ?>

            </div>
        </div>

    </div>

</div>