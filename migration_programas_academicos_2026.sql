-- ============================================================
-- Migración: Rol "Director de Programa" + tabla de programas académicos
-- Fecha: 2026 · Ejecutar una sola vez sobre sgth_talento_humano
-- ============================================================

CREATE TABLE IF NOT EXISTS programas_academicos (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(150) NOT NULL,
  codigo      VARCHAR(30)  NULL,
  facultad    VARCHAR(150) NULL,
  activo      TINYINT(1) NOT NULL DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE usuarios
  MODIFY COLUMN rol_id TINYINT NOT NULL COMMENT '1=Admin, 2=Gestor TH, 3=Empleado, 4=Director de Programa',
  ADD COLUMN programa_id INT NULL AFTER rol_id,
  ADD CONSTRAINT fk_usuarios_programa FOREIGN KEY (programa_id) REFERENCES programas_academicos(id) ON DELETE SET NULL;

ALTER TABLE empleados
  ADD COLUMN programa_id INT NULL AFTER area_id,
  ADD CONSTRAINT fk_empleados_programa FOREIGN KEY (programa_id) REFERENCES programas_academicos(id) ON DELETE SET NULL;
