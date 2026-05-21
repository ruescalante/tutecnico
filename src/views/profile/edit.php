<div class="container-edit">
    <h1 class="panel-greeting">Editar Perfil</h1>
    <p class="panel-sub">Actualiza tu información personal.</p>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($errors['general'])): ?>
        <p class="error"><?= htmlspecialchars($errors['general'][0]) ?></p>
    <?php endif; ?>

    <?php
        $foto      = $user['foto_perfil'] ?? null;
        $partes    = explode(' ', trim($user['nombre'] ?? 'U'));
        $iniciales = strtoupper(substr($partes[0],0,1) . (isset($partes[1]) ? substr($partes[1],0,1) : ''));
        $esTecnico = ($user['rol'] ?? '') === 'tecnico';
    ?>

    <form action="/perfil/editar" method="POST" enctype="multipart/form-data" class="form">
    <div class="perfil-edit-layout">

        <!-- datos personales -->
        <section class="card perfil-edit-card">
            <h1 class="edit-section-title"> Información personal</h1>

            <div class="foto-upload-wrap">
                <?php if ($foto): ?>
                    <img src="<?= htmlspecialchars($foto) ?>" class="perfil-foto" alt="Foto actual">
                <?php else: ?>
                    <div class="perfil-foto perfil-foto-iniciales"><?= $iniciales ?></div>
                <?php endif; ?>
                <div class="foto-upload-info">
                    <label class="btn-outline-upload" for="foto_perfil">📷 Cambiar foto</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" style="display:none">
                    <p class="muted" style="font-size:12px; margin-top:4px">JPG, PNG o WEBP · máx. 2MB</p>
                </div>
            </div>

            <div class="card-divider"></div>

            <label>Nombre completo
                <input type="text" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? $user['nombre'] ?? '') ?>" required>
                <?php if (!empty($errors['nombre'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['nombre'][0]) ?></p>
                <?php endif; ?>
            </label>

            <label>Correo electrónico
                <input type="email" name="correo" value="<?= htmlspecialchars($old['correo'] ?? $user['correo'] ?? '') ?>" required>
                <?php if (!empty($errors['correo'])): ?>
                    <p class="error"><?= htmlspecialchars($errors['correo'][0]) ?></p>
                <?php endif; ?>
            </label>

            <label>Teléfono
                <input type="tel" name="telefono" value="<?= htmlspecialchars($old['telefono'] ?? $user['telefono'] ?? '') ?>">
            </label>

            <label>Dirección
                <input type="text" name="direccion" value="<?= htmlspecialchars($old['direccion'] ?? $user['direccion'] ?? '') ?>">
            </label>

            <div class="form-row">
                <label>Ciudad
                    <input type="text" name="ciudad" value="<?= htmlspecialchars($old['ciudad'] ?? $user['ciudad'] ?? '') ?>">
                </label>
                <label>País
                    <input type="text" name="pais" value="<?= htmlspecialchars($old['pais'] ?? $user['pais'] ?? 'El Salvador') ?>">
                </label>
            </div>
        </section>

        <!--  solo si es técnico -->
        <?php if ($esTecnico): ?>
        <div class="perfil-edit-tecnico">

            <section class="card perfil-edit-card">
                <h1 class="edit-section-title"> Perfil técnico</h1>

                <label>Sobre mí
                    <textarea name="descripcion_tecnico" rows="4"><?= htmlspecialchars($old['descripcion_tecnico'] ?? $techProfile['descripcion'] ?? '') ?></textarea>
                </label>

                <label>Zona de cobertura
                    <input type="text" name="zona_cobertura" value="<?= htmlspecialchars($old['zona_cobertura'] ?? $techProfile['zona_cobertura'] ?? '') ?>">
                </label>
            </section>

            <section class="card perfil-edit-card">
                <h2 class="edit-section-title"> Categorías de servicio</h2>
                <p class="muted" style="margin-bottom:1rem; font-size:13px">Selecciona los servicios que ofreces.</p>
                <div class="categorias-grid">
                    <?php foreach ($categorias as $cat): ?>
                        <label class="categoria-chip <?= in_array($cat['id'], $misCategs) ? 'selected' : '' ?>">
                            <input type="checkbox"
                                   name="categorias[]"
                                   value="<?= $cat['id'] ?>"
                                   <?= in_array($cat['id'], $misCategs) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card perfil-edit-card">
                <h2 class="edit-section-title"> Fotos de trabajos</h2>
                <p class="muted" style="margin-bottom:1rem; font-size:13px">Muestra tu trabajo. Máx. 5MB por foto.</p>

                <?php if (!empty($fotosTrabajo)): ?>
                <div class="fotos-trabajo-grid">
                    <?php foreach ($fotosTrabajo as $foto_t): ?>
                        <div class="foto-trabajo-item">
                            <img src="<?= htmlspecialchars($foto_t['url']) ?>" alt="<?= htmlspecialchars($foto_t['descripcion'] ?? '') ?>">
                            <?php if ($foto_t['descripcion']): ?>
                                <p class="foto-desc"><?= htmlspecialchars($foto_t['descripcion']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-divider"></div>
                <?php endif; ?>

                <p class="field-label" style="margin-bottom:8px">Agregar nuevas fotos</p>
                <div id="fotos-nuevas-wrap">
                    <div class="foto-nueva-row">
                        <input type="file" name="fotos_trabajo[]" accept="image/*" class="foto-file-input">
                        <input type="text" name="fotos_descripcion[]" placeholder="Descripción (opcional)" class="foto-desc-input">
                    </div>
                </div>
                <button type="button" class="btn" style="margin-top:8px; font-size:12px" onclick="agregarFotoRow()">+ Agregar otra foto</button>
            </section>

        </div>
        <?php endif; ?>

    </div>

        <div class="form-actions" style="margin-top:1.5rem">
            <a href="/perfil" class="btn">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>

<!--  js para gregar foto extra -->
<script>
    function agregarFotoRow() {
        const wrap = document.getElementById('fotos-nuevas-wrap');
        const row  = document.createElement('div');
        row.className = 'foto-nueva-row';
        row.innerHTML = `
            <input type="file" name="fotos_trabajo[]" accept="image/*" class="foto-file-input">
            <input type="text" name="fotos_descripcion[]" placeholder="Descripción (opcional)" class="foto-desc-input">
            <button type="button" onclick="this.parentElement.remove()" style="color:#A32D2D; background:none; border:none; cursor:pointer; font-size:18px">×</button>
        `;
        wrap.appendChild(row);
    }
</script>