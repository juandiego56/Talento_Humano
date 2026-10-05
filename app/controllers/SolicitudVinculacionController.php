<?php
class SolicitudVinculacionController {

    public function index(): void {
        Auth::requireAuth();
        if (!Auth::esDirectorPrograma() && !Auth::puedeGestionar()) {
            header('Location: ' . APP_URL . '/?error=sin_permiso');
            exit;
        }

        $estado = $_GET['estado'] ?? '';
        $where = []; $params = [];

        if (Auth::esDirectorPrograma() && !Auth::puedeGestionar()) {
            $where[] = 'sv.programa_id = ?';
            $params[] = Auth::programaId();
        }
        if ($estado !== '') { $where[] = 'sv.estado = ?'; $params[] = $estado; }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $solicitudes = DB::fetchAll("
            SELECT sv.*, pa.nombre AS programa_nombre, u.nombre AS solicitante_nombre
            FROM solicitudes_vinculacion sv
            JOIN programas_academicos pa ON pa.id = sv.programa_id
            JOIN usuarios u ON u.id = sv.solicitado_por
            $whereSql
            ORDER BY FIELD(sv.estado,'pendiente','aprobada','rechazada'), sv.fecha_solicitud DESC
        ", $params);

        View::render('solicitudes.index', [
            'titulo'      => 'Solicitudes de Vinculación Laboral',
            'solicitudes' => $solicitudes,
            'estado'      => $estado,
        ]);
    }

    public function crear(): void {
        Auth::requireRol(ROL_DIRECTOR_PROGRAMA);
        if (!Auth::programaId()) {
            Session::flash('error', 'Tu usuario no tiene un programa académico asignado. Contacta a un administrador.');
            header('Location: ' . APP_URL . '/solicitudes-vinculacion');
            exit;
        }
        View::render('solicitudes.crear', ['titulo' => 'Nueva Solicitud de Vinculación']);
    }

    public function guardar(): void {
        Auth::requireRol(ROL_DIRECTOR_PROGRAMA);
        $programaId = Auth::programaId();
        if (!$programaId) {
            Session::flash('error', 'Tu usuario no tiene un programa académico asignado. Contacta a un administrador.');
            header('Location: ' . APP_URL . '/solicitudes-vinculacion');
            exit;
        }

        $justificacion = trim($_POST['justificacion'] ?? '');
        $nombres       = trim($_POST['nombres_candidato'] ?? '');
        $apellidos     = trim($_POST['apellidos_candidato'] ?? '');

        if ($justificacion === '' || $nombres === '' || $apellidos === '') {
            Session::flash('error', 'Nombres, apellidos del candidato y justificación son obligatorios.');
            header('Location: ' . APP_URL . '/solicitudes-vinculacion/crear');
            exit;
        }

        $id = DB::insert("
            INSERT INTO solicitudes_vinculacion
              (programa_id, solicitado_por, nombres_candidato, apellidos_candidato, cargo_sugerido,
               nivel_educativo_requerido, tipo_vinculacion_docente, justificacion, fecha_requerida, estado)
            VALUES (?,?,?,?,?,?,?,?,?, 'pendiente')
        ", [
            $programaId, Auth::user()['id'], $nombres, $apellidos,
            ($_POST['cargo_sugerido'] ?? '') ?: null,
            ($_POST['nivel_educativo_requerido'] ?? '') ?: null,
            ($_POST['tipo_vinculacion_docente'] ?? '') ?: null,
            $justificacion,
            ($_POST['fecha_requerida'] ?? '') ?: null,
        ]);

        Session::flash('success', 'Solicitud de vinculación enviada a Talento Humano.');
        header('Location: ' . APP_URL . '/solicitudes-vinculacion/' . $id);
        exit;
    }

    public function ver(string $id): void {
        Auth::requireAuth();
        $solicitud = $this->obtenerSolicitud($id);
        $this->verificarAcceso($solicitud);

        View::render('solicitudes.ver', [
            'titulo'    => 'Solicitud de Vinculación',
            'solicitud' => $solicitud,
        ]);
    }

    public function aprobar(string $id): void {
        Auth::requireGestion();
        $this->obtenerSolicitud($id);
        DB::execute("
            UPDATE solicitudes_vinculacion
            SET estado = 'aprobada', respuesta = ?, atendida_por = ?, fecha_respuesta = NOW()
            WHERE id = ?
        ", [($_POST['respuesta'] ?? '') ?: null, Auth::user()['id'], $id]);
        Session::flash('success', 'Solicitud aprobada.');
        header('Location: ' . APP_URL . '/solicitudes-vinculacion/' . $id);
        exit;
    }

    public function rechazar(string $id): void {
        Auth::requireGestion();
        $this->obtenerSolicitud($id);
        DB::execute("
            UPDATE solicitudes_vinculacion
            SET estado = 'rechazada', respuesta = ?, atendida_por = ?, fecha_respuesta = NOW()
            WHERE id = ?
        ", [($_POST['respuesta'] ?? '') ?: null, Auth::user()['id'], $id]);
        Session::flash('success', 'Solicitud rechazada.');
        header('Location: ' . APP_URL . '/solicitudes-vinculacion/' . $id);
        exit;
    }

    private function obtenerSolicitud(string $id): array {
        $solicitud = DB::fetch("
            SELECT sv.*, pa.nombre AS programa_nombre, u.nombre AS solicitante_nombre,
                   ua.nombre AS atendida_por_nombre
            FROM solicitudes_vinculacion sv
            JOIN programas_academicos pa ON pa.id = sv.programa_id
            JOIN usuarios u ON u.id = sv.solicitado_por
            LEFT JOIN usuarios ua ON ua.id = sv.atendida_por
            WHERE sv.id = ?
        ", [$id]);
        if (!$solicitud) { http_response_code(404); exit('Solicitud no encontrada.'); }
        return $solicitud;
    }

    private function verificarAcceso(array $solicitud): void {
        if (Auth::puedeGestionar()) return;
        if (Auth::esDirectorPrograma() && (int)$solicitud['programa_id'] === Auth::programaId()) return;
        header('Location: ' . APP_URL . '/?error=sin_permiso');
        exit;
    }
}
