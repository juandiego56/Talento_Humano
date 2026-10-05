-- ============================================================
-- Migración: Corte académico (etiqueta que agrupa periodos mensuales de nómina, ej. 2025-2)
-- Fecha: 2026 · Ejecutar una sola vez sobre sgth_talento_humano
-- ============================================================

ALTER TABLE nominas
  ADD COLUMN IF NOT EXISTS corte_academico VARCHAR(10) NULL COMMENT 'Etiqueta de corte académico que agrupa periodos mensuales, ej. 2025-2' AFTER periodo;

ALTER TABLE nominas
  ADD INDEX IF NOT EXISTS idx_nominas_corte (corte_academico);
