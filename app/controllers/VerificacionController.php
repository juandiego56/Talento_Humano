<?php
/**
 * Verificación de soportes documentales (checklist FO-TH-027) subidos por/para los
 * empleados. Vista de dos paneles: lista filtrable a la izquierda, detalle + acciones
 * (Aprobar / Devolver con motivo / Marcar no legible) a la derecha.
 */
class VerificacionController {

    private const ESTADOS_VALIDOS = ['pendiente', 'aprobado', 'devuelto', 'todos'];

    public function index(): void {
        $this->render(null, null);
    }

    public function ver(string $empleadoId, string $documentoId): void {
        $this->render($empleadoId, $documentoId);
    }

    private function render(?string $empleadoId, ?string $documentoId): void {
        Auth::requireGestion();

        $estado = $_GET['estado'] ?? 'pendiente';
        if (!in_array($estado, self::ESTADOS_VALIDOS, true)) $estado = 'pendiente';

        $where = ["ed.archivo_path IS NOT NULL"];
        $params = [];
        if ($estado !== 'todos') {
            $where[] = 'ed.estado_verificacion = ?';
            $params[] = $estado;
        }
        $whereSql = implode(' AND ', $where);

        $items = DB::fetchAll("
            SELECT ed.empleado_id, ed.documento_id, ed.archivo_nombre_original, ed.archivo_fecha_subida,
                   ed.peso_kb, ed.estado_verificacion, ed.motivo_devolucion, ed.verificado_por, ed.verificado_en,
                   dr.codigo, dr.nombre AS documento_nombre,
                   CONCAT(e.nombres, ' ', e.apellidos) AS empleado_nombre
            FROM empleado_documentos ed
            JOIN documentos_requeridos dr ON dr.id = ed.documento_id
            JOIN empleados e ON e.id = ed.empleado_id
            WHERE $whereSql
            ORDER BY ed.archivo_fecha_subida DESC
        ", $params);

        // Contadores para las pestañas de filtro (sobre el total, no sobre el filtro actual)
        $conteos = DB::fetch("
            SELECT
              SUM(estado_verificacion = 'pendiente') AS pendientes,
              SUM(estado_verificacion = 'aprobado')  AS aprobados,
              SUM(estado_verificacion = 'devuelto')  AS devueltos,
              COUNT(*) AS total
            FROM empleado_documentos WHERE archivo_path IS NOT NULL
        ") ?: ['pendientes' => 0, 'aprobados' => 0, 'devueltos' => 0, 'total' => 0];

        $seleccionado = null;
        if ($empleadoId !== null && $documentoId !== null) {
            foreach ($items as $it) {
                if ((string)$it['empleado_id'] === $empleadoId && (string)$it['documento_id'] === $documentoId) {
                    $seleccionado = $it;
                    break;
                }
            }
            // Si no está en la lista filtrada actual (p. ej. se acaba de aprobar y el filtro
            // sigue en "pendiente"), lo buscamos igual para poder mostrar el detalle.
            if (!$seleccionado) {
                $seleccionado = DB::fetch("
                    SELECT ed.empleado_id, ed.documento_id, ed.archivo_nombre_original, ed.archivo_fecha_subida,
                           ed.peso_kb, ed.estado_verificacion, ed.motivo_devolucion, ed.verificado_por, ed.verificado_en,
                           dr.codigo, dr.nombre AS documento_nombre,
                           CONCAT(e.nombres, ' ', e.apellidos) AS empleado_nombre
                    FROM empleado_documentos ed
                    JOIN documentos_requeridos dr ON dr.id = ed.documento_id
                    JOIN empleados e ON e.id = ed.empleado_id
                    WHERE ed.empleado_id = ? AND ed.documento_id = ? AND ed.archivo_path IS NOT NULL
                ", [$empleadoId, $documentoId]) ?: null;
            }
        }

        View::render('verificacion.index', [
            'titulo'       => 'Verificación de Documentos',
            'items'        => $items,
            'conteos'      => $conteos,
            'estadoFiltro' => $estado,
            'seleccionado' => $seleccionado,
        ]);
    }

    public function accion(string $empleadoId, string $documentoId): void {
        Auth::requireGestion();

        $accion = $_POST['accion'] ?? '';
        $quienVerifica = Auth::user()['nombre'] ?? 'Talento Humano';

        $reg = DB::fetch("
            SELECT archivo_path FROM empleado_documentos WHERE empleado_id = ? AND documento_id = ?
        ", [$empleadoId, $documentoId]);

        if (!$reg || !$reg['archivo_path']) {
            Session::flash('error', 'Ese documento no tiene un soporte cargado.');
            header('Location: ' . APP_URL . '/verificacion');
            exit;
        }

        switch ($accion) {
            case 'aprobar':
                DB::execute("
                    UPDATE empleado_documentos
                    SET estado_verificacion = 'aprobado', motivo_devolucion = NULL,
                        verificado_por = ?, verificado_en = NOW()
                    WHERE empleado_id = ? AND documento_id = ?
                ", [$quienVerifica, $empleadoId, $documentoId]);
                Session::flash('success', 'Documento aprobado.');
                break;

            case 'no_legible':
                DB::execute("
                    UPDATE empleado_documentos
                    SET estado_verificacion = 'devuelto',
                        motivo_devolucion = 'No legible: se requiere un nuevo escaneo del documento.',
                        verificado_por = ?, verificado_en = NOW()
                    WHERE empleado_id = ? AND documento_id = ?
                ", [$quienVerifica, $empleadoId, $documentoId]);
                Session::flash('success', 'Documento devuelto por no ser legible.');
                break;

            case 'devolver':
                $motivo = trim($_POST['motivo'] ?? '');
                if ($motivo === '') {
                    Session::flash('error', 'Escribe un motivo para devolver el documento.');
                    header('Location: ' . APP_URL . '/verificacion/' . $empleadoId . '/' . $documentoId . '?estado=' . urlencode($_GET['estado'] ?? 'pendiente'));
                    exit;
                }
                DB::execute("
                    UPDATE empleado_documentos
                    SET estado_verificacion = 'devuelto', motivo_devolucion = ?,
                        verificado_por = ?, verificado_en = NOW()
                    WHERE empleado_id = ? AND documento_id = ?
                ", [$motivo, $quienVerifica, $empleadoId, $documentoId]);
                Session::flash('success', 'Documento devuelto.');
                break;

            case 'reabrir':
                // Vuelve a "pendiente" (por si se aprobó/devolvió por error)
                DB::execute("
                    UPDATE empleado_documentos
                    SET estado_verificacion = 'pendiente', motivo_devolucion = NULL,
                        verificado_por = NULL, verificado_en = NULL
                    WHERE empleado_id = ? AND documento_id = ?
                ", [$empleadoId, $documentoId]);
                Session::flash('success', 'Documento marcado nuevamente como pendiente.');
                break;

            default:
                Session::flash('error', 'Acción no reconocida.');
        }

        $estado = $_GET['estado'] ?? 'pendiente';
        header('Location: ' . APP_URL . '/verificacion?estado=' . urlencode($estado));
        exit;
    }
}
