<div class="max-w-5xl mx-auto px-4 md:px-8 py-8 md:py-12 flex flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-[#0a4a38]">Editar Perfil</h1>
        <p class="text-sm text-[#5a8a7a] mt-1">Actualiza tu información personal.</p>
    </div>

    <?php if (!empty($success)): ?>
    <div class="bg-[#e6f4f1] border border-[#00796b]/30 text-[#00796b] px-5 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
        <span class="text-sm"><?= htmlspecialchars($success) ?></span>
    </div>
    <?php endif; ?>
    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
        <?= htmlspecialchars($errors['general'][0]) ?>
    </div>
    <?php endif; ?>

    <?php
        $foto      = $user['foto_perfil'] ?? null;
        $partes    = explode(' ', trim($user['nombre'] ?? 'U'));
        $iniciales = strtoupper(substr($partes[0],0,1) . (isset($partes[1]) ? substr($partes[1],0,1) : ''));
        $esTecnico = ($user['rol'] ?? '') === 'tecnico';
    ?>

    <form action="/perfil/editar" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
    <div class="grid grid-cols-1 <?= $esTecnico ? 'lg:grid-cols-3' : 'max-w-2xl' ?> gap-6 items-start">

        <!-- ══════════════════════════════
             Información personal
        ══════════════════════════════ -->
        <section class="<?= $esTecnico ? 'lg:col-span-1' : '' ?> bg-surface-container-lowest rounded-xl border border-outline-variant/20 overflow-hidden"
                 style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">

            <!-- Header de sección -->
            <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#e6f4f1] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.1rem">person</span>
                </div>
                <h2 class="text-base font-semibold text-on-surface">Información personal</h2>
            </div>

            <div class="p-6 flex flex-col gap-5">

                <!-- Foto de perfil -->
                <div class="flex items-center gap-4">
                    <?php if ($foto): ?>
                        <img src="<?= htmlspecialchars($foto) ?>"
                             class="w-20 h-20 rounded-xl object-cover border border-outline-variant/10 flex-shrink-0" alt="Foto actual">
                    <?php else: ?>
                        <div class="w-20 h-20 rounded-xl bg-[#e6f4f1] flex items-center justify-center text-[#00796b] font-bold text-2xl border border-outline-variant/10 flex-shrink-0">
                            <?= $iniciales ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-col gap-1">
                        <label for="foto_perfil"
                               class="inline-flex items-center gap-2 cursor-pointer bg-[#e6f4f1] text-[#00796b] text-sm font-semibold px-4 py-2 rounded-lg hover:bg-[#d0ece7] transition-colors">
                            <span class="material-symbols-outlined" style="font-size:1rem">photo_camera</span>
                            Cambiar foto
                        </label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" class="hidden">
                        <p class="text-xs text-outline">JPG, PNG o WEBP · máx. 2MB</p>
                    </div>
                </div>

                <div class="h-px bg-outline-variant/20"></div>

                <!-- Nombre -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre"
                           value="<?= htmlspecialchars($old['nombre'] ?? $user['nombre'] ?? '') ?>"
                           required
                           class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                  focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    <?php if (!empty($errors['nombre'])): ?>
                        <p class="text-red-600 text-xs"><?= htmlspecialchars($errors['nombre'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Correo -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo"
                           value="<?= htmlspecialchars($old['correo'] ?? $user['correo'] ?? '') ?>"
                           required
                           class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                  focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    <?php if (!empty($errors['correo'])): ?>
                        <p class="text-red-600 text-xs"><?= htmlspecialchars($errors['correo'][0]) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Teléfono -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono"
                           value="<?= htmlspecialchars($old['telefono'] ?? $user['telefono'] ?? '') ?>"
                           class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                  focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                </div>

                <!-- Dirección -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface" for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion"
                           value="<?= htmlspecialchars($old['direccion'] ?? $user['direccion'] ?? '') ?>"
                           class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                  focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                </div>

                <!-- Ciudad + País -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-on-surface" for="ciudad">Ciudad</label>
                        <input type="text" id="ciudad" name="ciudad"
                               value="<?= htmlspecialchars($old['ciudad'] ?? $user['ciudad'] ?? '') ?>"
                               class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                      focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-on-surface" for="pais">País</label>
                        <input type="text" id="pais" name="pais"
                               value="<?= htmlspecialchars($old['pais'] ?? $user['pais'] ?? 'El Salvador') ?>"
                               class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                      focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    </div>
                </div>

            </div>
        </section>

        <!-- ══════════════════════════════
             Solo si es técnico
        ══════════════════════════════ -->
        <?php if ($esTecnico): ?>
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Perfil técnico -->
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 overflow-hidden"
                     style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">

                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#e6f4f1] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.1rem">build</span>
                    </div>
                    <h2 class="text-base font-semibold text-on-surface">Perfil técnico</h2>
                </div>

                <div class="p-6 flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-on-surface" for="descripcion_tecnico">Sobre mí</label>
                        <textarea id="descripcion_tecnico" name="descripcion_tecnico" rows="4"
                                  class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                         focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] resize-none transition-colors"
                        ><?= htmlspecialchars($old['descripcion_tecnico'] ?? $techProfile['descripcion'] ?? '') ?></textarea>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-sm font-semibold text-on-surface" for="zona_cobertura">Zona de cobertura</label>
                        <input type="text" id="zona_cobertura" name="zona_cobertura"
                               value="<?= htmlspecialchars($old['zona_cobertura'] ?? $techProfile['zona_cobertura'] ?? '') ?>"
                               class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm text-on-surface bg-surface-container-low
                                      focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                    </div>
                </div>
            </section>

            <!-- Categorías -->
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 overflow-hidden"
                     style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">

                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#e6f4f1] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.1rem">category</span>
                    </div>
                    <h2 class="text-base font-semibold text-on-surface">Categorías de servicio</h2>
                </div>

                <div class="p-6">
                    <p class="text-sm text-outline mb-4">Selecciona los servicios que ofreces.</p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($categorias as $cat): ?>
                            <?php $sel = in_array($cat['id'], $misCategs); ?>
                            <label class="categoria-chip-edit <?= $sel ? 'selected' : '' ?>">
                                <input type="checkbox"
                                       name="categorias[]"
                                       value="<?= $cat['id'] ?>"
                                       <?= $sel ? 'checked' : '' ?>
                                       class="hidden">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Fotos de trabajos -->
            <section class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 overflow-hidden"
                     style="box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">

                <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#e6f4f1] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#00796b]" style="font-size:1.1rem">photo_library</span>
                    </div>
                    <h2 class="text-base font-semibold text-on-surface">Fotos de trabajos</h2>
                </div>

                <div class="p-6 flex flex-col gap-5">
                    <p class="text-sm text-outline -mt-1">Muestra tu trabajo. Máx. 5MB por foto.</p>

                    <?php if (!empty($fotosTrabajo)): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach ($fotosTrabajo as $foto_t): ?>
                            <div class="rounded-xl overflow-hidden border border-outline-variant/20 flex flex-col">
                                <img src="<?= htmlspecialchars($foto_t['url']) ?>"
                                     alt="<?= htmlspecialchars($foto_t['descripcion'] ?? '') ?>"
                                     class="w-full h-32 object-cover">
                                <?php if ($foto_t['descripcion']): ?>
                                    <p class="text-xs text-outline px-2 py-1.5"><?= htmlspecialchars($foto_t['descripcion']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="h-px bg-outline-variant/20"></div>
                    <?php endif; ?>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-outline mb-3">Agregar nuevas fotos</p>
                        <div id="fotos-nuevas-wrap" class="flex flex-col gap-3">
                            <div class="foto-nueva-row flex items-center gap-3">
                                <input type="file" name="fotos_trabajo[]" accept="image/*"
                                       class="flex-1 text-sm text-on-surface-variant border border-outline-variant rounded-lg px-3 py-2 bg-surface-container-low
                                              file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold
                                              file:bg-[#e6f4f1] file:text-[#00796b] hover:file:bg-[#d0ece7]">
                                <input type="text" name="fotos_descripcion[]" placeholder="Descripción (opcional)"
                                       class="flex-1 border border-outline-variant rounded-lg px-4 py-2 text-sm text-on-surface bg-surface-container-low
                                              focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
                            </div>
                        </div>
                        <button type="button"
                                onclick="agregarFotoRow()"
                                class="mt-3 inline-flex items-center gap-1.5 text-sm text-[#00796b] font-semibold hover:text-[#006458] transition-colors">
                            <span class="material-symbols-outlined" style="font-size:1rem">add_circle</span>
                            Agregar otra foto
                        </button>
                    </div>
                </div>
            </section>

        </div>
        <?php endif; ?>

    </div>

        <!-- Acciones -->
        <div class="flex items-center gap-3 pt-2">
            <a href="/perfil"
               class="inline-flex items-center gap-2 border border-outline-variant text-on-surface-variant px-5 py-2.5 rounded-lg
                      text-sm font-semibold hover:bg-surface-container-low transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-[#00796b] text-white px-6 py-2.5 rounded-lg
                           text-sm font-semibold hover:bg-[#006458] transition-colors shadow-sm">
                <span class="material-symbols-outlined" style="font-size:1rem">save</span>
                Guardar cambios
            </button>
        </div>

    </form>
</div>

<!-- CSS para chips de categorías -->
<style>
.categoria-chip-edit {
    display: inline-flex;
    align-items: center;
    padding: .375rem .875rem;
    border-radius: 999px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    border: 1.5px solid #bdc9c5;
    color: #3e4946;
    background: transparent;
    transition: all .15s;
    user-select: none;
}
.categoria-chip-edit:hover {
    border-color: #00796b;
    color: #00796b;
    background: #e6f4f1;
}
.categoria-chip-edit.selected {
    background: #e6f4f1;
    border-color: #00796b;
    color: #00796b;
}
</style>

<!-- JS para agregar foto a trabajos del tecnico -->
<script>
    function agregarFotoRow() {
        const wrap = document.getElementById('fotos-nuevas-wrap');
        const row  = document.createElement('div');
        row.className = 'foto-nueva-row flex items-center gap-3';
        row.innerHTML = `
            <input type="file" name="fotos_trabajo[]" accept="image/*"
                   class="flex-1 text-sm text-on-surface-variant border border-outline-variant rounded-lg px-3 py-2 bg-surface-container-low
                          file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold
                          file:bg-[#e6f4f1] file:text-[#00796b] hover:file:bg-[#d0ece7]">
            <input type="text" name="fotos_descripcion[]" placeholder="Descripción (opcional)"
                   class="flex-1 border border-outline-variant rounded-lg px-4 py-2 text-sm text-on-surface bg-surface-container-low
                          focus:outline-none focus:ring-2 focus:ring-[#00796b]/40 focus:border-[#00796b] transition-colors">
            <button type="button" onclick="this.parentElement.remove()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors flex-shrink-0">
                <span class="material-symbols-outlined" style="font-size:1.1rem">close</span>
            </button>
        `;
        wrap.appendChild(row);
    }

    // Toggle visual de chips
    document.querySelectorAll('.categoria-chip-edit').forEach(chip => {
        chip.addEventListener('click', () => {
            chip.classList.toggle('selected');
        });
    });
</script>