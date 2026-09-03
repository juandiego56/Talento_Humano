-- ============================================================
-- MIGRACIÓN: Formato de Entrevista de Personal Administrativo
-- FO-TH-009 V7
-- Ejecutar en phpMyAdmin -> base sgth_talento_humano -> Importar
-- No borra datos existentes.
-- ============================================================

CREATE TABLE IF NOT EXISTS entrevistas_personal (
  id                          INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id                 INT NOT NULL,

  -- Datos del entrevistador
  nombre_entrevistador        VARCHAR(150) NULL,
  cargo_entrevistador         VARCHAR(150) NULL,
  area_entrevistador          VARCHAR(150) NULL,
  fecha_entrevista            DATE NULL,
  modalidad                   ENUM('presencial','virtual') NULL,

  -- Datos del entrevistado
  nombre_entrevistado         VARCHAR(150) NULL,
  profesion                   VARCHAR(150) NULL,
  cargo_aspira                VARCHAR(150) NULL,
  posgrados_estado            ENUM('no_aplica','en_curso','culminado') NULL,
  posgrados_detalle           VARCHAR(255) NULL,
  doc_tipo                    ENUM('TI','CC','CE') NULL,
  doc_numero                  VARCHAR(30) NULL,
  doc_lugar_expedicion        VARCHAR(100) NULL,

  -- Secciones de texto libre
  informacion_personal        TEXT NULL,
  formacion_academica         TEXT NULL,
  experiencia_laboral         TEXT NULL,
  resultado_prueba_psicotecnica TEXT NULL,
  resultado_prueba_tecnica    TEXT NULL,

  -- Cierre de la entrevista
  disponibilidad_tiempo       ENUM('si','no') NULL,
  acepta_condiciones          ENUM('si','no') NULL,
  conclusiones                TEXT NULL,
  decision                    ENUM('vincular','descartar') NULL,
  firma_entrevistador         VARCHAR(150) NULL,

  created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uq_empleado_entrevista (empleado_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;
