<?php
/**
 * Formulario público (sin sesión) para que un aspirante diligencie su
 * Hoja de Vida (FO-TH-018) y quede registrado directamente como empleado
 * en estado "inactivo", pendiente de revisión y activación por Talento Humano.
 */
class PublicHojaVidaController {

    public function formulario(): void {
        View::render('publico.hojavida_formulario', [
            'titulo' => 'Formato Único de Hoja de Vida — FO-TH-018',
            'errores' => [],
            'datos'   => [],
        ], layout: 'imprimible');
    }

    public function guardar(): void {
        // Validación mínima de campos obligatorios
        $requeridos = ['tipo_documento', 'numero_documento', 'nombres', 'apellidos'];
        $errores = [];
        foreach ($requeridos as $campo) {
            if (trim($_POST[$campo] ?? '') === '') {
                $errores[] = 'El campo "' . $campo . '" es obligatorio.';
            }
        }

        if ($errores) {
            View::render('publico.hojavida_formulario', [
                'titulo'  => 'Formato Único de Hoja de Vida — FO-TH-018',
                'errores' => $errores,
                'datos'   => $_POST,
            ], layout: 'imprimible');
            return;
        }

        try {
            $id = DB::insert("
                INSERT INTO empleados
                  (tipo_documento, numero_documento, nombres, apellidos, fecha_nacimiento, genero,
                   estado_civil, direccion, telefono, email, fecha_ingreso,
                   tipo_contrato, salario_base, eps, fondo_pension, arl, tipo_sangre,
                   contacto_emergencia_nombre, contacto_emergencia_telefono, estado,
                   banco, tipo_cuenta, numero_cuenta)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
                $_POST['tipo_documento'], $_POST['numero_documento'], $_POST['nombres'], $_POST['apellidos'],
                ($_POST['fecha_nacimiento'] ?? '') ?: null, ($_POST['genero'] ?? '') ?: null,
                ($_POST['estado_civil'] ?? '') ?: null, ($_POST['direccion'] ?? '') ?: null,
                ($_POST['telefono'] ?? '') ?: null, ($_POST['email'] ?? '') ?: null,
                date('Y-m-d'), 'termino_fijo', 0,
                ($_POST['eps'] ?? '') ?: null, ($_POST['fondo_pension'] ?? '') ?: null, ($_POST['arl'] ?? '') ?: null,
                ($_POST['tipo_sangre'] ?? '') ?: null, ($_POST['contacto_emergencia_nombre'] ?? '') ?: null,
                ($_POST['contacto_emergencia_telefono'] ?? '') ?: null, 'inactivo',
                ($_POST['banco'] ?? '') ?: null, ($_POST['tipo_cuenta'] ?? '') ?: null, ($_POST['numero_cuenta'] ?? '') ?: null,
            ]);

            // Inicializa lista de chequeo con todos los documentos activos
            DB::execute("
                INSERT INTO empleado_documentos (empleado_id, documento_id, entregado)
                SELECT ?, id, 0 FROM documentos_requeridos WHERE activo = 1
            ", [$id]);

            $this->procesarFormacionYExperiencia($id);

            View::render('publico.hojavida_enviada', [
                'titulo' => 'Hoja de vida enviada',
            ], layout: 'imprimible');
        } catch (\PDOException $e) {
            $errores = [str_contains($e->getMessage(), 'Duplicate')
                ? 'Ya existe una hoja de vida registrada con ese número de documento.'
                : 'Ocurrió un error al guardar. Intenta nuevamente.'];
            View::render('publico.hojavida_formulario', [
                'titulo'  => 'Formato Único de Hoja de Vida — FO-TH-018',
                'errores' => $errores,
                'datos'   => $_POST,
            ], layout: 'imprimible');
        }
    }

    // ── Diligenciar/actualizar la hoja de vida de un empleado YA existente ──
    // Acceso público (sin sesión) mediante un link único por empleado.

