<?php
class CatalogoController {
    public function index(): void {
        Auth::requireGestion();
        $areas  = DB::fetchAll("
            SELECT a.*, COUNT(e.id) AS num_empleados
            FROM areas a LEFT JOIN empleados e ON e.area_id = a.id
            GROUP BY a.id ORDER BY a.nombre
        ");
        $cargos = DB::fetchAll("
            SELECT c.*, a.nombre AS area, COUNT(e.id) AS num_empleados
            FROM cargos c LEFT JOIN areas a ON a.id = c.area_id
            LEFT JOIN empleados e ON e.cargo_id = c.id
            GROUP BY c.id ORDER BY c.nombre
        ");
        $programas = DB::fetchAll("
            SELECT p.*, COUNT(e.id) AS num_empleados
            FROM programas_academicos p LEFT JOIN empleados e ON e.programa_id = p.id
            GROUP BY p.id ORDER BY p.nombre
        ");
        $escalafon = DB::fetchAll("
            SELECT es.*, c.nombre AS cargo
            FROM escalafon_salarial es JOIN cargos c ON c.id = es.cargo_id
            ORDER BY c.nombre, FIELD(es.nivel_educativo,'bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado')
        ");
        $nivelesArl = DB::fetchAll("SELECT * FROM arl_niveles_riesgo ORDER BY nivel");
        View::render('catalogos.index', [
            'titulo'     => 'Catálogos — Áreas, Cargos y Programas',
            'areas'      => $areas,
            'cargos'     => $cargos,
            'programas'  => $programas,
            'escalafon'  => $escalafon,
            'nivelesArl' => $nivelesArl,
        ]);
    }

    public function guardarArea(): void {
        Auth::requireGestion();
        DB::insert("INSERT INTO areas (nombre, descripcion) VALUES (?,?)", [$_POST['nombre'], ($_POST['descripcion'] ?? '') ?: null]);
        Session::flash('success', 'Área creada.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function eliminarArea(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM areas WHERE id = ?", [$id]);
        Session::flash('success', 'Área eliminada.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function guardarCargo(): void {
        Auth::requireGestion();
        DB::insert("
            INSERT INTO cargos (nombre, area_id, salario_base_sugerido) VALUES (?,?,?)
        ", [$_POST['nombre'], ($_POST['area_id'] ?? '') ?: null, $_POST['salario_base_sugerido'] !== '' ? (float)$_POST['salario_base_sugerido'] : null]);
        Session::flash('success', 'Cargo creado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function eliminarCargo(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM cargos WHERE id = ?", [$id]);
        Session::flash('success', 'Cargo eliminado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function guardarPrograma(): void {
        Auth::requireGestion();
        try {
            DB::insert("
                INSERT INTO programas_academicos (nombre, codigo, facultad, activo) VALUES (?,?,?,1)
            ", [
                $_POST['nombre'], ($_POST['codigo'] ?? '') ?: null, ($_POST['facultad'] ?? '') ?: null,
            ]);
            Session::flash('success', 'Programa académico creado.');
        } catch (\PDOException $e) {
            Session::flash('error', 'Error al crear el programa académico.');
        }
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function eliminarPrograma(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM programas_academicos WHERE id = ?", [$id]);
        Session::flash('success', 'Programa académico eliminado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    /** Crea o actualiza el salario del escalafón para un cargo + nivel de formación (upsert). */
    public function guardarEscalafon(): void {
        Auth::requireGestion();
        $cargoId = (int)($_POST['cargo_id'] ?? 0);
        $nivel   = $_POST['nivel_educativo'] ?? '';
        $tc      = (float)($_POST['salario_tiempo_completo'] ?? 0);
        $mt      = ($_POST['salario_medio_tiempo'] ?? '') !== '' ? (float)$_POST['salario_medio_tiempo'] : null;

        if (!$cargoId || !$nivel || $tc <= 0) {
            Session::flash('error', 'Selecciona el cargo, el nivel de formación e ingresa el salario de tiempo completo.');
            header('Location: ' . APP_URL . '/catalogos');
            exit;
        }

        DB::execute("
            INSERT INTO escalafon_salarial (cargo_id, nivel_educativo, salario_tiempo_completo, salario_medio_tiempo)
            VALUES (?,?,?,?)
            ON DUPLICATE KEY UPDATE
              salario_tiempo_completo = VALUES(salario_tiempo_completo),
              salario_medio_tiempo    = VALUES(salario_medio_tiempo)
        ", [$cargoId, $nivel, $tc, $mt]);

        Session::flash('success', 'Escalafón guardado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function eliminarEscalafon(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM escalafon_salarial WHERE id = ?", [$id]);
        Session::flash('success', 'Registro del escalafón eliminado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    /** Actualiza la tasa (%) de un nivel de riesgo ARL fijo (I a V). */
    public function actualizarNivelArl(string $nivel): void {
        Auth::requireGestion();
        $nivel = (int)$nivel;
        $tasaPct = (float)str_replace(',', '.', $_POST['tasa_pct'] ?? '0');

        if ($nivel < 1 || $nivel > 5 || $tasaPct <= 0 || $tasaPct > 100) {
            Session::flash('error', 'Tasa inválida para el nivel de riesgo ARL.');
            header('Location: ' . APP_URL . '/catalogos');
            exit;
        }

        DB::execute("UPDATE arl_niveles_riesgo SET tasa = ? WHERE nivel = ?", [$tasaPct / 100, $nivel]);
        Session::flash('success', 'Tasa de ARL actualizada.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }
}
