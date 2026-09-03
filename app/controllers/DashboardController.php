<?php
class DashboardController {
    public function index(): void {
        Auth::requireAuth();

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
        $nominaStats  = DB::fetch("
            SELECT COUNT(*) AS total,
                   SUM(estado='borrador') AS borrador,
                   SUM(estado='pagada')   AS pagada
            FROM nominas
        ") ?? ['total'=>0,'borrador'=>0,'pagada'=>0];

        // ── KPIs de Bienestar ───────────────────────────────────────────
        $bienestarStats = DB::fetch("
            SELECT COUNT(*) AS total,
                   SUM(estado='programada') AS programadas,
                   SUM(estado='finalizada') AS finalizadas
            FROM actividades_bienestar
        ") ?? ['total'=>0,'programadas'=>0,'finalizadas'=>0];

        $totalInscripciones = (int)(DB::fetch("SELECT COUNT(*) AS t FROM bienestar_inscripciones")['t'] ?? 0);

        // ── Listados recientes ──────────────────────────────────────────
        $empleadosRecientes = DB::fetchAll("
            SELECT e.id, e.nombres, e.apellidos, e.fecha_ingreso, e.estado,
                   c.nombre AS cargo, a.nombre AS area
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            ORDER BY e.fecha_ingreso DESC LIMIT 5
        ");

        $proximasActividades = DB::fetchAll("
            SELECT * FROM actividades_bienestar
            WHERE estado IN ('programada','en_curso') AND fecha_inicio >= CURDATE()
            ORDER BY fecha_inicio ASC LIMIT 5
        ");

        $porArea = DB::fetchAll("
            SELECT a.nombre AS area, COUNT(e.id) AS total
            FROM areas a LEFT JOIN empleados e ON e.area_id = a.id AND e.estado = 'activo'
            GROUP BY a.id, a.nombre
            HAVING COUNT(e.id) > 0
            ORDER BY total DESC
        ");

        View::render('dashboard.index', [
            'titulo'              => 'Tablero de Talento Humano',
            'empStats'            => $empStats,
            'checklistProm'       => $checklistProm,
            'ultimaNomina'        => $ultimaNomina,
            'nominaStats'         => $nominaStats,
            'bienestarStats'      => $bienestarStats,
            'totalInscripciones'  => $totalInscripciones,
            'empleadosRecientes'  => $empleadosRecientes,
            'proximasActividades' => $proximasActividades,
            'porArea'             => $porArea,
        ]);
    }
}
