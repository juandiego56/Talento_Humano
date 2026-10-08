<?php
require_once ROOT . '/app/helpers/Schema.php';
require_once ROOT . '/app/helpers/Validador.php';

/**
 * Hoja de vida diligenciada por el propio empleado (con su usuario) y validada por Talento Humano.
 * Estados: borrador → enviada (en revisión) → aprobada | devuelta (con observaciones) → enviada…
 * Talento Humano solo la visualiza y la aprueba o devuelve; no edita lo que el empleado llenó.
 */
class HojaVidaController {
    private const PASOS = [
        1 => 'Identificación',
        2 => 'Dirección y contacto',
        3 => 'Seguridad social y banco',
        4 => 'Formación académica',
        5 => 'Formación complementaria',
        6 => 'Experiencia profesional',
        7 => 'Ver y enviar hoja de vida',
    ];

    private const CAMPOS_RESPALDO = [
        'fecha_nacimiento', 'genero', 'estado_civil', 'tipo_sangre', 'direccion', 'telefono',
        'contacto_emergencia_nombre', 'contacto_emergencia_telefono', 'eps', 'fondo_pension', 'arl',
        'banco', 'tipo_cuenta', 'numero_cuenta',
    ];
    private const TABLAS_RESPALDO = ['empleado_educacion', 'empleado_experiencia', 'empleado_formacion_complementaria', 'empleado_hojavida_notas'];

    // ── Utilidades ───────────────────────────────────────────────────────

    private function empleado(): array {
        Auth::requireAuth();
        Schema::asegurarHojaVida();
        $id = Auth::empleadoId();
        if (!$id) {
            Session::flash('error', 'Tu usuario no está vinculado a un registro de empleado. Contacta a Talento Humano.');
            header('Location: ' . APP_URL . '/');
            exit;
        }
        $e = DB::fetch("SELECT * FROM empleados WHERE id = ?", [$id]);
        if (!$e) { header('Location: ' . APP_URL . '/'); exit; }
        return $e;
    }

    private function editable(array $e): bool {
        return in_array($e['hojavida_estado'] ?? 'borrador', ['borrador', 'devuelta'], true);
    }

    private function ir(string $ruta): never {
        header('Location: ' . APP_URL . '/' . ltrim($ruta, '/'));
        exit;
    }

    /** Si la hoja ya no se puede editar (en revisión o aprobada), solo se deja ver el paso final. */
    private function exigirEditable(array $e): void {
        if (!$this->editable($e)) {
            Session::flash('warning', 'Tu hoja de vida está ' . mb_strtolower(View::hojaVidaEstadoLabel($e['hojavida_estado'])) . ' y no se puede modificar en este momento.');
            $this->ir('mi-hoja-de-vida/paso/7');
        }
    }

    private function educacion(int $id): array {
        return DB::fetchAll("SELECT * FROM empleado_educacion WHERE empleado_id = ? ORDER BY COALESCE(fecha_expedicion, '9999-12-31') DESC, id DESC", [$id]);
    }

    private function experiencia(int $id): array {
        return DB::fetchAll("SELECT * FROM empleado_experiencia WHERE empleado_id = ? ORDER BY fecha_inicio DESC, id DESC", [$id]);
    }

    private function sinExperiencia(int $id): bool {
        return (bool)DB::fetch("SELECT id FROM empleado_hojavida_notas WHERE empleado_id = ? AND seccion = 'Experiencia laboral'", [$id]);
    }

