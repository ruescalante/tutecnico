<?php
$categorias = [
    [
        'nombre' => 'Plomería',
        'img'    => '/img/cat-plomeria.jpg',
        'alt'    => 'Plomero trabajando en tuberías bajo un lavamanos',
    ],
    [
        'nombre' => 'Electricidad',
        'img'    => '/img/cat-electricidad.jpg',
        'alt'    => 'Electricista revisando panel eléctrico con guantes de seguridad',
    ],
    [
        'nombre' => 'Climatización',
        'img'    => '/img/cat-climatizacion.jpg',
        'alt'    => 'Técnico instalando aire acondicionado en pared',
    ],
    [
        'nombre' => 'Pintura',
        'img'    => '/img/cat-pintura.jpg',
        'alt'    => 'Pintor aplicando pintura blanca con rodillo en pared interior',
    ],
    [
        'nombre' => 'Cerrajería',
        'img'    => '/img/cat-cerrajeria.jpg',
        'alt'    => 'Manos insertando llave en cerradura de puerta de madera',
    ],
];

$destino = isset($_SESSION['user_id']) ? '/dashboard' : '/login';
?>

<main class="flex-grow flex flex-col">

  <!-- Hero Section -->
  <section class="relative w-full h-[700px] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
      <img
        alt="Interior moderno de lujo con técnico profesional"
        class="w-full h-full object-cover"
        src="/uploads/heroimage.jpg"
      />
      <div class="absolute inset-0 bg-gradient-to-r from-on-primary-fixed/90 via-on-primary-fixed/40 to-transparent"></div>
    </div>
    <div class="relative z-10 w-full max-w-container-max-width mx-auto px-margin-mobile md:px-margin-desktop">
      <div class="max-w-2xl">
        <h1 class="font-headline-xl text-5xl md:text-6xl text-white mb-6 leading-[1.1] font-extrabold">
          TuTécnico: Encuentra a tu experto ideal
        </h1>
        <p class="font-body-lg text-body-lg text-white/90 mb-10 max-w-xl font-medium">
          Conecta con técnicos certificados y verificados para cualquier reparación o instalación en tu hogar.
        </p>
        <div class="flex flex-col gap-6">
          <div class="relative flex items-center w-full max-w-lg">
            <span class="material-symbols-outlined absolute left-5 text-on-surface-variant">search</span>
            <input
              class="w-full bg-white text-on-surface font-body-md py-4 pl-14 pr-32 rounded-full border-none focus:ring-2 focus:ring-primary shadow-xl"
              placeholder="¿Qué servicio necesitas?"
              type="text"
            />
            <a href="<?= htmlspecialchars($destino) ?>"
               class="absolute right-2 bg-primary hover:bg-primary-container text-white font-label-md py-2.5 px-6 rounded-full transition-all active:scale-95">
              Buscar
            </a>
          </div>
          <div class="flex items-center gap-3">
            <div class="flex -space-x-2">
              <div class="w-8 h-8 rounded-full border-2 border-white bg-secondary-container overflow-hidden">
                <img alt="Foto técnico destacado" class="w-full h-full object-cover"
                     src="/img/avatar-demo-1.jpg" />
              </div>
              <div class="w-8 h-8 rounded-full border-2 border-white bg-secondary-container overflow-hidden">
                <img alt="Foto técnico destacado" class="w-full h-full object-cover"
                     src="/img/avatar-demo-2.jpg" />
              </div>
              <div class="w-8 h-8 rounded-full border-2 border-white bg-primary text-white flex items-center justify-center text-[10px] font-bold">+10k</div>
            </div>
            <p class="text-white/80 text-sm font-medium flex items-center gap-1">
              <span class="material-symbols-outlined text-yellow-400 text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
              Más de 10,000 técnicos verificados
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-container-max-width mx-auto w-full px-margin-mobile md:px-margin-desktop py-12 flex flex-col gap-16">

    <!-- Categorías Populares -->
    <section>
      <h2 class="font-headline-lg text-headline-md md:text-headline-lg text-on-surface mb-8">Categorías Populares</h2>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6">
        <?php foreach ($categorias as $cat): ?>
        <a class="group relative rounded-xl overflow-hidden aspect-[4/3] ambient-shadow transition-transform duration-300 hover:-translate-y-1 block" href="<?= htmlspecialchars($destino) ?>">
          <img alt="<?= htmlspecialchars($cat['alt']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="<?= $cat['img'] ?>" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
          <span class="absolute bottom-4 left-4 font-label-md text-label-md text-white font-semibold"><?= htmlspecialchars($cat['nombre']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Técnicos Destacados -->
    <section class="bg-surface-container-low rounded-2xl p-6 md:p-8 -mx-4 md:mx-0">
      <h2 class="font-headline-lg text-headline-md md:text-headline-lg text-on-surface mb-8">Técnicos Destacados</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($featuredTecnicos as $tec):
          $cats     = $tec['categorias'] ? explode(', ', $tec['categorias']) : [];
          $primera  = $cats[0] ?? 'Técnico';
          $rating   = number_format((float) $tec['avg_rating'], 1);
          $servicios = (int) $tec['service_count'];
          $inicial  = mb_strtoupper(mb_substr($tec['nombre'], 0, 1));
        ?>
        <div class="bg-surface rounded-xl p-5 border border-outline-variant shadow-sm hover:shadow-lg hover:-translate-y-1.5 transition-all duration-300 flex flex-col gap-4 group">
          <div class="flex gap-4">
            <div class="relative w-24 h-28 rounded-lg overflow-hidden flex-shrink-0">
              <?php if (!empty($tec['foto_perfil'])): ?>
                <img alt="Foto de <?= htmlspecialchars($tec['nombre']) ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars($tec['foto_perfil']) ?>" />
              <?php else: ?>
                <div class="w-full h-full bg-primary-container flex items-center justify-center text-on-primary text-3xl font-bold">
                  <?= $inicial ?>
                </div>
              <?php endif; ?>
              <div class="absolute top-1 right-1 bg-white/90 rounded-full p-1 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">verified</span>
              </div>
            </div>
            <div class="flex-grow flex flex-col">
              <div class="flex items-center justify-between">
                <h3 class="font-headline-md text-body-lg font-bold text-on-surface"><?= htmlspecialchars($tec['nombre']) ?></h3>
              </div>
              <p class="font-body-md text-on-surface-variant text-sm mb-1"><?= htmlspecialchars($primera) ?></p>
              <div class="flex items-center gap-1.5 mb-2">
                <div class="flex items-center">
                  <span class="material-symbols-outlined text-yellow-500 text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                  <span class="font-bold text-on-surface text-sm ml-0.5"><?= $rating ?></span>
                </div>
                <span class="text-outline text-xs">•</span>
                <span class="text-on-surface-variant text-xs"><?= $servicios ?>+ servicios</span>
              </div>
              <?php if ($tec['disponibilidad']): ?>
              <div class="flex items-center gap-1 text-[10px] uppercase tracking-wider font-bold text-primary bg-primary/5 px-2 py-0.5 rounded w-fit">
                <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                Disponibilidad inmediata
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($cats as $c): ?>
            <span class="px-2 py-1 bg-surface-variant text-on-surface-variant text-[11px] rounded font-medium"><?= htmlspecialchars($c) ?></span>
            <?php endforeach; ?>
          </div>
          <a href="/tecnico/<?= (int) $tec['id'] ?>"
             class="w-full bg-primary-container hover:bg-primary text-white font-label-md text-label-md py-2.5 rounded-lg transition-all duration-200 shadow-md group-hover:shadow-primary/20 text-center block">
            Ver Perfil
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Cómo Funciona -->
    <section class="pb-8">
      <h2 class="font-headline-lg text-headline-md md:text-headline-lg text-on-surface mb-12 text-center md:text-left">Cómo Funciona</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-8">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-4 text-center md:text-left">
          <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center flex-shrink-0 text-primary">
            <span class="material-symbols-outlined text-[32px]">search</span>
          </div>
          <div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2 text-xl">Busca el servicio</h3>
            <p class="font-body-md text-body-md text-on-surface-variant">Encuentra profesionales certificados cerca de ti.</p>
          </div>
        </div>
        <div class="flex flex-col md:flex-row items-center md:items-start gap-4 text-center md:text-left">
          <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center flex-shrink-0 text-primary">
            <span class="material-symbols-outlined text-[32px]">group</span>
          </div>
          <div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2 text-xl">Compara profesionales</h3>
            <p class="font-body-md text-body-md text-on-surface-variant">Revisa perfiles, calificaciones y precios.</p>
          </div>
        </div>
        <div class="flex flex-col md:flex-row items-center md:items-start gap-4 text-center md:text-left">
          <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center flex-shrink-0 text-primary">
            <span class="material-symbols-outlined text-[32px]">handshake</span>
          </div>
          <div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-2 text-xl">Contrata con confianza</h3>
            <p class="font-body-md text-body-md text-on-surface-variant">Programa y paga de forma segura a través de la plataforma.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Beneficios -->
    <section class="py-12 md:py-20 border-t border-outline-variant/30">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
          <h2 class="font-headline-lg text-headline-md md:text-headline-lg text-on-surface mb-6">Por qué elegir TuTécnico</h2>
          <p class="font-body-md text-on-surface-variant mb-8">Nuestra plataforma está diseñada para brindarte tranquilidad y resultados profesionales en cada servicio.</p>
          <ul class="space-y-6">
            <li class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-primary-container/20 flex items-center justify-center flex-shrink-0 text-primary">
                <span class="material-symbols-outlined text-[24px]">verified_user</span>
              </div>
              <div>
                <h4 class="font-headline-md text-lg text-on-surface mb-1">Expertos Verificados</h4>
                <p class="font-body-md text-sm text-on-surface-variant">Todos nuestros técnicos pasan por un riguroso proceso de selección y verificación de antecedentes.</p>
              </div>
            </li>
            <li class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-primary-container/20 flex items-center justify-center flex-shrink-0 text-primary">
                <span class="material-symbols-outlined text-[24px]">task_alt</span>
              </div>
              <div>
                <h4 class="font-headline-md text-lg text-on-surface mb-1">Trabajo Garantizado</h4>
                <p class="font-body-md text-sm text-on-surface-variant">Respaldamos cada servicio con una garantía de satisfacción para asegurar resultados de calidad.</p>
              </div>
            </li>
            <li class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-primary-container/20 flex items-center justify-center flex-shrink-0 text-primary">
                <span class="material-symbols-outlined text-[24px]">payments</span>
              </div>
              <div>
                <h4 class="font-headline-md text-lg text-on-surface mb-1">Pago Seguro</h4>
                <p class="font-body-md text-sm text-on-surface-variant">Transacciones protegidas a través de nuestra plataforma. Paga solo cuando el trabajo esté completo.</p>
              </div>
            </li>
            <li class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-primary-container/20 flex items-center justify-center flex-shrink-0 text-primary">
                <span class="material-symbols-outlined text-[24px]">support_agent</span>
              </div>
              <div>
                <h4 class="font-headline-md text-lg text-on-surface mb-1">Soporte 24/7</h4>
                <p class="font-body-md text-sm text-on-surface-variant">Nuestro equipo de atención al cliente está siempre disponible para ayudarte con cualquier duda o inconveniente.</p>
              </div>
            </li>
          </ul>
        </div>
        <div class="rounded-2xl overflow-hidden ambient-shadow">
          <img alt="Técnico profesional trabajando en instalación eléctrica" class="w-full h-auto object-cover"
               src="/img/beneficios.jpg" />
        </div>
      </div>
    </section>

  </div>

  <!-- Estadísticas de Confianza -->
  <section class="bg-surface-container-high py-16 w-full">
    <div class="max-w-container-max-width mx-auto px-margin-mobile md:px-margin-desktop">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div class="flex flex-col items-center">
          <span class="font-headline-xl text-5xl font-bold text-primary mb-2">50k+</span>
          <span class="font-body-lg font-medium text-on-surface">Servicios completados</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="font-headline-xl text-5xl font-bold text-primary mb-2">15k+</span>
          <span class="font-body-lg font-medium text-on-surface">Técnicos certificados</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="font-headline-xl text-5xl font-bold text-primary mb-2">4.9/5</span>
          <span class="font-body-lg font-medium text-on-surface">Calificación promedio</span>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-container-max-width mx-auto w-full px-margin-mobile md:px-margin-desktop py-12 flex flex-col gap-16">

    <!-- Testimonios -->
    <section>
      <h2 class="font-headline-lg text-headline-md md:text-headline-lg text-on-surface mb-12 text-center">Lo que dicen nuestros clientes</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($latestReviews as $review):
          $inicialCliente = mb_strtoupper(mb_substr($review['cliente_nombre'], 0, 1));
        ?>
        <div class="bg-surface p-8 rounded-2xl ambient-shadow flex flex-col items-center text-center">
          <div class="w-20 h-20 rounded-full overflow-hidden mb-4 border-4 border-surface-container-low flex-shrink-0">
            <?php if (!empty($review['cliente_foto'])): ?>
              <img alt="Foto de <?= htmlspecialchars($review['cliente_nombre']) ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars($review['cliente_foto']) ?>" />
            <?php else: ?>
              <div class="w-full h-full bg-primary-container flex items-center justify-center text-on-primary text-2xl font-bold">
                <?= $inicialCliente ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="flex gap-1 mb-4 text-yellow-500">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?php if ($i <= (int) $review['puntuacion']): ?>
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
              <?php else: ?>
                <span class="material-symbols-outlined text-[20px] text-outline">star</span>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
          <p class="font-body-md text-on-surface-variant italic mb-6 flex-grow line-clamp-4">"<?= htmlspecialchars($review['comentario']) ?>"</p>
          <h4 class="font-headline-md text-lg text-on-surface"><?= htmlspecialchars($review['cliente_nombre']) ?></h4>
          <span class="text-sm text-on-surface-variant">Servicio con <?= htmlspecialchars($review['tecnico_nombre']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

  </div>

  <!-- CTA Final -->
  <section class="bg-[#004d40] text-white py-20 px-4 text-center mt-auto w-full">
    <div class="max-w-3xl mx-auto">
      <h2 class="font-headline-xl text-3xl md:text-5xl font-bold mb-6">¿Listo para solucionar ese problema?</h2>
      <p class="font-body-lg text-gray-200 mb-10">Encuentra al profesional ideal hoy mismo y olvídate de las preocupaciones.</p>
      <a href="<?= htmlspecialchars($destino) ?>"
         class="inline-block bg-white text-[#004d40] hover:bg-gray-100 font-headline-md text-lg py-4 px-10 rounded-full shadow-xl transition-transform duration-300 hover:scale-105 active:scale-95">
        Solicitar Servicio Ahora
      </a>
    </div>
  </section>

</main>