    public function formularioEmpleado(string $id): void {
        $empleado = DB::fetch("SELECT * FROM empleados WHERE id = ?", [$id]);
        if (!$empleado) { http_response_code(404); echo 'Enlace no válido.'; exit; }

        $educacion   = DB::fetchAll("SELECT * FROM empleado_educacion WHERE empleado_id = ? ORDER BY anio_graduacion DESC LIMIT 3", [$id]);
        $experiencia = DB::fetchAll("SELECT * FROM empleado_experiencia WHERE empleado_id = ? ORDER BY fecha_inicio DESC LIMIT 3", [$id]);
        $notas       = DB::fetchAll("SELECT seccion, motivo FROM empleado_hojavida_notas WHERE empleado_id = ?", [$id]);
        $notasPorSeccion = [];
        foreach ($notas as $n) { $notasPorSeccion[$n['seccion']] = $n['motivo']; }

        $datos = $empleado;
        foreach ($educacion as $i => $ed) {
            $n = $i + 1;
            $datos["educ{$n}_nivel"]            = $ed['nivel_educativo'];
            $datos["educ{$n}_institucion"]      = $ed['institucion'];
            $datos["educ{$n}_titulo"]           = $ed['titulo_obtenido'];
            $datos["educ{$n}_anio"]             = $ed['anio_graduacion'];
            $datos["educ{$n}_fecha_expedicion"] = $ed['fecha_expedicion'];
            $datos["educ{$n}_en_curso"]         = $ed['en_curso'];
        }
        foreach ($experiencia as $i => $ex) {
            $n = $i + 1;
            $datos["exp{$n}_empresa"]   = $ex['empresa'];
            $datos["exp{$n}_cargo"]     = $ex['cargo'];
            $datos["exp{$n}_inicio"]    = $ex['fecha_inicio'];
            $datos["exp{$n}_fin"]       = $ex['fecha_fin'];
            $datos["exp{$n}_funciones"] = $ex['funciones'];
        }
        // Si un bloque quedó marcado "no aplica" la última vez, lo reflejamos aunque
        // ese bloque no tenga registro en empleado_educacion/empleado_experiencia.
        for ($n = 1; $n <= 3; $n++) {
            if (isset($notasPorSeccion["Formación académica {$n}"])) {
                $datos["educ{$n}_no_aplica"] = '1';
                $datos["educ{$n}_motivo_otro"] = $notasPorSeccion["Formación académica {$n}"];
            }
            if (isset($notasPorSeccion["Experiencia laboral {$n}"])) {
                $datos["exp{$n}_no_aplica"] = '1';
                $datos["exp{$n}_motivo_otro"] = $notasPorSeccion["Experiencia laboral {$n}"];
            }
        }

        View::render('publico.hojavida_formulario', [
            'titulo'       => 'Formato Único de Hoja de Vida — FO-TH-018',
            'errores'      => [],
            'datos'        => $datos,
            'empleadoId'   => $id,
            'accionGuardar'=> APP_URL . '/empleados/' . $id . '/diligenciar/guardar',
        ], layout: 'imprimible');
    }