    /** Valida los datos de un paso (1-3). Devuelve la lista de errores. */
    private function validarPaso(int $paso, array $d): array {
        switch ($paso) {
            case 1:
                return Validador::errores(
                    Validador::fechaNacimiento($d['fecha_nacimiento'] ?? ''),
                    Validador::enLista($d['genero'] ?? '', ['M', 'F', 'Otro'], 'El género'),
                    Validador::enLista($d['estado_civil'] ?? '', Validador::ESTADOS_CIVILES, 'El estado civil'),
                    Validador::enLista($d['tipo_sangre'] ?? '', Validador::TIPOS_SANGRE, 'El tipo de sangre')
                );
            case 2:
                return Validador::errores(
                    Validador::direccion($d['direccion'] ?? ''),
                    Validador::telefono($d['telefono'] ?? ''),
                    Validador::nombrePersona($d['contacto_emergencia_nombre'] ?? '', 'El nombre del contacto de emergencia'),
                    Validador::telefono($d['contacto_emergencia_telefono'] ?? '', 'El teléfono del contacto de emergencia')
                );
            case 3:
                $fondo = trim((string)($d['fondo_pension'] ?? ''));
                return Validador::errores(
                    mb_strlen(Validador::texto($d['eps'] ?? '')) < 2 ? 'La EPS es obligatoria.' : null,
                    $fondo === '' ? 'El fondo de pensión es obligatorio (si no está en la lista, elige "Otro" y escríbelo).' : null,
                    mb_strlen(Validador::texto($d['arl'] ?? '')) < 2 ? 'La ARL es obligatoria.' : null,
                    mb_strlen(Validador::texto($d['banco'] ?? '')) < 2 ? 'El banco es obligatorio.' : null,
                    Validador::enLista($d['tipo_cuenta'] ?? '', ['ahorros', 'corriente'], 'El tipo de cuenta'),
                    Validador::cuenta($d['numero_cuenta'] ?? '')
                );
        }
        return [];
    }

    /** Lo que le falta a la hoja de vida, agrupado por paso: [paso => [mensajes]]. */
    private function faltantes(array $e, array $educacion, array $experiencia, bool $sinExp): array {
        $f = [];
        foreach ([1, 2, 3] as $paso) {
            $datos = $e;
            $errs = [];
            foreach ($this->validarPaso($paso, $datos) as $m) $errs[] = $m;
            // Un campo vacío ya se reporta como "obligatorio"; se muestra tal cual.
            if ($errs) $f[$paso] = $errs;
        }
        if (!$educacion) $f[4] = ['Agrega al menos un estudio realizado (por ejemplo, tu bachillerato).'];
        if (!$experiencia && !$sinExp) $f[6] = ['Agrega tu experiencia laboral o marca que aún no tienes experiencia.'];
        return $f;
    }

    // ── Vistas del empleado ──────────────────────────────────────────────

    public function index(): void {
        $e = $this->empleado();
        if (!$this->editable($e)) $this->ir('mi-hoja-de-vida/paso/7');
        $f = $this->faltantes($e, $this->educacion((int)$e['id']), $this->experiencia((int)$e['id']), $this->sinExperiencia((int)$e['id']));
        $this->ir('mi-hoja-de-vida/paso/' . ($f ? min(array_keys($f)) : 7));
    }

    public function paso(string $n): void {
        $e = $this->empleado();
        $paso = (int)$n;
        if ($paso < 1 || $paso > 7) $this->ir('mi-hoja-de-vida');
        if ($paso < 7) $this->exigirEditable($e);

        $educacion   = $this->educacion((int)$e['id']);
        $experiencia = $this->experiencia((int)$e['id']);
        $sinExp      = $this->sinExperiencia((int)$e['id']);
        $faltantes   = $this->faltantes($e, $educacion, $experiencia, $sinExp);

        // Si el paso anterior falló, se conservan los datos que la persona había escrito.
        $old = Session::get('old_hv') ?: [];
        Session::set('old_hv', null);

        View::render('hojavida.formulario', [
            'titulo'      => 'Mi hoja de vida',
            'empleado'    => $e,
            'datos'       => array_merge($e, $old),
            'paso'        => $paso,
            'pasos'       => self::PASOS,
            'editable'    => $this->editable($e),
            'faltantes'   => $faltantes,
            'educacion'   => $educacion,
            'experiencia' => $experiencia,
            'sinExp'      => $sinExp,
            'complementaria' => DB::fetchAll("SELECT * FROM empleado_formacion_complementaria WHERE empleado_id = ? ORDER BY fecha DESC, id DESC", [$e['id']]),
            'editarCompId' => (int)($_GET['editar_complementaria'] ?? 0),
            'editarEduId' => (int)($_GET['editar_educacion'] ?? 0),
            'editarExpId' => (int)($_GET['editar_experiencia'] ?? 0),
        ]);
    }

