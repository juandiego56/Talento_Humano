<?php
define('ROOT', dirname(__DIR__));

require_once ROOT . '/config/app.php';
require_once ROOT . '/config/database.php';
require_once ROOT . '/app/helpers/Session.php';
require_once ROOT . '/app/helpers/DB.php';
require_once ROOT . '/app/helpers/Auth.php';
require_once ROOT . '/app/helpers/Router.php';
require_once ROOT . '/app/helpers/View.php';
require_once ROOT . '/app/helpers/CalculadoraSalarial.php';
require_once ROOT . '/app/helpers/ImagenHelper.php';

// Errores: en modo normal no se muestran en pantalla (rutas internas, SQL, etc.); quedan en el log del servidor.
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

Session::start();

// Soporta mod_rewrite (?url=...) y acceso directo (/index.php?url=...)
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

try {
    (new Router())->dispatch($url);
} catch (\Throwable $e) {
    error_log('SGTH: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    if (APP_DEBUG) throw $e;
    if (!headers_sent()) http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Error — SGTH</title></head>'
       . '<body style="font-family:Lato,Arial,sans-serif;background:#eef2f7;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px">'
       . '<div style="background:#fff;border-radius:14px;padding:32px;max-width:440px;box-shadow:0 10px 40px rgba(10,37,64,.12);text-align:center">'
       . '<h1 style="color:#0a2540;font-size:22px;margin:0 0 10px">Algo salió mal</h1>'
       . '<p style="color:#64748b;line-height:1.5;margin:0 0 18px">No pudimos completar la solicitud. Inténtalo de nuevo en unos segundos; si el problema continúa, avisa al administrador del sistema.</p>'
       . '<a href="' . APP_URL . '/" style="display:inline-block;background:#0a2540;color:#fff;text-decoration:none;padding:11px 20px;border-radius:10px;font-weight:700">Volver al inicio</a>'
       . '</div></body></html>';
}
