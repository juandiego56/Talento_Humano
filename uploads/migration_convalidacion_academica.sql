-- ============================================================
-- MIGRACIÓN: Convalidación de formación académica
-- Agrega un estado (convalidado / no convalidado) a nivel de
-- empleado, visible en la sección "Formación académica".
-- Ejecutar en phpMyAdmin -> base sgth_talento_humano -> pestaña SQL
-- No borra datos existentes.
-- ============================================================

ALTER TABLE empleados
  ADD COLUMN formacion_convalidada TINYINT(1) NOT NULL DEFAULT 0 AFTER foto_path;