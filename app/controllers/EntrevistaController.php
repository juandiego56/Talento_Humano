<?php
class EntrevistaController {

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
        $entrevista = DB::fetch("SELECT * FROM entrevistas_personal WHERE empleado_id = ?", [$id]);

        View::render('entrevistas.ver', [
            'titulo'     => 'Entrevista de Personal — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'   => $empleado,
            'entrevista' => $entrevista,
        ]);
    }

    public function formulario(string $id): void {
        Auth::requireGestion();
        $empleado   = $this->obtenerEmpleado($id);
        $entrevista = DB::fetch("SELECT * FROM entrevistas_personal WHERE empleado_id = ?", [$id]);

        View::render('entrevistas.formulario', [
            'titulo'     => 'Diligenciar Entrevista — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'   => $empleado,
            'entrevista' => $entrevista,
        ]);
    }

    public function guardar(string $id): void {
        Auth::requireGestion();
        $this->obtenerEmpleado($id); // valida que exista

        $p = fn(string $k) => ($_POST[$k] ?? '') !== '' ? $_POST[$k] : null;

        DB::execute("
            INSERT INTO entrevistas_personal
              (empleado_id, nombre_entrevistador, cargo_entrevistador, area_entrevistador,
               fecha_entrevista, modalidad, nombre_entrevistado, profesion, cargo_aspira,
               posgrados_estado, posgrados_detalle, doc_tipo, doc_numero, doc_lugar_expedicion,
               informacion_personal, formacion_academica, experiencia_laboral,
               resultado_prueba_psicotecnica, resultado_prueba_tecnica,
               disponibilidad_tiempo, acepta_condiciones, conclusiones, decision, firma_entrevistador)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
               nombre_entrevistador = VALUES(nombre_entrevistador),
               cargo_entrevistador = VALUES(cargo_entrevistador),
               area_entrevistador = VALUES(area_entrevistador),
               fecha_entrevista = VALUES(fecha_entrevista),
               modalidad = VALUES(modalidad),
               nombre_entrevistado = VALUES(nombre_entrevistado),
               profesion = VALUES(profesion),
               cargo_aspira = VALUES(cargo_aspira),
               posgrados_estado = VALUES(posgrados_estado),
               posgrados_detalle = VALUES(posgrados_detalle),
               doc_tipo = VALUES(doc_tipo),
               doc_numero = VALUES(doc_numero),
               doc_lugar_expedicion = VALUES(doc_lugar_expedicion),
               informacion_personal = VALUES(informacion_personal),
               formacion_academica = VALUES(formacion_academica),
               experiencia_laboral = VALUES(experiencia_laboral),
               resultado_prueba_psicotecnica = VALUES(resultado_prueba_psicotecnica),
               resultado_prueba_tecnica = VALUES(resultado_prueba_tecnica),
               disponibilidad_tiempo = VALUES(disponibilidad_tiempo),
               acepta_condiciones = VALUES(acepta_condiciones),
               conclusiones = VALUES(conclusiones),
               decision = VALUES(decision),
               firma_entrevistador = VALUES(firma_entrevistador)
        ", [
            $id,
            $p('nombre_entrevistador'), $p('cargo_entrevistador'), $p('area_entrevistador'),
            $p('fecha_entrevista'), $p('modalidad'), $p('nombre_entrevistado'), $p('profesion'), $p('cargo_aspira'),
            $p('posgrados_estado'), $p('posgrados_detalle'), $p('doc_tipo'), $p('doc_numero'), $p('doc_lugar_expedicion'),
            $p('informacion_personal'), $p('formacion_academica'), $p('experiencia_laboral'),
            $p('resultado_prueba_psicotecnica'), $p('resultado_prueba_tecnica'),
            $p('disponibilidad_tiempo'), $p('acepta_condiciones'), $p('conclusiones'), $p('decision'), $p('firma_entrevistador'),
        ]);

        Session::flash('success', 'Entrevista de personal guardada.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/entrevista');
        exit;
    }

    public function imprimir(string $id): void {
        Auth::requireAuth();
        $empleado   = $this->obtenerEmpleado($id);
        $entrevista = DB::fetch("SELECT * FROM entrevistas_personal WHERE empleado_id = ?", [$id]);

        View::render('entrevistas.imprimir', [
            'titulo'     => 'FO-TH-009 — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'   => $empleado,
            'entrevista' => $entrevista,
        ], layout: 'imprimible');
    }

    public function eliminar(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM entrevistas_personal WHERE empleado_id = ?", [$id]);
        Session::flash('success', 'Entrevista eliminada.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/entrevista');
        exit;
    }
}
