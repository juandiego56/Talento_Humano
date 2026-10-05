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

        // Genera el token del código QR de auto-registro de asistencia la primera vez que se ve la actividad
        if (empty($actividad['qr_token'])) {
            $actividad['qr_token'] = bin2hex(random_bytes(16));
            DB::execute("UPDATE actividades_bienestar SET qr_token = ? WHERE id = ?", [$actividad['qr_token'], $id]);
        }
        $checkinUrl = APP_URL . '/bienestar/checkin/' . $actividad['qr_token'];

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
            'checkinUrl'  => $checkinUrl,
        ]);
    }

    /** Regenera el token del código QR (invalida el anterior) */
    public function regenerarQr(string $id): void {
        Auth::requireGestion();
        $this->obtenerActividad($id);
        DB::execute("UPDATE actividades_bienestar SET qr_token = ? WHERE id = ?", [bin2hex(random_bytes(16)), $id]);
        Session::flash('success', 'Código QR regenerado. El código anterior ya no funcionará.');
        header('Location: ' . APP_URL . '/bienestar/' . $id);
        exit;
    }

    /**
     * Punto de llegada al escanear el código QR de una actividad: muestra un formulario
     * público (sin necesidad de iniciar sesión) donde el empleado diligencia su número
     * de documento para registrar su propia asistencia.
     */
    public function checkin(string $token): void {
        $actividad = DB::fetch("SELECT * FROM actividades_bienestar WHERE qr_token = ?", [$token]);

        View::render('bienestar.checkin', [
            'titulo'    => 'Registro de asistencia',
            'paso'      => 'formulario',
            'actividad' => $actividad,
            'token'     => $token,
            'error'     => null,
        ], layout: '');
    }

    /**
     * Procesa el número de documento diligenciado en el formulario del QR: busca el
     * empleado vinculado a ese documento y, si existe, registra su asistencia.
     */
    public function checkinRegistrar(string $token): void {
        $actividad = DB::fetch("SELECT * FROM actividades_bienestar WHERE qr_token = ?", [$token]);
        $numeroDocumento = trim($_POST['numero_documento'] ?? '');

        if (!$actividad) {
            View::render('bienestar.checkin', [
                'titulo'    => 'Registro de asistencia',
                'paso'      => 'resultado',
                'ok'        => false,
                'mensaje'   => 'Este código QR no es válido o la actividad ya no existe.',
                'actividad' => $actividad,
            ], layout: '');
            return;
        }

        if ($numeroDocumento === '') {
            View::render('bienestar.checkin', [
                'titulo'    => 'Registro de asistencia',
                'paso'      => 'formulario',
                'actividad' => $actividad,
                'token'     => $token,
                'error'     => 'Ingresa tu número de documento.',
            ], layout: '');
            return;
        }

        $emp = DB::fetch("SELECT id, nombres, apellidos FROM empleados WHERE numero_documento = ?", [$numeroDocumento]);

        if (!$emp) {
            View::render('bienestar.checkin', [
                'titulo'    => 'Registro de asistencia',
                'paso'      => 'formulario',
                'actividad' => $actividad,
                'token'     => $token,
                'error'     => 'No encontramos ningún empleado con ese número de documento. Verifica el número o contacta a Talento Humano.',
            ], layout: '');
            return;
        }

        DB::execute("
            INSERT INTO bienestar_inscripciones (actividad_id, empleado_id, asistio)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE asistio = 1
        ", [$actividad['id'], $emp['id']]);

        View::render('bienestar.checkin', [
            'titulo'         => 'Registro de asistencia',
            'paso'           => 'resultado',
            'ok'             => true,
            'mensaje'        => '',
            'actividad'      => $actividad,
            'empleadoNombre' => $emp['nombres'] . ' ' . $emp['apellidos'],
        ], layout: '');
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
