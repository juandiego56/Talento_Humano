-- ============================================================
-- Migración: Solicitud de vinculación laboral
-- (Director de Programa → Talento Humano)
-- Fecha: 2026 · Ejecutar una sola vez sobre sgth_talento_humano
-- ============================================================

CREATE TABLE IF NOT EXISTS solicitudes_vinculacion (
  id                          INT AUTO_INCREMENT PRIMARY KEY,
  programa_id                 INT NOT NULL,
  solicitado_por              INT NOT NULL,
  nombres_candidato           VARCHAR(100) NOT NULL,
  apellidos_candidato         VARCHAR(100) NOT NULL,
  cargo_sugerido              VARCHAR(150) NULL,
  nivel_educativo_requerido   ENUM('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') NULL,
  tipo_vinculacion_docente    ENUM('tiempo_completo','medio_tiempo','hora_catedra') NULL,
  justificacion               TEXT NOT NULL,
  fecha_requerida             DATE NULL,
  estado                      ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  respuesta                   TEXT NULL,
  atendida_por                INT NULL,
  fecha_solicitud             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_respuesta             TIMESTAMP NULL,
  CONSTRAINT fk_solvinc_programa FOREIGN KEY (programa_id) REFERENCES programas_academicos(id) ON DELETE CASCADE,
  CONSTRAINT fk_solvinc_solicitante FOREIGN KEY (solicitado_por) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_solvinc_atendida FOREIGN KEY (atendida_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

ALTER TABLE solicitudes_vinculacion
  ADD INDEX IF NOT EXISTS idx_solvinc_programa (programa_id),
  ADD INDEX IF NOT EXISTS idx_solvinc_estado (estado);
