<?php
/**
 * Asegura que la base de datos tenga las columnas de la hoja de vida del empleado
 * (estado, versión, fecha, observaciones). Se ejecuta sola, una vez, la primera vez
 * que se necesita: no hay que correr ningún script a mano.
 */
class Schema {
    private static bool $hecho = false;

    private static bool $claveHecha = false;

    /** Columna usuarios.debe_cambiar_password: obliga a cambiar la clave inicial en el primer ingreso. */
    public static function asegurarCambioClave(): void {
        if (self::$claveHecha) return;
        self::$claveHecha = true;
        try {
            $cols = array_column(DB::fetchAll("SHOW COLUMNS FROM usuarios"), 'Field');
            if (!in_array('debe_cambiar_password', $cols, true)) {
                DB::execute("ALTER TABLE usuarios ADD COLUMN debe_cambiar_password TINYINT(1) NOT NULL DEFAULT 0");
            }
        } catch (\Throwable $e) {
            error_log('Schema::asegurarCambioClave: ' . $e->getMessage());
        }
    }

    public static function asegurarHojaVida(): void {
        self::asegurarCambioClave();
        if (self::$hecho) return;
        self::$hecho = true;
        try {
            DB::execute("
                CREATE TABLE IF NOT EXISTS empleado_formacion_complementaria (
                  id           INT AUTO_INCREMENT PRIMARY KEY,
                  empleado_id  INT NOT NULL,
                  nombre       VARCHAR(150) NOT NULL,
                  institucion  VARCHAR(150) NOT NULL,
                  fecha        DATE NOT NULL,
                  horas        INT NULL,
                  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
                ) ENGINE=InnoDB
            ");
            $cols = array_column(DB::fetchAll("SHOW COLUMNS FROM empleados"), 'Field');
            if (in_array('hojavida_estado', $cols, true)) {
                if (!in_array('hojavida_respaldo', $cols, true)) {
                    DB::execute("ALTER TABLE empleados ADD COLUMN hojavida_respaldo LONGTEXT NULL");
                }
                return;
            }

            DB::execute("
                ALTER TABLE empleados
                  ADD COLUMN hojavida_estado ENUM('borrador','enviada','aprobada','devuelta') NOT NULL DEFAULT 'borrador',
                  ADD COLUMN hojavida_version INT NOT NULL DEFAULT 1,
                  ADD COLUMN hojavida_fecha DATE NULL,
                  ADD COLUMN hojavida_observaciones TEXT NULL,
                  ADD COLUMN hojavida_enviada_en DATETIME NULL,
                  ADD COLUMN hojavida_revisada_por VARCHAR(150) NULL,
                  ADD COLUMN hojavida_revisada_en DATETIME NULL,
                  ADD COLUMN hojavida_respaldo LONGTEXT NULL
            ");
            // Los empleados que ya existían se consideran con su hoja de vida aprobada (versión 1).
            DB::execute("UPDATE empleados SET hojavida_estado = 'aprobada', hojavida_fecha = CURDATE()");
        } catch (\Throwable $e) {
            error_log('Schema::asegurarHojaVida: ' . $e->getMessage());
        }
    }
}
