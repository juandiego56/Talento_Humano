<?php
// Script temporal para ejecutar migration_documentos_hojavida_2026.sql
// Visitar una vez en el navegador y luego BORRAR este archivo.
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__); // este script vive en /public, el proyecto está un nivel arriba
require $root . '/config/database.php';
require $root . '/app/helpers/DB.php';

$sqlFile = $root . '/migration_documentos_hojavida_2026.sql';
if (!file_exists($sqlFile)) {
    die("No se encontró el archivo: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);

// Quita comentarios de línea (-- ...) preservando saltos de línea
$sql = preg_replace('/--.*$/m', '', $sql);

// Divide en sentencias por ';'
$statements = array_filter(array_map('trim', explode(';', $sql)));

$pdo = DB::connect();
$ok = 0; $skipped = 0; $errors = [];

foreach ($statements as $stmt) {
    if ($stmt === '') continue;
    try {
        $pdo->exec($stmt);
        $ok++;
        echo "OK: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 90) . "...\n";
    } catch (PDOException $e) {
        // 1060 = Duplicate column name, 1050 = Table already exists, 1061 = dup key
        if (in_array($e->errorInfo[1] ?? null, [1060, 1050, 1061], true)) {
            $skipped++;
            echo "YA EXISTÍA (omitido): " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 90) . "...\n";
        } else {
            $errors[] = $e->getMessage() . " -- SQL: " . substr($stmt, 0, 120);
            echo "ERROR: " . $e->getMessage() . "\n   SQL: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 120) . "\n";
        }
    }
}

echo "\n----------------------------------------\n";
echo "Sentencias ejecutadas OK: $ok\n";
echo "Sentencias omitidas (ya existían): $skipped\n";
echo "Errores: " . count($errors) . "\n";
if ($errors) {
    echo "\nDETALLE DE ERRORES:\n";
    foreach ($errors as $e) echo "- $e\n";
}
echo "\nListo. Recuerda borrar este archivo (_run_migration_2026.php) por seguridad.\n";
