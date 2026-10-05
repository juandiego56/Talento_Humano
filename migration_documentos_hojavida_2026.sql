-- ============================================================
-- MIGRACIÓN: Documentos del empleado, hoja de vida y verificación
-- Ejecutar en phpMyAdmin -> base sgth_talento_humano -> pestaña SQL
-- (o mediante el runner temporal). No borra datos existentes.
-- ============================================================

-- 1. Ocultar del empleado los 5 documentos que NO sube él mismo
--    (Requerimiento de Personal, Entrevista, Prueba Psicotécnica,
--     Examen Médico, Contrato Laboral) — quedan como registro interno.
ALTER TABLE documentos_requeridos
  ADD COLUMN visible_empleado TINYINT(1) NOT NULL DEFAULT 1 AFTER obligatorio;

UPDATE documentos_requeridos SET visible_empleado = 0 WHERE orden IN (19,20,21,22,23);

-- 2. Hoja de vida: fecha de expedición del título, estudios en curso/incompletos,
--    y soporte del título (PDF) por cada bloque de formación académica.
ALTER TABLE empleado_educacion
  ADD COLUMN fecha_expedicion DATE NULL AFTER anio_graduacion,
  ADD COLUMN en_curso TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_expedicion,
  ADD COLUMN archivo_titulo_path VARCHAR(255) NULL AFTER en_curso,
  ADD COLUMN archivo_titulo_nombre_original VARCHAR(255) NULL AFTER archivo_titulo_path,
  ADD COLUMN archivo_titulo_peso_kb INT NULL AFTER archivo_titulo_nombre_original;

-- 3. Cuenta bancaria (dato estructurado; el soporte de certificación
--    bancaria ya existe como documento #18 del checklist).
ALTER TABLE empleados
  ADD COLUMN banco VARCHAR(100) NULL AFTER foto_path,
  ADD COLUMN tipo_cuenta ENUM('ahorros','corriente') NULL AFTER banco,
  ADD COLUMN numero_cuenta VARCHAR(40) NULL AFTER tipo_cuenta;

-- 4. Verificación de documentos cargados: peso comprimido, estado y motivo
--    de devolución (por ejemplo, "no legible").
ALTER TABLE empleado_documentos
  ADD COLUMN peso_kb INT NULL AFTER archivo_fecha_subida,
  ADD COLUMN estado_verificacion ENUM('pendiente','aprobado','devuelto') NOT NULL DEFAULT 'pendiente' AFTER peso_kb,
  ADD COLUMN motivo_devolucion VARCHAR(255) NULL AFTER estado_verificacion,
  ADD COLUMN verificado_por VARCHAR(150) NULL AFTER motivo_devolucion,
  ADD COLUMN verificado_en TIMESTAMP NULL AFTER verificado_por;

-- 5. Hoja de vida: marcar una sección como "no aplica / tachado" con motivo
--    (lista de motivos predefinidos + "Otro" en texto libre).
CREATE TABLE IF NOT EXISTS empleado_hojavida_notas (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id   INT NOT NULL,
  seccion       VARCHAR(80) NOT NULL COMMENT 'nombre de la sección/campo marcado',
  motivo        VARCHAR(255) NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;
