<!-- Page header -->
<div class="mb-8 flex items-center gap-4">
    <a href="/dashboard/admin/usuarios"
       class="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors text-label-md font-label-md">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Volver a Usuarios
    </a>
</div>

<div class="mb-6">
    <h1 class="text-headline-lg font-headline-lg text-on-surface mb-1">Editar Usuario</h1>
    <p class="text-body-md font-body-md text-on-surface-variant">
        Modificando perfil de <strong><?= htmlspecialchars($user['nombre']) ?></strong>
    </p>
</div>

<!-- Form card -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 max-w-2xl">
    <div class="p-6 border-b border-outline-variant/30">
        <h2 class="text-headline-md font-headline-md text-on-surface">Datos del usuario</h2>
    </div>

    <form action="/dashboard/admin/usuarios/<?= (int) $user['id'] ?>/editar" method="POST" class="p-6 space-y-5">
        <input type="hidden" name="_back_url" value="/dashboard/admin/usuarios/<?= (int) $user['id'] ?>/editar">

        <!-- Nombre -->
        <div>
            <label class="block text-label-md font-label-md text-on-surface mb-1.5" for="nombre">
                Nombre completo <span class="text-error">*</span>
            </label>
            <input id="nombre"
                   name="nombre"
                   type="text"
                   value="<?= htmlspecialchars($old['nombre'] ?? $user['nombre']) ?>"
                   maxlength="100"
                   class="w-full px-4 py-2.5 bg-surface-container-lowest border <?= !empty($errors['nombre']) ? 'border-error' : 'border-outline-variant' ?> rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <?php if (!empty($errors['nombre'])): ?>
            <p class="mt-1 text-xs text-error"><?= htmlspecialchars($errors['nombre'][0]) ?></p>
            <?php endif; ?>
        </div>

        <!-- Correo -->
        <div>
            <label class="block text-label-md font-label-md text-on-surface mb-1.5" for="correo">
                Correo electrónico <span class="text-error">*</span>
            </label>
            <input id="correo"
                   name="correo"
                   type="email"
                   value="<?= htmlspecialchars($old['correo'] ?? $user['correo']) ?>"
                   maxlength="150"
                   class="w-full px-4 py-2.5 bg-surface-container-lowest border <?= !empty($errors['correo']) ? 'border-error' : 'border-outline-variant' ?> rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <?php if (!empty($errors['correo'])): ?>
            <p class="mt-1 text-xs text-error"><?= htmlspecialchars($errors['correo'][0]) ?></p>
            <?php endif; ?>
        </div>

        <!-- Teléfono -->
        <div>
            <label class="block text-label-md font-label-md text-on-surface mb-1.5" for="telefono">
                Teléfono
            </label>
            <input id="telefono"
                   name="telefono"
                   type="tel"
                   value="<?= htmlspecialchars($old['telefono'] ?? $user['telefono'] ?? '') ?>"
                   maxlength="20"
                   placeholder="+503 0000-0000"
                   class="w-full px-4 py-2.5 bg-surface-container-lowest border <?= !empty($errors['telefono']) ? 'border-error' : 'border-outline-variant' ?> rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
            <?php if (!empty($errors['telefono'])): ?>
            <p class="mt-1 text-xs text-error"><?= htmlspecialchars($errors['telefono'][0]) ?></p>
            <?php endif; ?>
        </div>

        <!-- Rol -->
        <div>
            <label class="block text-label-md font-label-md text-on-surface mb-1.5" for="rol">
                Rol <span class="text-error">*</span>
            </label>
            <select id="rol"
                    name="rol"
                    class="w-full px-4 py-2.5 bg-surface-container-lowest border <?= !empty($errors['rol']) ? 'border-error' : 'border-outline-variant' ?> rounded-lg text-body-md font-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                <?php foreach (['cliente' => 'Cliente', 'tecnico' => 'Técnico', 'admin' => 'Administrador'] as $val => $label): ?>
                <option value="<?= $val ?>"
                    <?= (($old['rol'] ?? $user['rol']) === $val ? 'selected' : '') ?>>
                    <?= $label ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['rol'])): ?>
            <p class="mt-1 text-xs text-error"><?= htmlspecialchars($errors['rol'][0]) ?></p>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="px-6 py-2.5 bg-primary text-on-primary text-label-md font-label-md rounded-lg hover:brightness-95 active:scale-[0.98] transition-all shadow-sm">
                Guardar cambios
            </button>
            <a href="/dashboard/admin/usuarios"
               class="px-6 py-2.5 bg-surface-container text-on-surface-variant text-label-md font-label-md rounded-lg hover:bg-surface-container-high transition-all">
                Cancelar
            </a>
        </div>
    </form>
</div>
