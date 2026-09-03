-- ============================================================
-- MIGRACIÓN: Lista de Chequeo FO-TH-027 (V9) con soporte adjunto
-- Ejecutar en phpMyAdmin -> base sgth_talento_humano -> Importar
-- No borra datos existentes.
-- ============================================================

-- 1. Columnas nuevas en documentos_requeridos: código del formato y orden
ALTER TABLE documentos_requeridos
  ADD COLUMN codigo VARCHAR(30) NULL AFTER nombre,
  ADD COLUMN orden   INT NOT NULL DEFAULT 0 AFTER activo;

-- 2. Columnas nuevas en empleado_documentos: el archivo adjunto (soporte)
ALTER TABLE empleado_documentos
  ADD COLUMN archivo_path             VARCHAR(255) NULL,
  ADD COLUMN archivo_nombre_original  VARCHAR(255) NULL,
  ADD COLUMN archivo_fecha_subida     TIMESTAMP NULL;

-- 3. Tabla para el pie del formato: "Revisó en archivo" y "Firma"
CREATE TABLE IF NOT EXISTS empleado_checklist_firma (
  empleado_id        INT PRIMARY KEY,
  reviso_en_archivo  VARCHAR(150) NULL,
  firma_reviso       VARCHAR(150) NULL,
  fecha_revision     DATE NULL,
  updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Desactiva el catálogo genérico anterior (conserva el historial ya diligenciado,
--    no se borra ningún registro de empleado_documentos)
UPDATE documentos_requeridos SET activo = 0;

-- 5. Inserta los 23 ítems oficiales del formato FO-TH-027 V9
INSERT INTO documentos_requeridos (codigo, nombre, descripcion, obligatorio, activo, orden) VALUES
('FO-TH-18',            'Formato Único Hoja de Vida',                                      NULL, 1, 1, 1),
('FO-TH-019',           'Formato Resumen Hoja de Vida',                                     NULL, 1, 1, 2),
('FO-TH-013',           'Formato ficha sociodemográfica',                                   NULL, 1, 1, 3),
(NULL,                  'Copia de documento de identidad al 150%',                          NULL, 1, 1, 4),
(NULL,                  'Copia de Libreta Militar',                                         'Si aplica', 0, 1, 5),
(NULL,                  'Copia de Tarjeta Profesional',                                     'Si aplica', 0, 1, 6),
(NULL,                  'Certificado de vigencia y antecedentes disciplinarios profesionales', 'Si aplica', 0, 1, 7),
(NULL,                  'Certificados Académicos de estudios formales y educación continuada', 'Bachiller, Técnico, Tecnólogo, Pregrados, Posgrados, Cursos, Seminarios, Diplomados, etc.', 1, 1, 8),
(NULL,                  'Apostillado y Convalidación ante el MEN',                          'Estudios de pregrado y posgrado realizados fuera del país (si aplica)', 0, 1, 9),
(NULL,                  'Certificados Laborales',                                           NULL, 1, 1, 10),
(NULL,                  'Certificado de afiliación a EPS (Salud)',                          NULL, 1, 1, 11),
(NULL,                  'Certificado de afiliación a AFP (Pensión)',                        NULL, 1, 1, 12),
(NULL,                  'Certificado de Procuraduría',                                      NULL, 1, 1, 13),
(NULL,                  'Certificado de Contraloría',                                       NULL, 1, 1, 14),
(NULL,                  'Certificado Judicial (Policía Nacional)',                          NULL, 1, 1, 15),
(NULL,                  'Certificado Sistema Registro Nacional de Medidas Correctivas RNMC', 'Policía Nacional', 1, 1, 16),
(NULL,                  'Certificado de Inhabilidades por delitos sexuales contra menores', 'Ley 1918 de 2018 — Policía Nacional', 1, 1, 17),
(NULL,                  'Certificación Cuenta Bancaria',                                    NULL, 1, 1, 18),
('FO-TH-006',           'Formato de Requerimiento de Personal',                             NULL, 1, 1, 19),
('FO-TH-009 / FO-TH-031','Formato de Entrevista de Personal (Administrativo o Docente)',    NULL, 1, 1, 20),
(NULL,                  'Prueba Psicotécnica',                                              NULL, 1, 1, 21),
(NULL,                  'Examen Médico Ocupacional',                                        NULL, 1, 1, 22),
(NULL,                  'Contrato Laboral',                                                 NULL, 1, 1, 23);

-- 6. Inicializa la lista de chequeo con los 23 ítems nuevos para TODOS los
--    empleados que ya existían (para que no queden vacíos), sin tocar
--    lo que ya tenían diligenciado en el catálogo anterior.
INSERT INTO empleado_documentos (empleado_id, documento_id, entregado)
SELECT e.id, dr.id, 0
FROM empleados e
CROSS JOIN documentos_requeridos dr
WHERE dr.activo = 1
  AND NOT EXISTS (
    SELECT 1 FROM empleado_documentos ed
    WHERE ed.empleado_id = e.id AND ed.documento_id = dr.id
  );