    public function guardarPaso(string $n): void {
        $e = $this->empleado();
        $this->exigirEditable($e);
        $paso = (int)$n;
        if ($paso < 1 || $paso > 3 || $_SERVER['REQUEST_METHOD'] !== 'POST') $this->ir('mi-hoja-de-vida');

        $p = $_POST;
        $p['fondo_pension'] = Validador::fondoPension($p);
        $errores = $this->validarPaso($paso, $p);
        if ($errores) {
            Session::set('old_hv', $_POST);
            Session::flash('error', implode(' · ', $errores));
            $this->ir('mi-hoja-de-vida/paso/' . $paso);
        }

        $campos = [
            1 => ['fecha_nacimiento', 'genero', 'estado_civil', 'tipo_sangre'],
            2 => ['direccion', 'telefono', 'contacto_emergencia_nombre', 'contacto_emergencia_telefono'],
            3 => ['eps', 'fondo_pension', 'arl', 'banco', 'tipo_cuenta', 'numero_cuenta'],
        ][$paso];
        $set = []; $vals = [];
        foreach ($campos as $c) { $set[] = "$c = ?"; $vals[] = Validador::texto($p[$c] ?? '') ?: null; }
        $vals[] = $e['id'];
        DB::execute("UPDATE empleados SET " . implode(', ', $set) . " WHERE id = ?", $vals);

        Session::flash('success', 'Paso ' . $paso . ' guardado: ' . self::PASOS[$paso] . '.');
        $this->ir('mi-hoja-de-vida/paso/' . ($paso + 1));
    }

    // ── Formación académica ──────────────────────────────────────────────

    public function guardarFormacion(): void {
        $e = $this->empleado();
        $this->exigirEditable($e);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->ir('mi-hoja-de-vida/paso/4');

        $p = $_POST;
        $id = (int)($p['id'] ?? 0);
        $enCurso = isset($p['en_curso']);
        $fecha = trim($p['fecha_expedicion'] ?? '');

        $errores = Validador::errores(
            Validador::enLista($p['nivel_educativo'] ?? '', ['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'], 'El nivel de estudio'),
            mb_strlen(Validador::texto($p['institucion'] ?? '')) < 3 ? 'La institución es obligatoria (mínimo 3 caracteres).' : null,
            mb_strlen(Validador::texto($p['titulo_obtenido'] ?? '')) < 3 ? 'El título o programa es obligatorio (mínimo 3 caracteres).' : null,
            $enCurso ? null : Validador::fechaPasada($fecha, 'La fecha de expedición del título', true, '1950-01-01')
        );
        if (!$enCurso && $fecha !== '' && ($d = Validador::fechaValida($fecha)) && ($n = Validador::fechaValida($e['fecha_nacimiento'] ?? '')) && $d < $n) {
            $errores[] = 'La fecha de expedición no puede ser anterior a tu fecha de nacimiento.';
        }
        if ($errores) {
            Session::flash('error', implode(' · ', $errores));
            $this->ir('mi-hoja-de-vida/paso/4' . ($id ? '?editar_educacion=' . $id : ''));
        }

        $vals = [
            $p['nivel_educativo'], Validador::texto($p['institucion']), Validador::texto($p['titulo_obtenido']),
            $enCurso ? null : (int)substr($fecha, 0, 4), $enCurso ? null : $fecha, $enCurso ? 1 : 0,
        ];
        if ($id && DB::fetch("SELECT id FROM empleado_educacion WHERE id = ? AND empleado_id = ?", [$id, $e['id']])) {
            DB::execute("UPDATE empleado_educacion SET nivel_educativo=?, institucion=?, titulo_obtenido=?, anio_graduacion=?, fecha_expedicion=?, en_curso=? WHERE id=? AND empleado_id=?",
                array_merge($vals, [$id, $e['id']]));
            Session::flash('success', 'Formación académica actualizada.');
        } else {
            DB::insert("INSERT INTO empleado_educacion (nivel_educativo, institucion, titulo_obtenido, anio_graduacion, fecha_expedicion, en_curso, empleado_id) VALUES (?,?,?,?,?,?,?)",
                array_merge($vals, [$e['id']]));
            Session::flash('success', 'Formación académica agregada.');
        }
        $this->ir('mi-hoja-de-vida/paso/4');
    }

    // ── Formación complementaria (cursos, diplomados, certificaciones) ───

