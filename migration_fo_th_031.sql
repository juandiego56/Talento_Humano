-- ============================================================
-- MIGRACIÓN: Formato de Entrevista de Personal Docente
-- FO-TH-031 V2
-- Ejecutar en phpMyAdmin -> base sgth_talento_humano -> Importar
-- No borra datos existentes.
-- ============================================================

CREATE TABLE IF NOT EXISTS entrevistas_docente (
  id                          INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id                 INT NOT NULL,

  -- Datos del entrevistador
  nombre_entrevistador        VARCHAR(150) NULL,
  cargo_entrevistador         VARCHAR(150) NULL,
  programa_academico          VARCHAR(150) NULL,
  fecha_entrevista            DATE NULL,
  modalidad                   ENUM('presencial','virtual') NULL,

  -- Datos del entrevistado
  nombre_entrevistado         VARCHAR(150) NULL,
  profesion                   VARCHAR(150) NULL,
  posgrados_estado            ENUM('en_curso','culminado') NULL,
  posgrados_detalle           VARCHAR(255) NULL,
  doc_tipo                    ENUM('CC','CE') NULL,
  doc_numero                  VARCHAR(30) NULL,
  doc_lugar_expedicion        VARCHAR(100) NULL,
  tipo_vinculacion_docente    ENUM('tiempo_completo','medio_tiempo','hora_catedra') NULL,

  -- Competencias (secciones numeradas del formato)
  competencias_personales     TEXT NULL,
  competencias_academicas     TEXT NULL,
  competencias_profesionales  TEXT NULL,
  concepto_general            TEXT NULL,

  -- Cierre de la entrevista
  disponibilidad_tiempo       ENUM('si','no') NULL,
  acepta_condiciones          ENUM('si','no') NULL,
  conclusiones                TEXT NULL,
  firma_entrevistador         VARCHAR(150) NULL,

  created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uq_empleado_entrevista_docente (empleado_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB;
