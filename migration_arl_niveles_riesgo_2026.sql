-- ============================================================
-- Migración: ARL configurable por nivel de riesgo (I-V)
-- Fecha: 2026 · Ejecutar una sola vez sobre sgth_talento_humano
-- ============================================================

CREATE TABLE IF NOT EXISTS arl_niveles_riesgo (
  nivel        TINYINT PRIMARY KEY,
  descripcion  VARCHAR(150) NOT NULL,
  tasa         DECIMAL(6,5) NOT NULL COMMENT 'Tasa como fracción, ej. 0.00522 = 0.522%',
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO arl_niveles_riesgo (nivel, descripcion, tasa) VALUES
  (1, 'Riesgo I — Mínimo (labores administrativas, docencia)', 0.00522),
  (2, 'Riesgo II — Bajo', 0.01044),
  (3, 'Riesgo III — Medio', 0.02436),
  (4, 'Riesgo IV — Alto', 0.04350),
  (5, 'Riesgo V — Máximo', 0.06960);

ALTER TABLE empleados
  ADD COLUMN IF NOT EXISTS arl_nivel_riesgo TINYINT NOT NULL DEFAULT 1;
