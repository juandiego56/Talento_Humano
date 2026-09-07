-- ============================================================
-- MIGRACIÓN: Foto de perfil de la CUENTA (usuarios)
-- Permite que cualquier cuenta (admin, gestor o empleado) tenga
-- su propia foto, sin depender de tener una ficha de empleado vinculada.
-- Ejecutar en phpMyAdmin -> base sgth_talento_humano -> Importar
-- No borra datos existentes.
-- ============================================================

ALTER TABLE usuarios
  ADD COLUMN foto_path VARCHAR(255) NULL AFTER activo;
