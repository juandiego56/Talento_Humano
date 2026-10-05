-- ============================================================
-- Migración: Asistencia mediante código QR para actividades de bienestar
-- Fecha: 2026 · Ejecutar una sola vez sobre sgth_talento_humano
-- ============================================================

ALTER TABLE actividades_bienestar
  ADD COLUMN IF NOT EXISTS qr_token VARCHAR(64) NULL COMMENT 'Token único para el enlace/QR de auto-registro de asistencia';

ALTER TABLE actividades_bienestar
  ADD UNIQUE INDEX IF NOT EXISTS uq_actividades_qr_token (qr_token);
