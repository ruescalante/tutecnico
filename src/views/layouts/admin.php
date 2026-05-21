<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'TuTecnico Admin') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-lowest": "#ffffff",
                        "surface": "#f3faff",
                        "on-tertiary-fixed-variant": "#004a75",
                        "on-secondary-fixed": "#0e1e1e",
                        "on-error-container": "#93000a",
                        "surface-container-low": "#e6f6ff",
                        "outline": "#6e7a76",
                        "on-secondary-container": "#576867",
                        "on-surface-variant": "#3e4946",
                        "on-tertiary-fixed": "#001d32",
                        "inverse-primary": "#7ad7c6",
                        "tertiary": "#005788",
                        "on-secondary": "#ffffff",
                        "inverse-on-surface": "#dff4ff",
                        "tertiary-fixed-dim": "#96ccff",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed-variant": "#3a4a49",
                        "primary": "#005e53",
                        "on-tertiary": "#ffffff",
                        "surface-dim": "#c7dde9",
                        "surface-container-high": "#d5ecf8",
                        "error": "#ba1a1a",
                        "on-primary-container": "#a1feec",
                        "surface-tint": "#006b5e",
                        "tertiary-fixed": "#cee5ff",
                        "secondary": "#516161",
                        "on-surface": "#071e27",
                        "secondary-fixed-dim": "#b8cac9",
                        "inverse-surface": "#1e333c",
                        "primary-fixed-dim": "#7ad7c6",
                        "on-primary-fixed-variant": "#005047",
                        "surface-variant": "#cfe6f2",
                        "tertiary-container": "#0070ae",
                        "primary-fixed": "#97f3e2",
                        "on-primary-fixed": "#00201b",
                        "surface-bright": "#f3faff",
                        "surface-container-highest": "#cfe6f2",
                        "background": "#f3faff",
                        "secondary-fixed": "#d4e6e5",
                        "secondary-container": "#d4e6e5",
                        "surface-container": "#dbf1fe",
                        "on-error": "#ffffff",
                        "on-tertiary-container": "#e0eeff",
                        "on-background": "#071e27",
                        "outline-variant": "#bdc9c5",
                        "primary-container": "#00796b",
                        "on-primary": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "48px",
                        "container-max-width": "1200px",
                        "base-unit": "8px",
                        "margin-mobile": "16px",
                        "gutter": "24px"
                    },
                    "fontFamily": {
                        "label-md": ["Work Sans"],
                        "headline-lg-mobile": ["Work Sans"],
                        "headline-lg": ["Work Sans"],
                        "headline-xl": ["Work Sans"],
                        "body-md": ["Work Sans"],
                        "body-lg": ["Work Sans"],
                        "headline-md": ["Work Sans"]
                    },
                    "fontSize": {
                        "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500" }],
                        "headline-lg-mobile": ["28px", { "lineHeight": "36px", "fontWeight": "600" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "fontWeight": "600" }],
                        "headline-xl": ["40px", { "lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    <link rel="stylesheet" href="/css/app.css?v=2">
    <?= $extraCss ?? '' ?>
</head>

<body class="bg-background text-on-background flex h-screen overflow-hidden">

<!-- Sidebar -->
<aside class="hidden md:flex flex-col h-full py-4 border-r border-outline-variant bg-surface-container-low shadow-sm w-64 flex-shrink-0 z-20">

    <!-- Logo area -->
    <div class="px-6 mb-6">
        <a href="/" class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">home_repair_service</span>
            <span class="text-headline-md font-headline-md text-primary">TuTécnico</span>
        </a>
        <p class="text-xs text-on-surface-variant mt-1 pl-8">Panel de administrador</p>
    </div>

    <!-- Nav links -->
    <nav class="flex flex-col space-y-1 flex-1 px-2">
        <?php
        $navLinks = [
            'usuarios'    => ['href' => '/dashboard/admin/usuarios',    'icon' => 'group',      'label' => 'Usuarios'],
            'tecnicos'    => ['href' => '/dashboard/admin/tecnicos',    'icon' => 'engineering','label' => 'Técnicos'],
            'solicitudes' => ['href' => '/dashboard/admin/solicitudes', 'icon' => 'assignment', 'label' => 'Solicitudes'],
        ];
        foreach ($navLinks as $key => $link):
            $isActive = ($activeSection ?? '') === $key;
            $baseCls  = 'flex items-center px-4 py-3 mx-0 my-0.5 rounded-lg transition-all';
            $activeCls = $isActive
                ? 'bg-primary-container text-on-primary-container font-bold'
                : 'text-on-surface-variant hover:bg-secondary-container';
        ?>
        <a href="<?= $link['href'] ?>" class="<?= $baseCls . ' ' . $activeCls ?>">
            <span class="material-symbols-outlined mr-3 text-xl"
                  style="font-variation-settings: 'FILL' <?= $isActive ? 1 : 0 ?>;">
                <?= $link['icon'] ?>
            </span>
            <span class="text-label-md font-label-md"><?= $link['label'] ?></span>
        </a>
        <?php endforeach; ?>

        <div class="border-t border-outline-variant/40 my-2"></div>

        <a href="/" class="flex items-center px-4 py-3 rounded-lg text-on-surface-variant hover:bg-secondary-container transition-all">
            <span class="material-symbols-outlined mr-3 text-xl">home</span>
            <span class="text-label-md font-label-md">Ir al sitio</span>
        </a>
    </nav>

    <!-- Profile + logout -->
    <div class="px-4 flex flex-col space-y-2 mt-4">
        <div class="flex items-center px-3 py-3 rounded-lg bg-surface border border-outline-variant/30">
            <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center mr-3 flex-shrink-0">
                <span class="text-on-primary-container font-bold text-label-md">
                    <?= mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                </span>
            </div>
            <div class="flex flex-col flex-1 min-w-0">
                <span class="text-label-md font-label-md text-on-surface truncate font-bold">
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                </span>
                <span class="text-xs text-on-surface-variant">Administrador</span>
            </div>
        </div>
        <form action="/logout" method="POST">
            <button type="submit"
                    class="w-full flex items-center px-4 py-3 text-on-surface-variant hover:bg-secondary-container rounded-lg transition-all">
                <span class="material-symbols-outlined mr-3 text-xl">logout</span>
                <span class="text-label-md font-label-md">Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>

<!-- Main content -->
<div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
    <main class="flex-1 overflow-y-auto bg-background p-4 md:p-10">
        <div class="max-w-screen-xl mx-auto">

            <?php if (!empty($success)): ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-primary-container/20 text-primary border border-primary/20 text-label-md font-label-md flex items-center gap-2">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($errors['auth'])): ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-error-container text-on-error-container border border-error/20 text-label-md font-label-md flex items-center gap-2">
                <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;">error</span>
                <?= htmlspecialchars($errors['auth'][0]) ?>
            </div>
            <?php endif; ?>

            <?= $content ?? ''  ?>
        </div>
    </main>
</div>

<script>
(function () {
    document.addEventListener('click', function (e) {
        const toggle = e.target.closest('[data-dropdown-toggle]');

        document.querySelectorAll('[data-dropdown]').forEach(function (el) {
            if (!toggle || el.id !== toggle.dataset.dropdownToggle) {
                el.classList.add('hidden');
            }
        });

        if (toggle) {
            e.stopPropagation();
            const target = document.getElementById(toggle.dataset.dropdownToggle);
            if (target) {
                target.classList.toggle('hidden');
            }
        }
    });
})();
</script>

<?= $extraJs ?? '' ?>
</body>
</html>
