<?php
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');
$root = dirname(__DIR__);
require $root . '/config/database.php';
require $root . '/app/helpers/DB.php';

DB::execute("UPDATE empleados SET banco=NULL, tipo_cuenta=NULL, numero_cuenta=NULL WHERE id = 2");
echo "Revertido.\n";
