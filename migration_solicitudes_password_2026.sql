-- Migración: tabla de solicitudes de restablecimiento de contraseña
-- Flujo: el usuario solicita desde /auth/recuperar; un Administrador la atiende desde /usuarios

CREATE TABLE solicitudes_password (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id       INT NOT NULL,
  estado           ENUM('pendiente','atendida') NOT NULL DEFAULT 'pendiente',
  fecha_solicitud  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atendida_por     INT NULL,
  fecha_atendida   TIMESTAMP NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (atendida_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;
