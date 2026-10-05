<?php
define('APP_NAME',    'SGTH — Talento Humano');

// APP_URL ya no queda fijo en "localhost". Usa el host con el que llegó la petición actual
// (ej. la IP de red local del computador, si se accedió así), y si se accedió por
// "localhost"/127.0.0.1 detecta automáticamente la IP de red (Wi-Fi/Ethernet) real del
// equipo — así los enlaces y códigos QR generados (ej. registro de asistencia de Bienestar)
// funcionan también desde el celular u otro equipo de la misma red, sin configuración manual,
// incluso si el equipo cambia de red WiFi (otra oficina, otro router, IP diferente cada vez).
//
// SGTH_LAN_IP solo se usa como respaldo de emergencia, por si la detección automática fallara
// (por ejemplo, en un servidor sin acceso de red saliente). Si el auto-detectado no coincide
// con lo esperado, puedes fijar aquí una IP fija; en blanco ('') significa "usar solo la
// detección automática".
const SGTH_LAN_IP = ''; // Respaldo manual (vacío = confiar en la detección automática)

/**
 * Detecta la IP de red local (LAN) real de este equipo, evitando adaptadores virtuales
 * (ej. VirtualBox, VMware) que a veces confunden a gethostbyname(gethostname()). Abre una
 * conexión UDP "de prueba" hacia una IP pública conocida (no envía datos reales, UDP no
 * establece conexión) solo para preguntarle al sistema operativo qué IP local usaría su
 * tabla de enrutamiento — que normalmente es la del adaptador Wi-Fi/Ethernet activo, no la
 * de una red virtual aislada.
 */
function sgth_detectar_ip_lan(): ?string {
    $fp = @stream_socket_client('udp://8.8.8.8:53', $errno, $errstr, 1);
    if (!$fp) return null;
    $local = stream_socket_get_name($fp, false); // formato "ip:puerto"
    fclose($fp);
    if (!$local) return null;
    $ip = substr($local, 0, strrpos($local, ':'));
    if (!filter_var($ip, FILTER_VALIDATE_IP) || $ip === '0.0.0.0') return null;
    return $ip;
}

$appEsHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

$appHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
if ($appHost === 'localhost' || strpos($appHost, '127.0.0.1') === 0) {
    $appHost = sgth_detectar_ip_lan() ?: (SGTH_LAN_IP ?: 'localhost');
}
// Conserva el puerto de la petición actual si no es el estándar (80/443) y aún no está incluido
$appPuerto = $_SERVER['SERVER_PORT'] ?? null;
if ($appPuerto && !in_array((int)$appPuerto, [80, 443], true) && strpos($appHost, ':') === false) {
    $appHost .= ':' . $appPuerto;
}

define('APP_URL', ($appEsHttps ? 'https' : 'http') . '://' . $appHost . '/sgth/public');

define('APP_VERSION', '1.0.0');
define('TIMEZONE',    'America/Bogota');
define('SESSION_NAME','sgth_session');

// Roles del sistema
define('ROL_ADMIN',              1); // Administrador del sistema (control total)
define('ROL_GESTOR',             2); // Gestor de Talento Humano (opera los 3 módulos)
define('ROL_EMPLEADO',           3); // Empleado (consulta su propia información)
define('ROL_DIRECTOR_PROGRAMA',  4); // Director de Programa académico (consulta y solicita vinculación de docentes de su programa)

// Estados de empleado
define('EMP_ACTIVO',   'activo');
define('EMP_INACTIVO', 'inactivo');
define('EMP_RETIRADO', 'retirado');

// Estados de nómina
define('NOM_BORRADOR', 'borrador');
define('NOM_PAGADA',   'pagada');
define('NOM_ANULADA',  'anulada');

// ── Calculadora de Costos Laborales (Colombia) ──────────────────
// IMPORTANTE: actualiza estos 3 valores cada año según decreto de SMMLV / auxilio de transporte.
define('CL_PISO_IBC',          1750905);   // Piso mínimo de base de cotización (salud/pensión/parafiscales)
define('CL_AUX_TRANSPORTE',    249095);    // Valor mensual del auxilio de transporte vigente
define('CL_AUX_TRANSPORTE_TOPE', 3501810); // Tope salarial para tener derecho a auxilio de transporte (2 x CL_PISO_IBC)

date_default_timezone_set(TIMEZONE);

// ── Soportes adjuntos de la Lista de Chequeo (FO-TH-027) ────────
define('CHECKLIST_UPLOAD_DIR', ROOT . '/uploads/documentos'); // fuera de /public: no accesible directo por URL
define('CHECKLIST_MAX_SIZE',   5 * 1024 * 1024);              // 5 MB por archivo
define('CHECKLIST_EXT_PERMITIDAS', ['pdf']);                  // Solo PDF (2026): asegura legibilidad y formato único

// ── Foto de perfil del empleado/usuario ─────────────────────────
define('FOTO_UPLOAD_DIR', ROOT . '/public/uploads/fotos'); // dentro de /public: sí es accesible por URL (no es info sensible)
define('FOTO_UPLOAD_URL', APP_URL . '/uploads/fotos');
define('FOTO_MAX_SIZE',   2 * 1024 * 1024); // 2 MB
define('FOTO_EXT_PERMITIDAS', ['jpg', 'jpeg', 'png', 'webp']);
