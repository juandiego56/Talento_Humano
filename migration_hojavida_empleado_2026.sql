-- Hoja de vida diligenciada por el empleado y validada por Talento Humano.
-- OJO: la aplicación ya aplica esto sola (app/helpers/Schema.php); este archivo es solo referencia.
ALTER TABLE empleados
  ADD COLUMN hojavida_estado ENUM('borrador','enviada','aprobada','devuelta') NOT NULL DEFAULT 'borrador',
  ADD COLUMN hojavida_version INT NOT NULL DEFAULT 1,
  ADD COLUMN hojavida_fecha DATE NULL,
  ADD COLUMN hojavida_observaciones TEXT NULL,
  ADD COLUMN hojavida_enviada_en DATETIME NULL,
  ADD COLUMN hojavida_revisada_por VARCHAR(150) NULL,
  ADD COLUMN hojavida_revisada_en DATETIME NULL;
UPDATE empleados SET hojavida_estado = 'aprobada', hojavida_fecha = CURDATE();
