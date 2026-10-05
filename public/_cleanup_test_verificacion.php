<?php
// Script temporal: revierte el registro de prueba sembrado para probar Verificación
// de Documentos, dejando la BD y los archivos exactamente como estaban antes.
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);
require $root . '/config/database.php';
require $root . '/app/helpers/DB.php';

$empleadoId = 2;
$documentoId = 5;

DB::execute("
    UPDATE empleado_documentos
    SET entregado = 0, fecha_entrega = NULL, archivo_path = NULL, archivo_nombre_original = NULL,
        archivo_fecha_subida = NULL, peso_kb = NULL, estado_verificacion = 'pendiente',
        motivo_devolucion = NULL, verificado_por = NULL, verificado_en = NULL
    WHERE empleado_id = ? AND documento_id = ?
", [$empleadoId, $documentoId]);

$pdf = $root . '/uploads/documentos/2/5_test_verificacion.pdf';
$borrado = false;
if (is_file($pdf)) { $borrado = @unlink($pdf); }

echo "Registro de prueba revertido a su estado original.\n";
echo "Archivo de prueba " . ($borrado ? "eliminado." : (is_file($pdf) ? "NO se pudo eliminar (bórralo manualmente)." : "no existía.")) . "\n";
