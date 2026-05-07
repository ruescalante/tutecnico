<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'TuTecnico') ?></title>
    <link rel="stylesheet" href="/css/app.css">
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