    public function guardarComplementaria(): void {
        $e = $this->empleado();
        $this->exigirEditable($e);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->ir('mi-hoja-de-vida/paso/5');

        $p = $_POST;
        $id = (int)($p['id'] ?? 0);
        $horas = trim($p['horas'] ?? '');
        $errores = Validador::errores(
            mb_strlen(Validador::texto($p['nombre'] ?? '')) < 3 ? 'El nombre del curso o certificación es obligatorio (mínimo 3 caracteres).' : null,
            mb_strlen(Validador::texto($p['institucion'] ?? '')) < 3 ? 'La institución es obligatoria (mínimo 3 caracteres).' : null,
            Validador::fechaPasada($p['fecha'] ?? '', 'La fecha de finalización', true, '1960-01-01'),
            ($horas !== '' && (!ctype_digit($horas) || (int)$horas < 1 || (int)$horas > 5000)) ? 'Las horas deben ser un número entero entre 1 y 5000.' : null
        );
        if ($errores) {
            Session::flash('error', implode(' · ', $errores));
            $this->ir('mi-hoja-de-vida/paso/5' . ($id ? '?editar_complementaria=' . $id : ''));
        }

        $vals = [Validador::texto($p['nombre']), Validador::texto($p['institucion']), $p['fecha'], $horas === '' ? null : (int)$horas];
        if ($id && DB::fetch("SELECT id FROM empleado_formacion_complementaria WHERE id = ? AND empleado_id = ?", [$id, $e['id']])) {
            DB::execute("UPDATE empleado_formacion_complementaria SET nombre=?, institucion=?, fecha=?, horas=? WHERE id=? AND empleado_id=?", array_merge($vals, [$id, $e['id']]));
            Session::flash('success', 'Formación complementaria actualizada.');
        } else {
            DB::insert("INSERT INTO empleado_formacion_complementaria (nombre, institucion, fecha, horas, empleado_id) VALUES (?,?,?,?,?)", array_merge($vals, [$e['id']]));
            Session::flash('success', 'Formación complementaria agregada.');
        }
        $this->ir('mi-hoja-de-vida/paso/5');
    }

    // ── Experiencia laboral ──────────────────────────────────────────────

    public function guardarExperiencia(): void {
        $e = $this->empleado();
        $this->exigirEditable($e);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->ir('mi-hoja-de-vida/paso/6');

        $p = $_POST;
        $id = (int)($p['id'] ?? 0);
        $actual = isset($p['trabajo_actual']);
        $ini = trim($p['fecha_inicio'] ?? '');
        $fin = trim($p['fecha_fin'] ?? '');

        $errores = Validador::errores(
            mb_strlen(Validador::texto($p['empresa'] ?? '')) < 2 ? 'La empresa es obligatoria.' : null,
            mb_strlen(Validador::texto($p['cargo'] ?? '')) < 2 ? 'El cargo es obligatorio.' : null,
            Validador::fechaPasada($ini, 'La fecha de inicio', true, '1960-01-01'),
            $actual ? null : Validador::fechaPasada($fin, 'La fecha de finalización', true, '1960-01-01'),
            mb_strlen(Validador::texto($p['funciones'] ?? '')) < 10 ? 'Describe tus funciones (mínimo 10 caracteres).' : null
        );
        if (!$errores && !$actual && $fin < $ini) $errores[] = 'La fecha de finalización no puede ser anterior a la de inicio.';
        if ($errores) {
            Session::flash('error', implode(' · ', $errores));
            $this->ir('mi-hoja-de-vida/paso/6' . ($id ? '?editar_experiencia=' . $id : ''));
        }

        $vals = [Validador::texto($p['empresa']), Validador::texto($p['cargo']), $ini, $actual ? null : $fin, trim($p['funciones'])];
        if ($id && DB::fetch("SELECT id FROM empleado_experiencia WHERE id = ? AND empleado_id = ?", [$id, $e['id']])) {
            DB::execute("UPDATE empleado_experiencia SET empresa=?, cargo=?, fecha_inicio=?, fecha_fin=?, funciones=? WHERE id=? AND empleado_id=?",
                array_merge($vals, [$id, $e['id']]));
            Session::flash('success', 'Experiencia laboral actualizada.');
        } else {
            DB::insert("INSERT INTO empleado_experiencia (empresa, cargo, fecha_inicio, fecha_fin, funciones, empleado_id) VALUES (?,?,?,?,?,?)",
                array_merge($vals, [$e['id']]));
            DB::execute("DELETE FROM empleado_hojavida_notas WHERE empleado_id = ? AND seccion = 'Experiencia laboral'", [$e['id']]);
            Session::flash('success', 'Experiencia laboral agregada.');
        }
        $this->ir('mi-hoja-de-vida/paso/6');
    }

