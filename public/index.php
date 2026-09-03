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

Session::start();

// Soporta mod_rewrite (?url=...) y acceso directo (/index.php?url=...)
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

(new Router())->dispatch($url);