    public function guardarEmpleado(string $id): void {
        $empleado = DB::fetch("SELECT id FROM empleados WHERE id = ?", [$id]);
        if (!$empleado) { http_response_code(404); echo 'Enlace no válido.'; exit; }

        $requeridos = ['tipo_documento', 'numero_documento', 'nombres', 'apellidos'];
        $errores = [];
        foreach ($requeridos as $campo) {
            if (trim($_POST[$campo] ?? '') === '') {
                $errores[] = 'El campo "' . $campo . '" es obligatorio.';
            }
        }

        if ($errores) {
            View::render('publico.hojavida_formulario', [
                'titulo'        => 'Formato Único de Hoja de Vida — FO-TH-018',
                'errores'       => $errores,
                'datos'         => $_POST,
                'empleadoId'    => $id,
                'accionGuardar' => APP_URL . '/empleados/' . $id . '/diligenciar/guardar',
            ], layout: 'imprimible');
            return;
        }

        try {
            DB::execute("
                UPDATE empleados SET
                  tipo_documento=?, numero_documento=?, nombres=?, apellidos=?, fecha_nacimiento=?, genero=?,
                  estado_civil=?, direccion=?, telefono=?, email=?, eps=?, fondo_pension=?, arl=?, tipo_sangre=?,
                  contacto_emergencia_nombre=?, contacto_emergencia_telefono=?,
                  banco=?, tipo_cuenta=?, numero_cuenta=?
                WHERE id=?
            ", [
                $_POST['tipo_documento'], $_POST['numero_documento'], $_POST['nombres'], $_POST['apellidos'],
                ($_POST['fecha_nacimiento'] ?? '') ?: null, ($_POST['genero'] ?? '') ?: null,
                ($_POST['estado_civil'] ?? '') ?: null, ($_POST['direccion'] ?? '') ?: null,
                ($_POST['telefono'] ?? '') ?: null, ($_POST['email'] ?? '') ?: null,
                ($_POST['eps'] ?? '') ?: null, ($_POST['fondo_pension'] ?? '') ?: null, ($_POST['arl'] ?? '') ?: null,
                ($_POST['tipo_sangre'] ?? '') ?: null, ($_POST['contacto_emergencia_nombre'] ?? '') ?: null,
                ($_POST['contacto_emergencia_telefono'] ?? '') ?: null,
                ($_POST['banco'] ?? '') ?: null, ($_POST['tipo_cuenta'] ?? '') ?: null, ($_POST['numero_cuenta'] ?? '') ?: null,
                $id,
            ]);

            // Reemplaza formación y experiencia con lo diligenciado en el formulario
            DB::execute("DELETE FROM empleado_educacion WHERE empleado_id = ?", [$id]);
            DB::execute("DELETE FROM empleado_experiencia WHERE empleado_id = ?", [$id]);

            $this->procesarFormacionYExperiencia($id);

            View::render('publico.hojavida_enviada', [
                'titulo' => 'Hoja de vida actualizada',
            ], layout: 'imprimible');
        } catch (\PDOException $e) {
            $errores = [str_contains($e->getMessage(), 'Duplicate')
                ? 'Ese número de documento ya está en uso por otro registro.'
                : 'Ocurrió un error al guardar. Intenta nuevamente.'];
            View::render('publico.hojavida_formulario', [
                'titulo'        => 'Formato Único de Hoja de Vida — FO-TH-018',
                'errores'       => $errores,
                'datos'         => $_POST,
                'empleadoId'    => $id,
                'accionGuardar' => APP_URL . '/empleados/' . $id . '/diligenciar/guardar',
            ], layout: 'imprimible');
        }
    }

    /**
     * Procesa los hasta-3 bloques de formación académica y hasta-3 bloques de experiencia
     * laboral del formulario. Un bloque marcado "no aplica" no se guarda como registro
     * normal: en su lugar queda una nota (empleado_hojavida_notas) con el motivo elegido.
     */
    private function procesarFormacionYExperiencia(string $empleadoId): void {
        DB::execute("DELETE FROM empleado_hojavida_notas WHERE empleado_id = ?", [$empleadoId]);

        for ($i = 1; $i <= 3; $i++) {
            if (isset($_POST["educ{$i}_no_aplica"])) {
                $this->guardarNotaHojaVida($empleadoId, "Formación académica {$i}", $_POST["educ{$i}_motivo"] ?? '', $_POST["educ{$i}_motivo_otro"] ?? '');
                continue;
            }
            $institucion = trim($_POST["educ{$i}_institucion"] ?? '');
            if ($institucion === '') continue;
            DB::insert("
                INSERT INTO empleado_educacion
                  (empleado_id, nivel_educativo, institucion, titulo_obtenido, anio_graduacion, fecha_expedicion, en_curso)
                VALUES (?,?,?,?,?,?,?)
            ", [
                $empleadoId,
                $_POST["educ{$i}_nivel"] ?? 'bachillerato',
                $institucion,
                ($_POST["educ{$i}_titulo"] ?? '') ?: null,
                ($_POST["educ{$i}_anio"] ?? '') ?: null,
                ($_POST["educ{$i}_fecha_expedicion"] ?? '') ?: null,
                isset($_POST["educ{$i}_en_curso"]) ? 1 : 0,
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            if (isset($_POST["exp{$i}_no_aplica"])) {
                $this->guardarNotaHojaVida($empleadoId, "Experiencia laboral {$i}", $_POST["exp{$i}_motivo"] ?? '', $_POST["exp{$i}_motivo_otro"] ?? '');
                continue;
            }
            $empresa = trim($_POST["exp{$i}_empresa"] ?? '');
            if ($empresa === '') continue;
            DB::insert("
                INSERT INTO empleado_experiencia (empleado_id, empresa, cargo, fecha_inicio, fecha_fin, funciones)
                VALUES (?,?,?,?,?,?)
            ", [
                $empleadoId,
                $empresa,
                $_POST["exp{$i}_cargo"] ?? '',
                ($_POST["exp{$i}_inicio"] ?? '') ?: null,
                ($_POST["exp{$i}_fin"] ?? '') ?: null,
                ($_POST["exp{$i}_funciones"] ?? '') ?: null,
            ]);
        }
    }

    private function guardarNotaHojaVida(string $empleadoId, string $seccion, string $motivoSeleccionado, string $motivoOtro): void {
        $motivo = $motivoSeleccionado === 'Otro' ? trim($motivoOtro) : $motivoSeleccionado;
        if ($motivo === '') $motivo = null;
        DB::insert("
            INSERT INTO empleado_hojavida_notas (empleado_id, seccion, motivo) VALUES (?,?,?)
        ", [$empleadoId, $seccion, $motivo]);
    }
}