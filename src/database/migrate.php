#!/usr/bin/env php
<?php
// Cargar .env manualmente si existe
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

require_once __DIR__ . '/../config/database.php';

$runSeeds = in_array('--seed', $argv ?? [], true);

function runSqlFile(PDO $pdo, string $filepath, string $label): void
{
    if (!file_exists($filepath)) {
        echo "  [ERROR] No se encontró: $filepath\n";
        return;
    }

    $sql = file_get_contents($filepath);

    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => $s !== ''
    );

    $failed = false;
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            echo "  [FAIL] Error en $label: " . $e->getMessage() . "\n";
            echo "  Statement: " . substr($statement, 0, 100) . "...\n";
            $failed = true;
            break;
        }
    }

    if (!$failed) {
        echo "  [OK] $label ejecutado correctamente.\n";
    } else {
        exit(1);
    }
}

// ---------------------------------------------------------------
echo "\n=== Iniciando migraciones ===\n";
$pdo = Database::getInstance();

runSqlFile($pdo, __DIR__ . '/migrations.sql', 'migrations.sql');

if ($runSeeds) {
    echo "\n=== Ejecutando seeds ===\n";
    runSqlFile($pdo, __DIR__ . '/seeds.sql', 'seeds.sql');
}

echo "\n=== Listo. ===\n\n";