    /** Marca (o desmarca) que el empleado aún no tiene experiencia laboral. */
    public function marcarSinExperiencia(): void {
        $e = $this->empleado();
        $this->exigirEditable($e);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->ir('mi-hoja-de-vida/paso/6');

        if ($this->experiencia((int)$e['id'])) {
            Session::flash('error', 'Ya tienes experiencia registrada; no puedes marcar que no tienes.');
        } elseif ($this->sinExperiencia((int)$e['id'])) {
            DB::execute("DELETE FROM empleado_hojavida_notas WHERE empleado_id = ? AND seccion = 'Experiencia laboral'", [$e['id']]);
        } else {
            DB::insert("INSERT INTO empleado_hojavida_notas (empleado_id, seccion, motivo) VALUES (?,?,?)", [$e['id'], 'Experiencia laboral', 'Sin experiencia laboral (primer empleo)']);
            Session::flash('success', 'Quedó registrado que aún no tienes experiencia laboral.');
        }
        $this->ir('mi-hoja-de-vida/paso/6');
    }

    // ── Envío y versiones ────────────────────────────────────────────────

    public function enviar(): void {
        $e = $this->empleado();
        $this->exigirEditable($e);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->ir('mi-hoja-de-vida/paso/7');

        $f = $this->faltantes($e, $this->educacion((int)$e['id']), $this->experiencia((int)$e['id']), $this->sinExperiencia((int)$e['id']));
        if ($f) {
            Session::flash('error', 'No puedes enviar la hoja de vida: aún tiene campos por completar. Revisa la lista.');
            $this->ir('mi-hoja-de-vida/paso/7');
        }

        DB::execute("
            UPDATE empleados SET hojavida_estado = 'enviada', hojavida_enviada_en = NOW(), hojavida_fecha = CURDATE(),
                   hojavida_observaciones = NULL, hojavida_respaldo = NULL
            WHERE id = ?
        ", [$e['id']]);
        Session::flash('success', 'Tu hoja de vida (versión ' . (int)$e['hojavida_version'] . ') fue enviada a Talento Humano para su revisión.');
        $this->ir('mi-hoja-de-vida/paso/7');
    }

    /** Reabre una hoja aprobada para actualizarla: sube la versión; al reenviarla se actualiza la fecha. */
    public function actualizar(): void {
        $e = $this->empleado();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($e['hojavida_estado'] ?? '') !== 'aprobada') $this->ir('mi-hoja-de-vida/paso/7');

        // Respaldo de la versión aprobada, por si el empleado cancela la modificación.
        $respaldo = ['campos' => []];
        foreach (self::CAMPOS_RESPALDO as $c) { $respaldo['campos'][$c] = $e[$c] ?? null; }
        foreach (self::TABLAS_RESPALDO as $t) {
            $respaldo[$t] = DB::fetchAll("SELECT * FROM $t WHERE empleado_id = ?", [$e['id']]);
        }
        DB::execute("UPDATE empleados SET hojavida_estado = 'borrador', hojavida_version = hojavida_version + 1, hojavida_observaciones = NULL, hojavida_respaldo = ? WHERE id = ?",
            [json_encode($respaldo, JSON_UNESCAPED_UNICODE), $e['id']]);
        Session::flash('success', 'Puedes actualizar tu hoja de vida. Al enviarla de nuevo quedará como la versión ' . ((int)$e['hojavida_version'] + 1) . ' con la fecha de envío.');
        $this->ir('mi-hoja-de-vida/paso/1');
    }

