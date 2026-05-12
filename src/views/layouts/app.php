<!DOCTYPE html>
<html lang="es">
<?php $layout = $layout ?? 'tailwind'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'TuTecnico') ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
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
                        "label-md": ["14px", {
                            "lineHeight": "20px",
                            "letterSpacing": "0.01em",
                            "fontWeight": "500"
                        }],
                        "headline-lg-mobile": ["28px", {
                            "lineHeight": "36px",
                            "fontWeight": "600"
                        }],
                        "headline-lg": ["32px", {
                            "lineHeight": "40px",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["40px", {
                            "lineHeight": "48px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "body-md": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "body-lg": ["18px", {
                            "lineHeight": "28px",
                            "fontWeight": "400"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "32px",
                            "fontWeight": "600"
                        }]
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

    <?php if ($layout === 'bootstrap'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <link rel="stylesheet" href="/css/app.css?v=2">
    <?= $extraCss ?? '' ?>
</head>

<body>
    <?php require_once BASE_PATH . '/views/layouts/nav.php' ?>
    <main>
        <?= $content ?? '' ?> <!-- Renderizamos las vistas, patron layout-content-->
    </main>
    <?php require_once BASE_PATH . '/views/layouts/footer.php' ?>
    <?= $extraJs ?? '' ?>
</body>

</html>