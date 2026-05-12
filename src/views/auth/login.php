<div class="bg-gradient-to-br from-[#4A72FF] to-[#3B9294] min-h-screen flex items-center justify-center p-4 font-body-md text-on-surface">
    <div class="w-full max-w-[1000px] bg-surface-container-lowest rounded-xl shadow-[0_12px_40px_rgba(0,32,27,0.1)] overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        <!-- Illustration Side -->
        <div class="hidden md:flex md:w-1/2 p-12 flex-col justify-between relative bg-surface-container-lowest border-r border-surface-variant">
            <div class="flex items-center z-10"><span class="text-primary font-headline-lg text-headline-lg text-on-surface mb-2 tracking-tight">Tu</span><span class="text-on-surface font-headline-lg text-headline-lg mb-2 font-semibold tracking-tight">Técnico</span></div>
            <div class="flex-1 flex items-center justify-center relative mt-8">
                <div class="w-full h-full min-h-[300px] relative" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBJ6rl_rDwe92TtdevQ4wLZSzUJYH6DQMuhaRKOOb5iS4AnKvQncHoVKDwUAeZ-KjrE1pMZQGRlybZh1RMvVgNfprTA9XHWv3JElQFzOgGUhbG_JqWBCo0ul2RR3IyA2o4gw323hKCRbOzJi2BHDehqKd8R6T08ei5ucgyA2v_a6CmtvtiQqApLAsUjlpcIpcTO_cxjJ2_me9w-o_mTILmkwIpcnE8BBKehYi9Upub1EY50dWqPnmgfjsEF5uyvv2QsDZut1Yh-TrM'); background-size: cover; background-position: center; border-radius: 12px;">
                </div>
            </div>
        </div>
        <!-- Form Side -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-surface-container-lowest">
            <div class="max-w-md w-full mx-auto">
                <div class="flex items-center mb-8 md:hidden justify-center"><span class="text-primary font-headline-md font-bold tracking-tight">Tu</span><span class="text-on-surface font-headline-md font-semibold tracking-tight">Técnico</span></div>
                <div class="mb-8 text-center md:text-left">
                    <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">Iniciar Sesión</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">Bienvenido de nuevo.<br />Ingresa para continuar.</p>
                </div>

                <?php if (!empty($success)): ?>
                    <p class="success mb-4"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>

                <?php if (!empty($errors['auth'])): ?>
                    <p class="error mb-4"><?= htmlspecialchars($errors['auth'][0]) ?></p>
                <?php endif; ?>

                <form action="/login" method="POST" class="space-y-6">
                    <input type="hidden" name="_back_url" value="/login">

                    <!-- Email Input -->
                    <div class="relative">
                        <label class="absolute -top-2.5 left-3 bg-surface-container-lowest px-1 font-label-md text-label-md text-on-surface-variant z-10" for="email">Correo Electrónico</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-outline-variant pointer-events-none">mail</span>
                            <input id="email" name="correo" type="email" required placeholder="tunombre@correo.com" value="<?= htmlspecialchars($old['correo'] ?? '') ?>" class="w-full pl-12 pr-4 py-3 bg-surface rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md text-on-surface placeholder-outline-variant" />
                        </div>
                        <?php if (!empty($errors['correo'])): ?>
                            <p class="error mt-2"><?= htmlspecialchars($errors['correo'][0]) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Password Input -->
                    <div class="relative">
                        <label class="absolute -top-2.5 left-3 bg-surface-container-lowest px-1 font-label-md text-label-md text-on-surface-variant z-10" for="password">Contraseña</label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-outline-variant pointer-events-none">lock</span>
                            <input id="password" name="contrasena" type="password" required placeholder="••••••••" class="w-full pl-12 pr-12 py-3 bg-surface rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors font-body-md text-body-md text-on-surface placeholder-outline-variant" />
                            <button id="togglePwd" type="button" class="absolute right-4 text-outline-variant hover:text-on-surface transition-colors focus:outline-none">
                                <span id="toggleIcon" class="material-symbols-outlined">visibility_off</span>
                            </button>
                        </div>
                        <?php if (!empty($errors['contrasena'])): ?>
                            <p class="error mt-2"><?= htmlspecialchars($errors['contrasena'][0]) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary bg-surface transition-colors" <?= !empty($old['remember']) ? 'checked' : '' ?> />
                            <span class="font-label-md text-label-md text-on-surface-variant group-hover:text-on-surface transition-colors">Recuérdame</span>
                        </label>
                        <a class="font-label-md text-label-md text-tertiary hover:text-tertiary-container underline transition-colors" href="#">¿Olvidaste tu contraseña?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 bg-primary text-on-primary font-label-md text-label-md rounded-lg shadow-sm hover:bg-primary-container hover:shadow-md active:scale-[0.98] transition-all duration-200">Ingresar</button>
                </form>

                <!-- Divider -->
                <div class="mt-8 mb-6 relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-surface-variant"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-surface-container-lowest font-body-md text-body-md text-on-surface-variant">O</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="font-body-md text-body-md text-on-surface-variant mb-2">¿No tienes cuenta?</p>
                    <a class="font-label-md text-label-md text-tertiary hover:text-tertiary-container hover:underline transition-colors" href="/registro">Regístrate</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const toggle = document.getElementById('togglePwd');
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (!toggle || !input) return;
        toggle.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility_off';
            }
        });
    })();
</script>