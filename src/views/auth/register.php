<div class="bg-surface font-body-md text-on-surface antialiased min-h-screen flex flex-col relative overflow-hidden">
  <div class="absolute inset-0 z-0 bg-gradient-to-br from-surface to-surface-container-high opacity-80 pointer-events-none"></div>
  <main class="relative z-10 min-h-screen flex items-center justify-center p-6 md:p-12 w-full">
    <div class="max-w-container-max-width w-full grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
      <!-- Left: Illustration -->
      <div class="hidden md:flex flex-col items-center justify-center space-y-8 pr-12">
        <img alt="Technician Illustration" class="w-full max-w-md object-contain drop-shadow-xl rounded-xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbRrjw2osvmEK6oKJqCzqtTVYrX_WuV_R3W0KBiYDP2yVm8G9ZB5MlEVwRku_gjDwnPy9rUGOiTCt-2XPxI_cXIvV7lqCHRqnMHSb9E73p8gGr_G6WYfnT3Bj-wFcsf1MlBOnGIO6xMXwEqIRBwY25DOndcPx_e9LaG7i_FpAbRDf33fdLLX-41n7UN4qCr2J43EgFYF4StdHW1qCT97TyTAFrnnhO7DzPVITBrc_OB9sPF5H9OOfI28FzAFVeVfIGl2ruxzCchfs" />
        <div class="text-center max-w-sm">
          <h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Expertos a tu servicio.</h2>
          <p class="font-body-md text-body-md text-on-surface-variant">Conecta con profesionales calificados para resolver tus necesidades técnicas hoy mismo.</p>
        </div>
      </div>

      <!-- Right: Registration Card -->
      <div class="bg-surface-container-lowest rounded-xl shadow-[0_12px_32px_-12px_rgba(0,94,83,0.08)] border border-outline-variant/30 p-8 md:p-10 w-full max-w-md mx-auto md:ml-auto">
        <div class="mb-8">
          <h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">Regístrate</h1>
          <p class="font-body-md text-body-md text-on-surface-variant">Completa el formulario para crear una cuenta.</p>
        </div>

        <?php if (!empty($errors['auth'])): ?>
          <p class="error mb-4"><?= htmlspecialchars($errors['auth'][0]) ?></p>
        <?php endif; ?>

        <form action="/registro" method="POST" class="space-y-6">
          <input type="hidden" name="_back_url" value="/registro">

          <!-- Nombre completo -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-outline">person</span>
            </div>
            <input id="nombre" name="nombre" type="text" placeholder="Nombre Completo" required value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" class="w-full pl-10 pr-3 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <?php if (!empty($errors['nombre'])): ?>
              <p class="error mt-2"><?= htmlspecialchars($errors['nombre'][0]) ?></p>
            <?php endif; ?>
          </div>

          <!-- Correo -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-outline">mail</span>
            </div>
            <input id="correo" name="correo" type="email" placeholder="Correo Electrónico" required value="<?= htmlspecialchars($old['correo'] ?? '') ?>" class="w-full pl-10 pr-3 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <?php if (!empty($errors['correo'])): ?>
              <p class="error mt-2"><?= htmlspecialchars($errors['correo'][0]) ?></p>
            <?php endif; ?>
          </div>

          <!-- Teléfono (opcional) -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-outline">phone</span>
            </div>
            <input id="telefono" name="telefono" type="text" placeholder="Teléfono (opcional)" value="<?= htmlspecialchars($old['telefono'] ?? '') ?>" class="w-full pl-10 pr-3 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
          </div>

          <!-- Contraseña -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-outline">lock</span>
            </div>
            <input id="password" name="contrasena" type="password" placeholder="Contraseña" required class="w-full pl-10 pr-10 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <button id="togglePwdReg" class="absolute inset-y-0 right-0 pr-3 flex items-center text-outline hover:text-on-surface-variant focus:outline-none" type="button">
              <span id="toggleIconReg" class="material-symbols-outlined">visibility_off</span>
            </button>
            <?php if (!empty($errors['contrasena'])): ?>
              <p class="error mt-2"><?= htmlspecialchars($errors['contrasena'][0]) ?></p>
            <?php endif; ?>
          </div>

          <!-- Confirmar contraseña -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-outline">lock</span>
            </div>
            <input id="contrasena_confirmacion" name="contrasena_confirmacion" type="password" placeholder="Confirmar Contraseña" required class="w-full pl-10 pr-10 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md text-on-surface placeholder-on-surface-variant focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <button id="togglePwdConfReg" class="absolute inset-y-0 right-0 pr-3 flex items-center text-outline hover:text-on-surface-variant focus:outline-none" type="button">
              <span id="toggleIconConfReg" class="material-symbols-outlined">visibility_off</span>
            </button>
            <?php if (!empty($errors['contrasena_confirmacion'])): ?>
              <p class="error mt-2"><?= htmlspecialchars($errors['contrasena_confirmacion'][0]) ?></p>
            <?php endif; ?>
          </div>

          <!-- Terms -->
          <div class="flex items-start mt-4">
            <div class="flex items-center h-5">
              <input id="terms" name="terms" type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-outline-variant rounded bg-surface-container-lowest" <?= !empty($old['terms']) ? 'checked' : '' ?> />
            </div>
            <div class="ml-3 text-sm">
              <label class="font-body-md text-label-md text-on-surface-variant" for="terms">Acepto los <a class="text-primary hover:underline font-medium" href="#">Términos y Condiciones</a> de uso y la <a class="text-primary hover:underline font-medium" href="#">Política de Privacidad</a>.</label>
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="w-full bg-primary-container text-on-primary py-3 px-4 rounded-lg font-label-md text-label-md font-bold shadow-sm hover:bg-primary transition-all active:scale-[0.98] mt-6 flex justify-center items-center">Crear Cuenta</button>
        </form>

        <div class="mt-8 text-center">
          <p class="font-body-md text-body-md text-on-surface-variant">¿Ya tienes una cuenta? <a class="font-label-md text-label-md text-primary font-bold hover:underline transition-colors" href="/login">Inicia Sesión</a></p>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  (function(){
    const toggle = document.getElementById('togglePwdReg');
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIconReg');
    if (toggle && input){
      toggle.addEventListener('click', ()=>{
        if (input.type === 'password'){ input.type = 'text'; icon.textContent = 'visibility'; }
        else { input.type = 'password'; icon.textContent = 'visibility_off'; }
      });
    }
    const toggle2 = document.getElementById('togglePwdConfReg');
    const input2 = document.getElementById('contrasena_confirmacion');
    const icon2 = document.getElementById('toggleIconConfReg');
    if (toggle2 && input2){
      toggle2.addEventListener('click', ()=>{
        if (input2.type === 'password'){ input2.type = 'text'; icon2.textContent = 'visibility'; }
        else { input2.type = 'password'; icon2.textContent = 'visibility_off'; }
      });
    }
  })();
</script>
