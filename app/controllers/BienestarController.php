<?php
class BienestarController {

    public function index(): void {
        Auth::requireAuth();
        $tipo   = $_GET['tipo'] ?? '';
        $estado = $_GET['estado'] ?? '';

        $where = []; $params = [];
        if ($tipo !== '')   { $where[] = 'tipo = ?';   $params[] = $tipo; }
        if ($estado !== '') { $where[] = 'estado = ?'; $params[] = $estado; }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $actividades = DB::fetchAll("
            SELECT ab.*, COUNT(bi.id) AS inscritos
            FROM actividades_bienestar ab
            LEFT JOIN bienestar_inscripciones bi ON bi.actividad_id = ab.id
            $whereSql
            GROUP BY ab.id
            ORDER BY ab.fecha_inicio DESC
        ", $params);

        View::render('bienestar.index', [
            'titulo'      => 'Bienestar Laboral',
            'actividades' => $actividades,
            'tipo'        => $tipo,
            'estado'      => $estado,
        ]);
    }

    public function crear(): void {
        Auth::requireGestion();
        View::render('bienestar.crear', ['titulo' => 'Nueva Actividad de Bienestar']);
    }

    public function guardar(): void {
        Auth::requireGestion();
        $id = DB::insert("
            INSERT INTO actividades_bienestar
              (nombre, descripcion, tipo, fecha_inicio, fecha_fin, hora_inicio, lugar, responsable, cupo_maximo, estado)
            VALUES (?,?,?,?,?,?,?,?,?, 'programada')
        ", [
            $_POST['nombre'], ($_POST['descripcion'] ?? '') ?: null, $_POST['tipo'],
            $_POST['fecha_inicio'], ($_POST['fecha_fin'] ?? '') ?: null, ($_POST['hora_inicio'] ?? '') ?: null,
            ($_POST['lugar'] ?? '') ?: null, ($_POST['responsable'] ?? '') ?: null,
            ($_POST['cupo_maximo'] ?? '') !== '' ? (int)$_POST['cupo_maximo'] : null,
        ]);
        Session::flash('success', 'Actividad de bienestar creada.');
        header('Location: ' . APP_URL . '/bienestar/' . $id);
        exit;
    }

    public function ver(string $id): void {
        Auth::requireAuth();
        $actividad = $this->obtenerActividad($id);
        $inscritos = DB::fetchAll("
            SELECT bi.*, e.nombres, e.apellidos, e.numero_documento, c.nombre AS cargo
            FROM bienestar_inscripciones bi
            JOIN empleados e ON e.id = bi.empleado_id
            LEFT JOIN cargos c ON c.id = e.cargo_id
            WHERE bi.actividad_id = ? ORDER BY e.apellidos, e.nombres
        ", [$id]);
        $disponibles = DB::fetchAll("
            SELECT e.* FROM empleados e
            WHERE e.estado = 'activo' AND e.id NOT IN (
                SELECT empleado_id FROM bienestar_inscripciones WHERE actividad_id = ?
            ) ORDER BY e.apellidos, e.nombres
        ", [$id]);

        View::render('bienestar.ver', [
            'titulo'      => $actividad['nombre'],
            'actividad'   => $actividad,
            'inscritos'   => $inscritos,
            'disponibles' => $disponibles,
        ]);
    }

    public function editar(string $id): void {
        Auth::requireGestion();
        $actividad = $this->obtenerActividad($id);
        View::render('bienestar.editar', ['titulo' => 'Editar Actividad', 'actividad' => $actividad]);
    }

    public function actualizar(string $id): void {
        Auth::requireGestion();
        DB::execute("
            UPDATE actividades_bienestar SET
              nombre=?, descripcion=?, tipo=?, fecha_inicio=?, fecha_fin=?, hora_inicio=?,
              lugar=?, responsable=?, cupo_maximo=?, estado=?
            WHERE id=?
        ", [
            $_POST['nombre'], ($_POST['descripcion'] ?? '') ?: null, $_POST['tipo'],
            $_POST['fecha_inicio'], ($_POST['fecha_fin'] ?? '') ?: null, ($_POST['hora_inicio'] ?? '') ?: null,
            ($_POST['lugar'] ?? '') ?: null, ($_POST['responsable'] ?? '') ?: null,
            ($_POST['cupo_maximo'] ?? '') !== '' ? (int)$_POST['cupo_maximo'] : null,
            $_POST['estado'], $id,
        ]);
        Session::flash('success', 'Actividad actualizada.');
        header('Location: ' . APP_URL . '/bienestar/' . $id);
        exit;
    }

    public function eliminar(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM actividades_bienestar WHERE id = ?", [$id]);
        Session::flash('success', 'Actividad eliminada.');
        header('Location: ' . APP_URL . '/bienestar');
        exit;
    }

    public function inscribir(string $id): void {
        Auth::requireGestion();
        $actividad  = $this->obtenerActividad($id);
        $empleadoId = $_POST['empleado_id'];

        if ($actividad['cupo_maximo']) {
            $actuales = (int)(DB::fetch("SELECT COUNT(*) t FROM bienestar_inscripciones WHERE actividad_id = ?", [$id])['t'] ?? 0);
            if ($actuales >= $actividad['cupo_maximo']) {
                Session::flash('error', 'Se alcanzó el cupo máximo para esta actividad.');
                header('Location: ' . APP_URL . '/bienestar/' . $id);
                exit;
            }
        }

        try {
            DB::insert("INSERT INTO bienestar_inscripciones (actividad_id, empleado_id) VALUES (?,?)", [$id, $empleadoId]);
            Session::flash('success', 'Empleado inscrito correctamente.');
        } catch (\PDOException $e) {
            Session::flash('error', 'El empleado ya está inscrito en esta actividad.');
        }
        header('Location: ' . APP_URL . '/bienestar/' . $id);
        exit;
    }

    public function marcarAsistio(string $id, string $inscripcionId): void {
        Auth::requireGestion();
        $actual = DB::fetch("SELECT asistio FROM bienestar_inscripciones WHERE id = ? AND actividad_id = ?", [$inscripcionId, $id]);
        $nuevo = $actual && $actual['asistio'] ? 0 : 1;
        DB::execute("UPDATE bienestar_inscripciones SET asistio = ? WHERE id = ?", [$nuevo, $inscripcionId]);
        header('Location: ' . APP_URL . '/bienestar/' . $id);
        exit;
    }

    public function eliminarInscripcion(string $id, string $inscripcionId): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM bienestar_inscripciones WHERE id = ? AND actividad_id = ?", [$inscripcionId, $id]);
        header('Location: ' . APP_URL . '/bienestar/' . $id);
        exit;
    }

    private function obtenerActividad(string $id): array {
        $actividad = DB::fetch("SELECT * FROM actividades_bienestar WHERE id = ?", [$id]);
        if (!$actividad) { http_response_code(404); exit('Actividad no encontrada.'); }
        return $actividad;
    }
}
