<?php
require_once ROOT . '/app/helpers/Schema.php';

class DashboardController {
    public function index(): void {
        Auth::requireAuth();

        if (Auth::puedeGestionar()) {
            $this->dashboardGestion();
        } elseif (Auth::esDirectorPrograma()) {
            $this->dashboardDirector();
        } else {
            $this->dashboardEmpleado();
        }
    }

    /** Admin / Gestor de Talento Humano: visión general + lo que espera su atención. */
    private function dashboardGestion(): void {
        // ── Pendientes de tu atención (lo nuevo: primero lo que hay que resolver) ──
        $solicitudesPendientes = (int)(DB::fetch("
            SELECT COUNT(*) AS t FROM solicitudes_vinculacion WHERE estado = 'pendiente'
        ")['t'] ?? 0);

        $verificacionPendiente = (int)(DB::fetch("
            SELECT COUNT(*) AS t FROM empleado_documentos
            WHERE archivo_path IS NOT NULL AND estado_verificacion = 'pendiente'
        ")['t'] ?? 0);

        $nominasBorrador = (int)(DB::fetch("
            SELECT COUNT(*) AS t FROM nominas WHERE estado = 'borrador'
        ")['t'] ?? 0);

        // ── KPIs de Personal ────────────────────────────────────────────
        $empStats = DB::fetch("
            SELECT
              COUNT(*) AS total,
              SUM(estado='activo')   AS activos,
              SUM(estado='inactivo') AS inactivos,
              SUM(estado='retirado') AS retirados
            FROM empleados
        ") ?? ['total'=>0,'activos'=>0,'inactivos'=>0,'retirados'=>0];

        $checklistProm = (float)(DB::fetch("
            SELECT COALESCE(ROUND(AVG(pct_completitud),1),0) AS p
            FROM v_checklist_completitud
        ")['p'] ?? 0);

        // ── KPIs de Nómina ──────────────────────────────────────────────
        $ultimaNomina = DB::fetch("SELECT * FROM nominas ORDER BY periodo DESC LIMIT 1");

        // ── KPIs de Bienestar ───────────────────────────────────────────
        $bienestarStats = DB::fetch("
            SELECT COUNT(*) AS total,
                   SUM(estado='programada') AS programadas,
                   SUM(estado='finalizada') AS finalizadas
            FROM actividades_bienestar
        ") ?? ['total'=>0,'programadas'=>0,'finalizadas'=>0];

        // ── Listados recientes ──────────────────────────────────────────
        $empleadosRecientes = DB::fetchAll("
            SELECT e.id, e.nombres, e.apellidos, e.fecha_ingreso, e.estado,
                   c.nombre AS cargo, a.nombre AS area
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            ORDER BY e.fecha_ingreso DESC LIMIT 5
        ");

        $proximasActividades = $this->proximasActividades();

        $porArea = DB::fetchAll("
            SELECT a.nombre AS area, COUNT(e.id) AS total
            FROM areas a LEFT JOIN empleados e ON e.area_id = a.id AND e.estado = 'activo'
            GROUP BY a.id, a.nombre
            HAVING COUNT(e.id) > 0
            ORDER BY total DESC
        ");

        // ── Datos para las gráficas del tablero ─────────────────────────
        // Últimas 6 nóminas (sin anuladas), de la más antigua a la más reciente
        $nominaSerie = array_reverse(DB::fetchAll("
            SELECT periodo, total_neto FROM nominas
            WHERE estado <> 'anulada'
            ORDER BY periodo DESC LIMIT 6
        "));

        $bienestarPorEstado = DB::fetchAll("
            SELECT estado, COUNT(*) AS total FROM actividades_bienestar GROUP BY estado
        ");

        View::render('dashboard.gestion', [
            'titulo'                 => 'Tablero de Talento Humano',
            'nominaSerie'            => $nominaSerie,
            'bienestarPorEstado'     => $bienestarPorEstado,
            'solicitudesPendientes'  => $solicitudesPendientes,
            'verificacionPendiente'  => $verificacionPendiente,
            'nominasBorrador'        => $nominasBorrador,
            'empStats'               => $empStats,
            'checklistProm'          => $checklistProm,
            'ultimaNomina'           => $ultimaNomina,
            'bienestarStats'         => $bienestarStats,
            'empleadosRecientes'     => $empleadosRecientes,
            'proximasActividades'    => $proximasActividades,
            'porArea'                => $porArea,
        ]);
    }

    /** Director de Programa: sus solicitudes de vinculación y su programa. */
    private function dashboardDirector(): void {
        $programaId = Auth::programaId();

        $programa = $programaId
            ? DB::fetch("SELECT * FROM programas_academicos WHERE id = ?", [$programaId])
            : null;

        $misSolicitudes = $programaId ? DB::fetchAll("
            SELECT * FROM solicitudes_vinculacion
            WHERE programa_id = ?
            ORDER BY FIELD(estado,'pendiente','aprobada','rechazada'), fecha_solicitud DESC
            LIMIT 5
        ", [$programaId]) : [];

        $conteoSolicitudes = $programaId ? DB::fetch("
            SELECT SUM(estado='pendiente') AS pendientes,
                   SUM(estado='aprobada')  AS aprobadas,
                   SUM(estado='rechazada') AS rechazadas
            FROM solicitudes_vinculacion WHERE programa_id = ?
        ", [$programaId]) : ['pendientes'=>0,'aprobadas'=>0,'rechazadas'=>0];

        $empleadosPrograma = $programaId ? (int)(DB::fetch("
            SELECT COUNT(*) AS t FROM empleados WHERE programa_id = ? AND estado = 'activo'
        ", [$programaId])['t'] ?? 0) : 0;

        $proximasActividades = $this->proximasActividades();

        View::render('dashboard.director', [
            'titulo'              => 'Tablero de Talento Humano',
            'programa'            => $programa,
            'misSolicitudes'      => $misSolicitudes,
            'conteoSolicitudes'   => $conteoSolicitudes,
            'empleadosPrograma'   => $empleadosPrograma,
            'proximasActividades' => $proximasActividades,
        ]);
    }

    /** Empleado: su propio avance y lo próximo en bienestar. */
    private function dashboardEmpleado(): void {
        Schema::asegurarHojaVida();
        $empleadoId = Auth::empleadoId();

        $empleado = $empleadoId ? DB::fetch("
            SELECT e.*, c.nombre AS cargo, a.nombre AS area
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            WHERE e.id = ?
        ", [$empleadoId]) : null;

        // Mientras la hoja de vida no esté enviada (o la hayan devuelto), el empleado llega directo a diligenciarla.
        if ($empleado && in_array($empleado['hojavida_estado'] ?? 'borrador', ['borrador', 'devuelta'], true) && empty($_GET['sin_redireccion'])) {
            header('Location: ' . APP_URL . '/mi-hoja-de-vida');
            exit;
        }

        $checklist = $empleadoId ? DB::fetch("
            SELECT * FROM v_checklist_completitud WHERE empleado_id = ?
        ", [$empleadoId]) : null;

        $proximasActividades = $this->proximasActividades();

        View::render('dashboard.empleado', [
            'titulo'              => 'Tablero de Talento Humano',
            'empleado'            => $empleado,
            'checklist'           => $checklist,
            'proximasActividades' => $proximasActividades,
        ]);
    }

    private function proximasActividades(): array {
        return DB::fetchAll("
            SELECT * FROM actividades_bienestar
            WHERE estado IN ('programada','en_curso') AND fecha_inicio >= CURDATE()
            ORDER BY fecha_inicio ASC LIMIT 5
        ");
    }
}
