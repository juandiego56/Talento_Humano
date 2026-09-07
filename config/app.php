<?php
define('APP_NAME',    'SGTH — Talento Humano');
define('APP_URL',     'http://localhost/sgth/public');
define('APP_VERSION', '1.0.0');
define('TIMEZONE',    'America/Bogota');
define('SESSION_NAME','sgth_session');

// Roles del sistema
define('ROL_ADMIN',    1); // Administrador del sistema (control total)
define('ROL_GESTOR',   2); // Gestor de Talento Humano (opera los 3 módulos)
define('ROL_EMPLEADO', 3); // Empleado (consulta su propia información)

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
define('CHECKLIST_EXT_PERMITIDAS', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

// ── Foto de perfil del empleado/usuario ─────────────────────────
define('FOTO_UPLOAD_DIR', ROOT . '/public/uploads/fotos'); // dentro de /public: sí es accesible por URL (no es info sensible)
define('FOTO_UPLOAD_URL', APP_URL . '/uploads/fotos');
define('FOTO_MAX_SIZE',   2 * 1024 * 1024); // 2 MB
define('FOTO_EXT_PERMITIDAS', ['jpg', 'jpeg', 'png', 'webp']);
