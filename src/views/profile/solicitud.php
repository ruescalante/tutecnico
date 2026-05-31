<div class="max-w-2xl mx-auto px-4 md:px-8 py-8 md:py-12 flex flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-[#0a4a38]">Solicitud para ser Técnico</h1>
        <p class="text-sm text-[#5a8a7a] mt-1">Completa el formulario para postularte como técnico en TuTécnico.</p>
    </div>

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

    <!-- Card -->
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 overflow-hidden"
         style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#e6f4f1] flex items-center justify-center">
                <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.1rem;font-variation-settings:'FILL' 1;">build</span>
            </div>
            <h2 class="text-base font-semibold text-on-surface">Datos de postulación</h2>
        </div>

        <div class="p-6 flex flex-col gap-5">

            <!-- Estado actual si ya existe solicitud -->
            <?php if (!empty($techProfile)): ?>
            <div class="flex items-center gap-3 p-4 bg-surface-container-low rounded-xl border border-outline-variant/20">
                <div class="w-9 h-9 bg-white shadow-sm flex items-center justify-center rounded-full text-[#00796b] flex-shrink-0">
                    <span class="material-symbols-outlined" style="font-size:1.1rem">pending</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs font-bold uppercase tracking-wide text-outline">Estado actual</span>
                    <span class="perfil-badge-estado badge-<?= htmlspecialchars($techProfile['estado']) ?>">
                        <?= htmlspecialchars($techProfile['estado']) ?>
                    </span>
                    <span class="text-sm text-on-surface-variant mt-0.5">
                        <?= htmlspecialchars($techProfile['comentario_admin'] ?? 'Sin comentarios') ?>
                    </span>
                </div>
            </div>
            <div class="h-px bg-outline-variant/20"></div>
            <?php endif; ?>

            <!-- Formulario -->
            <form action="/perfil/solicitud-tecnico" method="POST" class="flex flex-col gap-5">
                <input type="hidden" name="_back_url" value="/perfil">

                <!-- Zona de cobertura -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="zona_cobertura">Zona de cobertura</label>
                    <input type="text" id="zona_cobertura" name="zona_cobertura"
                           value="<?= htmlspecialchars($old['zona_cobertura'] ?? $techProfile['zona_cobertura'] ?? '') ?>"
                           required
                           class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                  focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    <?php if (!empty($errors['zona_cobertura'])): ?>
                        <p class="text-red-600 text-xs"><?= htmlspecialchars($errors['zona_cobertura'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Descripción -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="descripcion">Descripción de experiencia</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required
                              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                     focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] resize-none transition-colors"
                    ><?= htmlspecialchars($old['descripcion'] ?? $techProfile['descripcion'] ?? '') ?></textarea>
                    <?php if (!empty($errors['descripcion'])): ?>
                        <p class="text-red-600 text-xs"><?= htmlspecialchars($errors['descripcion'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Documentos -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="documentos_verificacion">Ruta o referencia de documentos</label>
                    <input type="text" id="documentos_verificacion" name="documentos_verificacion"
                           value="<?= htmlspecialchars($old['documentos_verificacion'] ?? $techProfile['documentos_verificacion'] ?? '') ?>"
                           required
                           class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                  focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    <?php if (!empty($errors['documentos_verificacion'])): ?>
                        <p class="text-red-600 text-xs"><?= htmlspecialchars($errors['documentos_verificacion'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Acciones -->
                <div class="flex items-center gap-3 pt-1">
                    <a href="/perfil"
                       class="inline-flex items-center gap-2 border border-outline-variant text-on-surface-variant px-5 py-2.5 rounded-lg
                              text-sm font-semibold hover:bg-surface-container-low transition-colors">
                        Volver al perfil
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-[#00796b] text-white px-6 py-2.5 rounded-lg
                                   text-sm font-semibold hover:bg-[#006458] transition-colors shadow-sm">
                        <span class="material-symbols-outlined" style="font-size:1rem">send</span>
                        Enviar / Actualizar solicitud
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>