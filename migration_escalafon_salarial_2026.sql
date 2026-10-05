-- ============================================================
-- Migración: Escalafón de cargos (salario por nivel de formación
-- y dedicación tiempo completo / medio tiempo)
-- Fecha: 2026 · Ejecutar una sola vez sobre sgth_talento_humano
-- ============================================================

CREATE TABLE IF NOT EXISTS escalafon_salarial (
  id                       INT AUTO_INCREMENT PRIMARY KEY,
  cargo_id                 INT NOT NULL,
  nivel_educativo          ENUM('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') NOT NULL,
  salario_tiempo_completo  DECIMAL(12,2) NOT NULL,
  salario_medio_tiempo     DECIMAL(12,2) NULL,
  created_at               TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_escalafon_cargo_nivel (cargo_id, nivel_educativo),
  FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE empleados
  ADD COLUMN nivel_educativo ENUM('bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado') NULL,
  ADD COLUMN tipo_vinculacion_docente ENUM('tiempo_completo','medio_tiempo','hora_catedra') NULL;
