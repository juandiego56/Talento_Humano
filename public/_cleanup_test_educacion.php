<?php
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');
$root = dirname(__DIR__);
require $root . '/config/database.php';
require $root . '/app/helpers/DB.php';

$rows = DB::fetchAll("SELECT id, nivel_educativo, institucion, fecha_expedicion, en_curso FROM empleado_educacion WHERE empleado_id = 2");
foreach ($rows as $r) { echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n"; }

$n = DB::execute("DELETE FROM empleado_educacion WHERE empleado_id = 2 AND institucion IN ('Institución de Prueba', 'Institución de Prueba 2')");
echo "Filas eliminadas: $n\n";