    /** Cancela una modificación en curso: vuelve a la versión aprobada anterior con sus datos. */
    public function cancelarActualizacion(): void {
        $e = $this->empleado();
        $r = json_decode((string)($e['hojavida_respaldo'] ?? ''), true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($e['hojavida_estado'] ?? '') !== 'borrador'
            || (int)$e['hojavida_version'] < 2) {
            Session::flash('error', 'No hay una modificación en curso que se pueda cancelar.');
            $this->ir('mi-hoja-de-vida/paso/7');
        }

        $pdo = DB::connect();
        $pdo->beginTransaction();
        try {
            // Si hay respaldo (la modificación se inició con él) se restauran los datos; si no, solo se vuelve a la versión aprobada.
            if (is_array($r)) {
            $set = []; $vals = [];
            foreach (self::CAMPOS_RESPALDO as $c) { $set[] = "$c = ?"; $vals[] = $r['campos'][$c] ?? null; }
            $vals[] = $e['id'];
            DB::execute("UPDATE empleados SET " . implode(', ', $set) . " WHERE id = ?", $vals);

            foreach (self::TABLAS_RESPALDO as $t) {
                DB::execute("DELETE FROM $t WHERE empleado_id = ?", [$e['id']]);
                foreach (($r[$t] ?? []) as $fila) {
                    $cols = array_keys($fila);
                    DB::execute(
                        "INSERT INTO $t (" . implode(',', array_map(fn($c) => "`$c`", $cols)) . ") VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ")",
                        array_values($fila)
                    );
                }
            }
            }
            DB::execute("UPDATE empleados SET hojavida_estado = 'aprobada', hojavida_version = hojavida_version - 1, hojavida_observaciones = NULL, hojavida_respaldo = NULL WHERE id = ?", [$e['id']]);
            $pdo->commit();
        } catch (\Throwable $ex) {
            $pdo->rollBack();
            error_log('cancelarActualizacion: ' . $ex->getMessage());
            Session::flash('error', 'No se pudo cancelar la modificación. Inténtalo de nuevo.');
            $this->ir('mi-hoja-de-vida/paso/7');
        }
        Session::flash('success', 'Cancelaste la modificación: tu hoja de vida volvió a la versión ' . ((int)$e['hojavida_version'] - 1) . ' aprobada.');
        $this->ir('mi-hoja-de-vida/paso/7');
    }

    // ── Revisión de Talento Humano ───────────────────────────────────────

    /** Admin / Gestor: aprueba la hoja de vida o la devuelve con observaciones (las fallas que debe corregir el empleado). */
    public function revisar(string $id): void {
        Auth::requireGestion();
        Schema::asegurarHojaVida();
        $volver = APP_URL . '/empleados/' . $id;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $volver); exit; }

        $e = DB::fetch("SELECT id, nombres, apellidos, hojavida_estado FROM empleados WHERE id = ?", [$id]);
        if (!$e || $e['hojavida_estado'] !== 'enviada') {
            Session::flash('error', 'Solo se puede revisar una hoja de vida que el empleado ya envió.');
            header('Location: ' . $volver);
            exit;
        }

        $accion = $_POST['accion'] ?? '';
        $obs = trim($_POST['observaciones'] ?? '');
        $quien = Auth::user()['nombre'] ?? 'Talento Humano';

        if ($accion === 'aprobar') {
            DB::execute("UPDATE empleados SET hojavida_estado='aprobada', hojavida_observaciones=NULL, hojavida_revisada_por=?, hojavida_revisada_en=NOW() WHERE id=?", [$quien, $id]);
            Session::flash('success', 'Hoja de vida de ' . $e['nombres'] . ' ' . $e['apellidos'] . ' aprobada.');
        } elseif ($accion === 'devolver') {
            if (mb_strlen($obs) < 10) {
                Session::flash('error', 'Para devolver la hoja de vida escribe qué debe corregir el empleado (mínimo 10 caracteres).');
            } else {
                DB::execute("UPDATE empleados SET hojavida_estado='devuelta', hojavida_observaciones=?, hojavida_revisada_por=?, hojavida_revisada_en=NOW() WHERE id=?", [$obs, $quien, $id]);
                Session::flash('success', 'Hoja de vida devuelta a ' . $e['nombres'] . '. Verá tus observaciones al ingresar a la plataforma.');
            }
        }
        header('Location: ' . $volver);
        exit;
    }
}
