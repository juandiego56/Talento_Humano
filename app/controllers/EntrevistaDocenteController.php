<?php
class EntrevistaDocenteController {

    private function obtenerEmpleado(string $id): array {
        $empleado = DB::fetch("
            SELECT e.*, c.nombre AS cargo, a.nombre AS area
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            WHERE e.id = ?
        ", [$id]);
        if (!$empleado) {
            http_response_code(404);
            echo 'Empleado no encontrado.';
            exit;
        }
        return $empleado;
    }

    public function ver(string $id): void {
        Auth::requireAuth();
        $empleado   = $this->obtenerEmpleado($id);
        $entrevista = DB::fetch("SELECT * FROM entrevistas_docente WHERE empleado_id = ?", [$id]);

        View::render('entrevistas_docente.ver', [
            'titulo'     => 'Entrevista Personal Docente — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'   => $empleado,
            'entrevista' => $entrevista,
        ]);
    }

    public function formulario(string $id): void {
        Auth::requireGestion();
        $empleado   = $this->obtenerEmpleado($id);
        $entrevista = DB::fetch("SELECT * FROM entrevistas_docente WHERE empleado_id = ?", [$id]);

        View::render('entrevistas_docente.formulario', [
            'titulo'     => 'Diligenciar Entrevista Docente — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'   => $empleado,
            'entrevista' => $entrevista,
        ]);
    }

    public function guardar(string $id): void {
        Auth::requireGestion();
        $this->obtenerEmpleado($id); // valida que exista

        $p = fn(string $k) => ($_POST[$k] ?? '') !== '' ? $_POST[$k] : null;

        DB::execute("
            INSERT INTO entrevistas_docente
              (empleado_id, nombre_entrevistador, cargo_entrevistador, programa_academico,
               fecha_entrevista, modalidad, nombre_entrevistado, profesion,
               posgrados_estado, posgrados_detalle, doc_tipo, doc_numero, doc_lugar_expedicion,
               tipo_vinculacion_docente, competencias_personales, competencias_academicas,
               competencias_profesionales, concepto_general,
               disponibilidad_tiempo, acepta_condiciones, conclusiones, firma_entrevistador)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
               nombre_entrevistador = VALUES(nombre_entrevistador),
               cargo_entrevistador = VALUES(cargo_entrevistador),
               programa_academico = VALUES(programa_academico),
               fecha_entrevista = VALUES(fecha_entrevista),
               modalidad = VALUES(modalidad),
               nombre_entrevistado = VALUES(nombre_entrevistado),
               profesion = VALUES(profesion),
               posgrados_estado = VALUES(posgrados_estado),
               posgrados_detalle = VALUES(posgrados_detalle),
               doc_tipo = VALUES(doc_tipo),
               doc_numero = VALUES(doc_numero),
               doc_lugar_expedicion = VALUES(doc_lugar_expedicion),
               tipo_vinculacion_docente = VALUES(tipo_vinculacion_docente),
               competencias_personales = VALUES(competencias_personales),
               competencias_academicas = VALUES(competencias_academicas),
               competencias_profesionales = VALUES(competencias_profesionales),
               concepto_general = VALUES(concepto_general),
               disponibilidad_tiempo = VALUES(disponibilidad_tiempo),
               acepta_condiciones = VALUES(acepta_condiciones),
               conclusiones = VALUES(conclusiones),
               firma_entrevistador = VALUES(firma_entrevistador)
        ", [
            $id,
            $p('nombre_entrevistador'), $p('cargo_entrevistador'), $p('programa_academico'),
            $p('fecha_entrevista'), $p('modalidad'), $p('nombre_entrevistado'), $p('profesion'),
            $p('posgrados_estado'), $p('posgrados_detalle'), $p('doc_tipo'), $p('doc_numero'), $p('doc_lugar_expedicion'),
            $p('tipo_vinculacion_docente'), $p('competencias_personales'), $p('competencias_academicas'),
            $p('competencias_profesionales'), $p('concepto_general'),
            $p('disponibilidad_tiempo'), $p('acepta_condiciones'), $p('conclusiones'), $p('firma_entrevistador'),
        ]);

        Session::flash('success', 'Entrevista de personal docente guardada.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/entrevista-docente');
        exit;
    }

    public function imprimir(string $id): void {
        Auth::requireAuth();
        $empleado   = $this->obtenerEmpleado($id);
        $entrevista = DB::fetch("SELECT * FROM entrevistas_docente WHERE empleado_id = ?", [$id]);

        View::render('entrevistas_docente.imprimir', [
            'titulo'     => 'FO-TH-031 — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'   => $empleado,
            'entrevista' => $entrevista,
        ], layout: 'imprimible');
    }

    public function eliminar(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM entrevistas_docente WHERE empleado_id = ?", [$id]);
        Session::flash('success', 'Entrevista docente eliminada.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/entrevista-docente');
        exit;
    }
}
