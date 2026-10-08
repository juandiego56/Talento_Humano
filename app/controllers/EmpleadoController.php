<?php
require_once ROOT . '/app/helpers/Schema.php';
require_once ROOT . '/app/helpers/Validador.php';

class EmpleadoController {
    private const POR_PAGINA = 15;

    /** Un empleado solo puede consultar su propia información; Talento Humano y Dirección de Programa ven la de todos. */
    private function soloPropioSiEmpleado(string $id): void {
        if (Auth::esEmpleado() && Auth::empleadoId() !== (int)$id) {
            Session::flash('error', 'Solo puedes consultar tu propia información.');
            header('Location: ' . APP_URL . '/');
            exit;
        }
    }

    public function index(): void {
        Auth::requireAuth();
        Schema::asegurarHojaVida();

        // Un empleado raso no tiene acceso al listado de personal: va a su propia ficha.
        if (Auth::esEmpleado()) {
            header('Location: ' . APP_URL . '/empleados/' . (int)Auth::empleadoId());
            exit;
        }

        $q       = trim($_GET['q'] ?? '');
        $estado  = $_GET['estado'] ?? '';
        $area    = $_GET['area'] ?? '';
        // Un Director de Programa solo puede ver el personal de su propio programa académico.
        $programa = Auth::esDirectorPrograma() ? (string)(Auth::programaId() ?? '0') : ($_GET['programa'] ?? '');

        $where  = [];
        $params = [];
        if ($q !== '') {
            $where[] = '(e.nombres LIKE ? OR e.apellidos LIKE ? OR e.numero_documento LIKE ? OR CONCAT(e.nombres, " ", e.apellidos) LIKE ?)';
            $like = "%$q%";
            array_push($params, $like, $like, $like, $like);
        }
        if ($estado !== '')   { $where[] = 'e.estado = ?'; $params[] = $estado; }
        if ($area !== '')     { $where[] = 'e.area_id = ?'; $params[] = $area; }
        if ($programa !== '') { $where[] = 'e.programa_id = ?'; $params[] = $programa; }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $total = (int)(DB::fetch("SELECT COUNT(*) AS t FROM empleados e $whereSql", $params)['t'] ?? 0);
        [$pagina, $paginas, $offset] = View::paginar($total, self::POR_PAGINA, $_GET['pagina'] ?? 1);

        $empleados = DB::fetchAll("
            SELECT e.*, c.nombre AS cargo, a.nombre AS area, p.nombre AS programa,
                   COALESCE(vc.pct_completitud,0) AS pct_checklist
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            LEFT JOIN programas_academicos p ON p.id = e.programa_id
            LEFT JOIN v_checklist_completitud vc ON vc.empleado_id = e.id
            $whereSql
            ORDER BY e.apellidos, e.nombres
            LIMIT " . self::POR_PAGINA . " OFFSET " . (int)$offset . "
        ", $params);

        $areas     = DB::fetchAll("SELECT * FROM areas ORDER BY nombre");
        $programas = DB::fetchAll("SELECT * FROM programas_academicos WHERE activo = 1 ORDER BY nombre");

        View::render('empleados.index', [
            'titulo'      => 'Administración de Personal',
            'empleados'   => $empleados,
            'areas'       => $areas,
            'programas'   => $programas,
            'q'           => $q,
            'estado'      => $estado,
            'areaSel'     => $area,
            'programaSel' => $programa,
            'total'       => $total,
            'pagina'      => $pagina,
            'paginas'     => $paginas,
            'porPagina'   => self::POR_PAGINA,
        ]);
    }

    public function crear(): void {
        Auth::requireGestion();
        $cargos    = DB::fetchAll("SELECT * FROM cargos ORDER BY nombre");
        $areas     = DB::fetchAll("SELECT * FROM areas ORDER BY nombre");
        $programas = DB::fetchAll("SELECT * FROM programas_academicos WHERE activo = 1 ORDER BY nombre");
        $escalafon = DB::fetchAll("SELECT cargo_id, nivel_educativo, salario_tiempo_completo, salario_medio_tiempo FROM escalafon_salarial");
        $nivelesArl = DB::fetchAll("SELECT * FROM arl_niveles_riesgo ORDER BY nivel");

        // Si el guardado falló por una validación, se conservan los datos escritos.
        $old = Session::get('old_empleado') ?: [];
        Session::set('old_empleado', null);

        View::render('empleados.crear', [
            'titulo'     => 'Nuevo Empleado',
            'cargos'     => $cargos,
            'areas'      => $areas,
            'programas'  => $programas,
            'escalafon'  => $escalafon,
            'nivelesArl' => $nivelesArl,
            'old'        => $old,
        ]);
    }

    /** Valida los datos que define Talento Humano (identificación, acceso y laborales). */
    private function validarDatosGestion(array $p): array {
        return Validador::errores(
            Validador::enLista($p['tipo_documento'] ?? '', ['CC', 'CE', 'TI', 'PA'], 'El tipo de documento'),
            Validador::documento($p['numero_documento'] ?? ''),
            Validador::nombrePersona($p['nombres'] ?? '', 'Los nombres'),
            Validador::nombrePersona($p['apellidos'] ?? '', 'Los apellidos'),
            Validador::email($p['email'] ?? ''),
            Validador::fechaIngreso($p['fecha_ingreso'] ?? ''),
            Validador::enLista($p['tipo_contrato'] ?? '', ['termino_fijo', 'termino_indefinido', 'obra_labor', 'prestacion_servicios'], 'El tipo de contrato'),
            Validador::salario($p['salario_base'] ?? '')
        );
    }

    public function guardar(): void {
        Auth::requireGestion();
        Schema::asegurarHojaVida();

        $p = $_POST;
        $errores = $this->validarDatosGestion($p);

        if (!$errores) {
            $email = trim($p['email']);
            if (DB::fetch("SELECT id FROM empleados WHERE numero_documento = ?", [trim($p['numero_documento'])])) {
                $errores[] = 'Ya existe un empleado con ese número de documento.';
            }
            if (DB::fetch("SELECT id FROM usuarios WHERE email = ?", [$email])) {
                $errores[] = 'Ya existe un usuario con ese correo; usa otro correo para el empleado.';
            }
        }

        if ($errores) {
            Session::set('old_empleado', $p);
            Session::flash('error', implode(' · ', $errores));
            header('Location: ' . APP_URL . '/empleados/crear');
            exit;
        }

        $pdo = DB::connect();
        try {
            $pdo->beginTransaction();
            $id = DB::insert("
                INSERT INTO empleados
                  (tipo_documento, numero_documento, nombres, apellidos, email, cargo_id, area_id, programa_id,
                   fecha_ingreso, tipo_contrato, salario_base, nivel_educativo, tipo_vinculacion_docente,
                   arl_nivel_riesgo, estado, hojavida_estado)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
                $p['tipo_documento'], trim($p['numero_documento']), Validador::texto($p['nombres']), Validador::texto($p['apellidos']),
                trim($p['email']),
                ($p['cargo_id'] ?? '') ?: null, ($p['area_id'] ?? '') ?: null, ($p['programa_id'] ?? '') ?: null,
                $p['fecha_ingreso'], $p['tipo_contrato'], Validador::dinero($p['salario_base']),
                ($p['nivel_educativo'] ?? '') ?: null, ($p['tipo_vinculacion_docente'] ?? '') ?: null,
                max(1, min(5, (int)($p['arl_nivel_riesgo'] ?? 1))), 'activo', 'borrador',
            ]);

            // Inicializa lista de chequeo con todos los documentos activos
            DB::execute("
                INSERT INTO empleado_documentos (empleado_id, documento_id, entregado)
                SELECT ?, id, 0 FROM documentos_requeridos WHERE activo = 1
            ", [$id]);

            // Crea el usuario del empleado: entra con su correo y, la primera vez, con su número de documento como contraseña.
            DB::insert("
                INSERT INTO usuarios (nombre, email, password_hash, rol_id, empleado_id, activo, debe_cambiar_password)
                VALUES (?,?,?,?,?,1,1)
            ", [
                Validador::texto($p['nombres']) . ' ' . Validador::texto($p['apellidos']), trim($p['email']),
                password_hash(trim($p['numero_documento']), PASSWORD_DEFAULT), ROL_EMPLEADO, $id,
            ]);
            $pdo->commit();

            Session::flash('success', 'Empleado registrado. Se creó su usuario: ingresa con el correo ' . trim($p['email'])
                . ' y, como contraseña inicial, su número de documento. Desde ahí diligencia su hoja de vida.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('EmpleadoController::guardar: ' . $e->getMessage());
            Session::set('old_empleado', $p);
            Session::flash('error', str_contains($e->getMessage(), 'Duplicate')
                ? 'Ya existe un empleado o usuario con ese documento o correo.'
                : 'Error al guardar el empleado. Intenta de nuevo.');
            header('Location: ' . APP_URL . '/empleados/crear');
        }
        exit;
    }

    public function ver(string $id): void {
        Auth::requireAuth();
        Schema::asegurarHojaVida();
        $this->soloPropioSiEmpleado($id);
        $empleado = $this->obtenerEmpleado($id);

        $educacion   = DB::fetchAll("SELECT * FROM empleado_educacion WHERE empleado_id = ? ORDER BY anio_graduacion DESC", [$id]);
        $experiencia = DB::fetchAll("SELECT * FROM empleado_experiencia WHERE empleado_id = ? ORDER BY fecha_inicio DESC", [$id]);
        $checklist   = DB::fetch("SELECT * FROM v_checklist_completitud WHERE empleado_id = ?", [$id]);
        $historialNomina = DB::fetchAll("
            SELECT nd.*, n.periodo, n.estado AS estado_nomina
            FROM nomina_detalle nd JOIN nominas n ON n.id = nd.nomina_id
            WHERE nd.empleado_id = ? ORDER BY n.periodo DESC LIMIT 6
        ", [$id]);
        $actividades = DB::fetchAll("
            SELECT ab.nombre, ab.fecha_inicio, ab.tipo, bi.asistio
            FROM bienestar_inscripciones bi JOIN actividades_bienestar ab ON ab.id = bi.actividad_id
            WHERE bi.empleado_id = ? ORDER BY ab.fecha_inicio DESC
        ", [$id]);
        $notasHojaVida = DB::fetchAll("
            SELECT seccion, motivo FROM empleado_hojavida_notas WHERE empleado_id = ? ORDER BY seccion
        ", [$id]);

        View::render('empleados.ver', [
            'titulo'          => 'Ficha de empleado',
            'empleado'        => $empleado,
            'educacion'       => $educacion,
            'experiencia'     => $experiencia,
            'checklist'       => $checklist,
            'historialNomina' => $historialNomina,
            'actividades'     => $actividades,
            'notasHojaVida'   => $notasHojaVida,
            'complementaria'  => DB::fetchAll("SELECT * FROM empleado_formacion_complementaria WHERE empleado_id = ? ORDER BY fecha DESC", [$id]),
            'usuario'         => DB::fetch("SELECT id, email, activo FROM usuarios WHERE empleado_id = ? AND rol_id = ?", [$id, ROL_EMPLEADO]),
        ]);
    }

    public function editar(string $id): void {
        Auth::requireGestion();
        $empleado  = $this->obtenerEmpleado($id);
        $cargos    = DB::fetchAll("SELECT * FROM cargos ORDER BY nombre");
        $areas     = DB::fetchAll("SELECT * FROM areas ORDER BY nombre");
        $programas = DB::fetchAll("SELECT * FROM programas_academicos WHERE activo = 1 ORDER BY nombre");
        $escalafon = DB::fetchAll("SELECT cargo_id, nivel_educativo, salario_tiempo_completo, salario_medio_tiempo FROM escalafon_salarial");
        $nivelesArl = DB::fetchAll("SELECT * FROM arl_niveles_riesgo ORDER BY nivel");
        View::render('empleados.editar', [
            'titulo'     => 'Editar Empleado',
            'empleado'   => $empleado,
            'cargos'     => $cargos,
            'areas'      => $areas,
            'programas'  => $programas,
            'escalafon'  => $escalafon,
            'nivelesArl' => $nivelesArl,
        ]);
    }

    /** Campos que diligencia el empleado en su hoja de vida: una vez llenos, Talento Humano ya no los modifica. */
    private const CAMPOS_EMPLEADO = [
        'fecha_nacimiento', 'genero', 'estado_civil', 'tipo_sangre', 'direccion', 'telefono',
        'contacto_emergencia_nombre', 'contacto_emergencia_telefono', 'eps', 'fondo_pension', 'arl',
        'banco', 'tipo_cuenta', 'numero_cuenta',
    ];

    public function actualizar(string $id): void {
        Auth::requireGestion();
        Schema::asegurarHojaVida();
        $actual = $this->obtenerEmpleado($id);
        $p = $_POST;
        $volver = APP_URL . '/empleados/' . $id . '/editar';

        $errores = $this->validarDatosGestion($p);

        // Datos del empleado: los ya diligenciados se conservan; solo se aceptan los que estaban vacíos.
        $p['fondo_pension'] = Validador::fondoPension($p);
        $datos = [];
        foreach (self::CAMPOS_EMPLEADO as $c) {
            $yaLleno = trim((string)($actual[$c] ?? '')) !== '';
            $nuevo   = Validador::texto($p[$c] ?? '');
            $datos[$c] = $yaLleno ? $actual[$c] : ($nuevo !== '' ? $nuevo : null);
            if (!$yaLleno && $nuevo !== '') {
                $err = match ($c) {
                    'fecha_nacimiento'             => Validador::fechaNacimiento($nuevo),
                    'genero'                       => Validador::enLista($nuevo, ['M', 'F', 'Otro'], 'El género'),
                    'estado_civil'                 => Validador::enLista($nuevo, Validador::ESTADOS_CIVILES, 'El estado civil'),
                    'tipo_sangre'                  => Validador::enLista($nuevo, Validador::TIPOS_SANGRE, 'El tipo de sangre'),
                    'direccion'                    => Validador::direccion($nuevo),
                    'telefono'                     => Validador::telefono($nuevo),
                    'contacto_emergencia_nombre'   => Validador::nombrePersona($nuevo, 'El nombre del contacto de emergencia'),
                    'contacto_emergencia_telefono' => Validador::telefono($nuevo, 'El teléfono del contacto de emergencia'),
                    'tipo_cuenta'                  => Validador::enLista($nuevo, ['ahorros', 'corriente'], 'El tipo de cuenta'),
                    'numero_cuenta'                => Validador::cuenta($nuevo),
                    default                        => null,
                };
                if ($err) $errores[] = $err;
            }
        }

        if (!$errores) {
            if (DB::fetch("SELECT id FROM empleados WHERE numero_documento = ? AND id <> ?", [trim($p['numero_documento']), $id])) {
                $errores[] = 'Ya existe otro empleado con ese número de documento.';
            }
            if (DB::fetch("SELECT id FROM usuarios WHERE email = ? AND (empleado_id IS NULL OR empleado_id <> ?)", [trim($p['email']), $id])) {
                $errores[] = 'Ese correo ya lo usa otro usuario del sistema.';
            }
        }
        if ($errores) {
            Session::flash('error', implode(' · ', $errores));
            header('Location: ' . $volver);
            exit;
        }

        $estado = in_array($p['estado'] ?? '', ['activo', 'inactivo', 'retirado'], true) ? $p['estado'] : 'activo';
        $fechaRetiro = match ($estado) {
            'retirado' => (($p['fecha_retiro'] ?? '') ?: ($actual['fecha_retiro'] ?: date('Y-m-d'))),
            'inactivo' => $actual['fecha_retiro'],
            default    => null,
        };

        $cols = [
            'tipo_documento' => $p['tipo_documento'], 'numero_documento' => trim($p['numero_documento']),
            'nombres' => Validador::texto($p['nombres']), 'apellidos' => Validador::texto($p['apellidos']), 'email' => trim($p['email']),
            'cargo_id' => ($p['cargo_id'] ?? '') ?: null, 'area_id' => ($p['area_id'] ?? '') ?: null,
            'programa_id' => ($p['programa_id'] ?? '') ?: null, 'fecha_ingreso' => $p['fecha_ingreso'],
            'tipo_contrato' => $p['tipo_contrato'], 'salario_base' => Validador::dinero($p['salario_base']),
            'nivel_educativo' => ($p['nivel_educativo'] ?? '') ?: null,
            'tipo_vinculacion_docente' => ($p['tipo_vinculacion_docente'] ?? '') ?: null,
            'arl_nivel_riesgo' => max(1, min(5, (int)($p['arl_nivel_riesgo'] ?? 1))),
            'estado' => $estado, 'fecha_retiro' => $fechaRetiro,
        ] + $datos;

        $set = implode(', ', array_map(fn($c) => "$c = ?", array_keys($cols)));
        try {
            DB::execute("UPDATE empleados SET $set WHERE id = ?", array_merge(array_values($cols), [$id]));
        } catch (\PDOException $e) {
            error_log('EmpleadoController::actualizar: ' . $e->getMessage());
            Session::flash('error', str_contains($e->getMessage(), 'Duplicate')
                ? 'Ya existe otro empleado con ese número de documento.'
                : 'Error al guardar los cambios. Intenta de nuevo.');
            header('Location: ' . $volver);
            exit;
        }

        // El usuario del empleado sigue a su ficha: nombre, correo y acceso (solo si está activo).
        DB::execute("UPDATE usuarios SET nombre = ?, email = ?, activo = ? WHERE empleado_id = ? AND rol_id = ?", [
            $cols['nombres'] . ' ' . $cols['apellidos'], $cols['email'], $estado === 'activo' ? 1 : 0, $id, ROL_EMPLEADO,
        ]);

        Session::flash('success', 'Empleado actualizado correctamente.');
        header('Location: ' . APP_URL . '/empleados/' . $id);
        exit;
    }

    /** Cambia rápido el estado de un empleado entre activo e inactivo (botón de la lista de empleados). */
    public function cambiarEstado(string $id): void {
        Auth::requireGestion();

        // Vuelve a la lista conservando los filtros que tenía, sin aceptar destinos externos.
        $volver = APP_URL . '/empleados';
        $ref    = parse_url($_SERVER['HTTP_REFERER'] ?? '');
        if (($ref['path'] ?? '') === parse_url($volver, PHP_URL_PATH) && !empty($ref['query'])) {
            $volver .= '?' . $ref['query'];
        }

        $nuevo = $_POST['estado'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !in_array($nuevo, ['activo', 'inactivo'], true)) {
            header('Location: ' . $volver);
            exit;
        }

        $empleado = $this->obtenerEmpleado($id);
        if ($empleado['estado'] === 'retirado') {
            Session::flash('error', 'Un empleado retirado no se activa ni se inactiva desde la lista. Edita su ficha para cambiar su estado.');
        } else {
            DB::execute("UPDATE empleados SET estado = ? WHERE id = ?", [$nuevo, $id]);
            DB::execute("UPDATE usuarios SET activo = ? WHERE empleado_id = ? AND rol_id = ?", [$nuevo === 'activo' ? 1 : 0, $id, ROL_EMPLEADO]);
            Session::flash('success', $empleado['nombres'] . ' ' . $empleado['apellidos'] . ($nuevo === 'inactivo' ? ' quedó inactivo.' : ' quedó activo.'));
        }
        header('Location: ' . $volver);
        exit;
    }

    /** "Eliminar" un empleado = darlo de baja: no se borra nada; queda inactivo, con fecha de retiro, y su usuario ya no puede ingresar. */
    public function eliminar(string $id): void {
        Auth::requireGestion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }
        $empleado = $this->obtenerEmpleado($id);
        DB::execute("UPDATE empleados SET estado = 'inactivo', fecha_retiro = CURDATE() WHERE id = ?", [$id]);
        DB::execute("UPDATE usuarios SET activo = 0 WHERE empleado_id = ? AND rol_id = ?", [$id, ROL_EMPLEADO]);
        Session::flash('success', $empleado['nombres'] . ' ' . $empleado['apellidos'] . ' fue dado de baja: quedó inactivo y su usuario ya no puede ingresar. Su historial se conserva.');
        header('Location: ' . APP_URL . '/empleados');
        exit;
    }

    /** Crea el usuario de un empleado que aún no tiene (correo + número de documento como contraseña inicial). */
    public function crearUsuario(string $id): void {
        Auth::requireGestion();
        $empleado = $this->obtenerEmpleado($id);
        $volver = APP_URL . '/empleados/' . $id;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ' . $volver); exit; }

        if (DB::fetch("SELECT id FROM usuarios WHERE empleado_id = ?", [$id])) {
            Session::flash('error', 'Este empleado ya tiene un usuario.');
        } elseif (Validador::email($empleado['email'] ?? '')) {
            Session::flash('error', 'Primero registra un correo válido para el empleado (Editar → Identificación).');
        } elseif (DB::fetch("SELECT id FROM usuarios WHERE email = ?", [$empleado['email']])) {
            Session::flash('error', 'Ese correo ya lo usa otro usuario del sistema.');
        } else {
            DB::insert("
                INSERT INTO usuarios (nombre, email, password_hash, rol_id, empleado_id, activo, debe_cambiar_password)
                VALUES (?,?,?,?,?,?,1)
            ", [
                $empleado['nombres'] . ' ' . $empleado['apellidos'], $empleado['email'],
                password_hash($empleado['numero_documento'], PASSWORD_DEFAULT), ROL_EMPLEADO, $id,
                $empleado['estado'] === 'activo' ? 1 : 0,
            ]);
            Session::flash('success', 'Usuario creado: ingresa con ' . $empleado['email'] . ' y, como contraseña inicial, su número de documento.');
        }
        header('Location: ' . $volver);
        exit;
    }

    // ── Hoja de vida ─────────────────────────────────────────────────────

    public function hojaVida(string $id): void {
        Auth::requireAuth();
        Schema::asegurarHojaVida();
        $this->soloPropioSiEmpleado($id);
        $empleado    = $this->obtenerEmpleado($id);
        $educacion   = DB::fetchAll("SELECT * FROM empleado_educacion WHERE empleado_id = ? ORDER BY anio_graduacion", [$id]);
        $experiencia = DB::fetchAll("SELECT * FROM empleado_experiencia WHERE empleado_id = ? ORDER BY fecha_inicio", [$id]);

        View::render('empleados.hojavida', [
            'titulo'      => 'Hoja de Vida — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado'    => $empleado,
            'educacion'   => $educacion,
            'experiencia' => $experiencia,
            'complementaria' => DB::fetchAll("SELECT * FROM empleado_formacion_complementaria WHERE empleado_id = ? ORDER BY fecha DESC", [$id]),
        ], layout: 'imprimible');
    }

    /** La formación y la experiencia las diligencia el empleado en su hoja de vida; Talento Humano solo las revisa. */
    private function soloLecturaHojaVida(string $id): void {
        Auth::requireGestion();
        Session::flash('error', 'La formación académica y la experiencia las diligencia el empleado en su hoja de vida. Talento Humano solo las revisa y las devuelve con observaciones si hace falta.');
        header('Location: ' . APP_URL . '/empleados/' . $id);
        exit;
    }

    public function guardarEducacion(string $id): void { $this->soloLecturaHojaVida($id); }

    public function eliminarEducacion(string $id, string $eduId): void { $this->soloLecturaHojaVida($id); }

    public function toggleConvalidacion(string $id): void {
        Auth::requireGestion();
        DB::execute(
            "UPDATE empleados SET formacion_convalidada = IF(formacion_convalidada = 1, 0, 1) WHERE id = ?",
            [$id]
        );
        header('Location: ' . APP_URL . '/empleados/' . $id);
        exit;
    }

    public function guardarExperiencia(string $id): void { $this->soloLecturaHojaVida($id); }

    public function eliminarExperiencia(string $id, string $expId): void { $this->soloLecturaHojaVida($id); }

    // ── Lista de chequeo ─────────────────────────────────────────────────

    public function checklist(string $id): void {
        Auth::requireAuth();
        $this->soloPropioSiEmpleado($id);
        $empleado = $this->obtenerEmpleado($id);

        // Los 5 documentos del proceso interno de selección/contratación (Requerimiento de
        // Personal, Entrevista, Prueba Psicotécnica, Examen Médico, Contrato Laboral) no los
        // sube el empleado: solo Admin/Gestor los ven en esta lista.
        $soloVisiblesEmpleado = !Auth::puedeGestionar();

        $items = DB::fetchAll("
            SELECT dr.id AS documento_id, dr.codigo, dr.nombre, dr.descripcion, dr.obligatorio, dr.visible_empleado,
                   COALESCE(ed.entregado,0) AS entregado, ed.fecha_entrega, ed.observaciones,
                   ed.archivo_path, ed.archivo_nombre_original, ed.archivo_fecha_subida, ed.peso_kb
            FROM documentos_requeridos dr
            LEFT JOIN empleado_documentos ed ON ed.documento_id = dr.id AND ed.empleado_id = ?
            WHERE dr.activo = 1" . ($soloVisiblesEmpleado ? " AND dr.visible_empleado = 1" : "") . "
            ORDER BY dr.orden, dr.nombre
        ", [$id]);

        $firma = DB::fetch("SELECT * FROM empleado_checklist_firma WHERE empleado_id = ?", [$id]);

        View::render('empleados.checklist', [
            'titulo'   => 'Lista de Chequeo — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado' => $empleado,
            'items'    => $items,
            'firma'    => $firma,
        ]);
    }

    public function actualizarChecklist(string $id): void {
        Auth::requireGestion();
        $documentoId = $_POST['documento_id'];
        $accion      = $_POST['accion'] ?? 'toggle';

        $actual = DB::fetch("
            SELECT entregado, fecha_entrega, observaciones, archivo_path, archivo_nombre_original, peso_kb
            FROM empleado_documentos
            WHERE empleado_id = ? AND documento_id = ?
        ", [$id, $documentoId]);

        $archivoPath  = $actual['archivo_path'] ?? null;
        $archivoNom   = $actual['archivo_nombre_original'] ?? null;
        $archivoPeso  = $actual['peso_kb'] ?? null;
        $archivoFecha = null;
        $nuevoArchivo = false; // si se reemplaza el soporte, vuelve a quedar "pendiente" de verificación

        if ($accion === 'observacion') {
            // Solo actualiza la observación, conserva el estado de entrega actual
            $entregado     = (int)($actual['entregado'] ?? 0);
            $fechaEntrega  = $actual['fecha_entrega'] ?? null;
            $observaciones = ($_POST['observaciones'] ?? '') ?: null;
        } elseif ($accion === 'archivo') {
            // Sube el soporte adjunto de este ítem; conserva entrega/observación actuales
            $entregado     = (int)($actual['entregado'] ?? 0);
            $fechaEntrega  = $actual['fecha_entrega'] ?? null;
            $observaciones = $actual['observaciones'] ?? null;

            $subido = $this->procesarArchivoChecklist($id, $documentoId);
            if ($subido === false) {
                header('Location: ' . APP_URL . '/empleados/' . $id . '/checklist');
                exit;
            }
            if ($subido !== null) {
                // Si se sube el soporte, el ítem queda marcado como entregado automáticamente
                [$archivoPath, $archivoNom, $archivoPeso] = $subido;
                $archivoFecha = date('Y-m-d H:i:s');
                $entregado    = 1;
                $fechaEntrega = $fechaEntrega ?? date('Y-m-d');
                $nuevoArchivo = true;
            }
        } else {
            // Marca/desmarca la entrega, conserva la observación actual
            $entregado     = isset($_POST['entregado']) ? 1 : 0;
            $fechaEntrega  = $entregado ? ($actual['fecha_entrega'] ?? date('Y-m-d')) : null;
            $observaciones = $actual['observaciones'] ?? null;
        }

        $estadoVerificacion = $nuevoArchivo ? 'pendiente' : null; // null = no tocar el estado actual

        DB::execute("
            INSERT INTO empleado_documentos
              (empleado_id, documento_id, entregado, fecha_entrega, observaciones,
               archivo_path, archivo_nombre_original, archivo_fecha_subida, peso_kb, estado_verificacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, COALESCE(?, 'pendiente'))
            ON DUPLICATE KEY UPDATE entregado = VALUES(entregado),
                                    fecha_entrega = VALUES(fecha_entrega),
                                    observaciones = VALUES(observaciones),
                                    archivo_path = VALUES(archivo_path),
                                    archivo_nombre_original = VALUES(archivo_nombre_original),
                                    archivo_fecha_subida = COALESCE(VALUES(archivo_fecha_subida), archivo_fecha_subida),
                                    peso_kb = VALUES(peso_kb),
                                    estado_verificacion = COALESCE(?, estado_verificacion)
        ", [$id, $documentoId, $entregado, $fechaEntrega, $observaciones, $archivoPath, $archivoNom, $archivoFecha, $archivoPeso, $estadoVerificacion, $estadoVerificacion]);

        Session::flash('success', $accion === 'archivo' ? 'Soporte adjuntado correctamente.' : 'Lista de chequeo actualizada.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/checklist');
        exit;
    }

    /** Recibe y valida el archivo subido para un ítem del checklist. Devuelve [path, nombreOriginal, pesoKb], null si no había archivo, o false si hubo error (ya deja el flash). */
    private function procesarArchivoChecklist(string $empleadoId, string $documentoId): array|null|false {
        if (empty($_FILES['archivo']['name'])) return null;

        $file = $_FILES['archivo'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No se pudo subir el archivo. Intenta nuevamente.');
            return false;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf' || !in_array($ext, CHECKLIST_EXT_PERMITIDAS, true)) {
            Session::flash('error', 'Formato no permitido. Solo se aceptan archivos en PDF.');
            return false;
        }
        // Rechaza de una vez los archivos exageradamente grandes (aunque se intente comprimir,
        // no vale la pena procesar algo que de entrada triplica el máximo permitido).
        if ($file['size'] > CHECKLIST_MAX_SIZE * 3) {
            $maxMb = round(CHECKLIST_MAX_SIZE / 1024 / 1024, 1);
            Session::flash('error', "El archivo pesa " . round($file['size'] / 1024 / 1024, 1) . " MB. El máximo permitido es {$maxMb} MB.");
            return false;
        }

        $dir = CHECKLIST_UPLOAD_DIR . '/' . $empleadoId;
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $nombreArchivo = $documentoId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $rutaCompleta  = $dir . '/' . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            Session::flash('error', 'Error al guardar el archivo en el servidor.');
            return false;
        }

        // Intenta comprimir el PDF (requiere Ghostscript instalado en el servidor; si no
        // está disponible, simplemente se conserva el archivo original sin comprimir).
        $this->comprimirPdf($rutaCompleta);

        // Verifica el tamaño DESPUÉS de comprimir, para no rechazar archivos que sí caben
        // una vez comprimidos.
        $pesoBytes = filesize($rutaCompleta);
        if ($pesoBytes > CHECKLIST_MAX_SIZE) {
            @unlink($rutaCompleta);
            $maxMb = round(CHECKLIST_MAX_SIZE / 1024 / 1024, 1);
            Session::flash('error', "El archivo pesa " . round($pesoBytes / 1024 / 1024, 1) . " MB, incluso comprimido supera el máximo permitido ({$maxMb} MB).");
            return false;
        }

        // Ruta relativa guardada en BD (relativa a CHECKLIST_UPLOAD_DIR)
        return [$empleadoId . '/' . $nombreArchivo, $file['name'], (int)round($pesoBytes / 1024)];
    }

    /** Comprime un PDF in-place usando Ghostscript si está instalado en el servidor (best-effort, silencioso). */
    private function comprimirPdf(string $rutaCompleta): void {
        $gsBin = null;
        $esWindows = stripos(PHP_OS, 'WIN') === 0;
        foreach (($esWindows ? ['gswin64c', 'gswin32c'] : ['gs']) as $bin) {
            $chequeo = @shell_exec(($esWindows ? 'where ' : 'command -v ') . escapeshellarg($bin) . ' 2>' . ($esWindows ? 'NUL' : '/dev/null'));
            if ($chequeo && trim($chequeo) !== '') { $gsBin = $bin; break; }
        }
        if (!$gsBin) return; // Ghostscript no instalado: se deja el archivo tal cual

        $tmp = $rutaCompleta . '.tmp.pdf';
        $cmd = escapeshellarg($gsBin)
             . ' -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook'
             . ' -dNOPAUSE -dQUIET -dBATCH -sOutputFile=' . escapeshellarg($tmp) . ' ' . escapeshellarg($rutaCompleta);
        @shell_exec($cmd);

        if (is_file($tmp) && filesize($tmp) > 0 && filesize($tmp) < filesize($rutaCompleta)) {
            @rename($tmp, $rutaCompleta); // solo reemplaza si de verdad quedó más liviano
        } elseif (is_file($tmp)) {
            @unlink($tmp);
        }
    }

    public function verArchivoChecklist(string $id, string $documentoId): void {
        Auth::requireAuth();
        $reg = DB::fetch("
            SELECT archivo_path, archivo_nombre_original FROM empleado_documentos
            WHERE empleado_id = ? AND documento_id = ?
        ", [$id, $documentoId]);

        if (!$reg || !$reg['archivo_path']) { http_response_code(404); echo 'Archivo no encontrado.'; exit; }

        $rutaCompleta = CHECKLIST_UPLOAD_DIR . '/' . $reg['archivo_path'];
        if (!is_file($rutaCompleta)) { http_response_code(404); echo 'Archivo no encontrado.'; exit; }

        $ext  = strtolower(pathinfo($rutaCompleta, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf'          => 'application/pdf',
            'jpg', 'jpeg'  => 'image/jpeg',
            'png'          => 'image/png',
            'doc'          => 'application/msword',
            'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default        => 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($reg['archivo_nombre_original']) . '"');
        header('Content-Length: ' . filesize($rutaCompleta));
        readfile($rutaCompleta);
        exit;
    }

    public function eliminarArchivoChecklist(string $id, string $documentoId): void {
        Auth::requireGestion();
        $reg = DB::fetch("
            SELECT archivo_path FROM empleado_documentos WHERE empleado_id = ? AND documento_id = ?
        ", [$id, $documentoId]);

        if ($reg && $reg['archivo_path']) {
            $rutaCompleta = CHECKLIST_UPLOAD_DIR . '/' . $reg['archivo_path'];
            if (is_file($rutaCompleta)) @unlink($rutaCompleta);
        }

        DB::execute("
            UPDATE empleado_documentos
            SET archivo_path = NULL, archivo_nombre_original = NULL, archivo_fecha_subida = NULL
            WHERE empleado_id = ? AND documento_id = ?
        ", [$id, $documentoId]);

        Session::flash('success', 'Soporte eliminado.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/checklist');
        exit;
    }

    /** Solo el propio empleado (su cuenta) o Admin/Gestor pueden cambiar la foto. */
    private function puedeEditarFoto(string $empleadoId): bool {
        if (Auth::puedeGestionar()) return true;
        return (int)(Auth::user()['empleado_id'] ?? 0) === (int)$empleadoId;
    }

    public function subirFoto(string $id): void {
        Auth::requireAuth();
        if (!$this->puedeEditarFoto($id)) {
            Session::flash('error', 'No tienes permiso para cambiar esta foto.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }
        $this->obtenerEmpleado($id); // valida que exista

        if (empty($_FILES['foto']['name'])) {
            Session::flash('error', 'No se seleccionó ninguna imagen.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }

        $file = $_FILES['foto'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No se pudo subir la imagen. Intenta nuevamente.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }
        if ($file['size'] > FOTO_MAX_SIZE) {
            Session::flash('error', 'La imagen supera el tamaño máximo permitido (2 MB).');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, FOTO_EXT_PERMITIDAS, true)) {
            Session::flash('error', 'Formato no permitido. Usa JPG, PNG o WEBP.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }

        if (!is_dir(FOTO_UPLOAD_DIR)) mkdir(FOTO_UPLOAD_DIR, 0775, true);

        // Borra la foto anterior si existía
        $anterior = DB::fetch("SELECT foto_path FROM empleados WHERE id = ?", [$id]);
        if ($anterior && $anterior['foto_path']) {
            $rutaAnterior = FOTO_UPLOAD_DIR . '/' . $anterior['foto_path'];
            if (is_file($rutaAnterior)) @unlink($rutaAnterior);
        }

        $nombreArchivo = 'empleado_' . $id . '_' . time() . '.' . $ext;
        $rutaCompleta  = FOTO_UPLOAD_DIR . '/' . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            Session::flash('error', 'Error al guardar la imagen en el servidor.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }

        DB::execute("UPDATE empleados SET foto_path = ? WHERE id = ?", [$nombreArchivo, $id]);

        $fondoBlanco = ImagenHelper::pareceTenerFondoBlanco($rutaCompleta);
        if ($fondoBlanco === false) {
            Session::flash('warning', 'Foto guardada, pero el fondo no parece blanco/claro. Recuerda que debe ser tipo selfie con fondo blanco.');
        } else {
            Session::flash('success', 'Foto de perfil actualizada.');
        }
        header('Location: ' . APP_URL . '/empleados/' . $id);
        exit;
    }

    public function eliminarFoto(string $id): void {
        Auth::requireAuth();
        if (!$this->puedeEditarFoto($id)) {
            Session::flash('error', 'No tienes permiso para cambiar esta foto.');
            header('Location: ' . APP_URL . '/empleados/' . $id);
            exit;
        }

        $reg = DB::fetch("SELECT foto_path FROM empleados WHERE id = ?", [$id]);
        if ($reg && $reg['foto_path']) {
            $rutaCompleta = FOTO_UPLOAD_DIR . '/' . $reg['foto_path'];
            if (is_file($rutaCompleta)) @unlink($rutaCompleta);
        }
        DB::execute("UPDATE empleados SET foto_path = NULL WHERE id = ?", [$id]);

        Session::flash('success', 'Foto de perfil eliminada.');
        header('Location: ' . APP_URL . '/empleados/' . $id);
        exit;
    }

    public function guardarFirmaChecklist(string $id): void {
        Auth::requireGestion();
        DB::execute("
            INSERT INTO empleado_checklist_firma (empleado_id, reviso_en_archivo, firma_reviso, fecha_revision)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE reviso_en_archivo = VALUES(reviso_en_archivo),
                                    firma_reviso = VALUES(firma_reviso),
                                    fecha_revision = VALUES(fecha_revision)
        ", [
            $id,
            ($_POST['reviso_en_archivo'] ?? '') ?: null,
            ($_POST['firma_reviso'] ?? '') ?: null,
            date('Y-m-d'),
        ]);

        Session::flash('success', 'Firma registrada.');
        header('Location: ' . APP_URL . '/empleados/' . $id . '/checklist');
        exit;
    }

    public function imprimirChecklist(string $id): void {
        Auth::requireAuth();
        $this->soloPropioSiEmpleado($id);
        $empleado = $this->obtenerEmpleado($id);

        $items = DB::fetchAll("
            SELECT dr.id AS documento_id, dr.codigo, dr.nombre, dr.descripcion, dr.obligatorio,
                   COALESCE(ed.entregado,0) AS entregado, ed.observaciones,
                   ed.archivo_path, ed.archivo_nombre_original
            FROM documentos_requeridos dr
            LEFT JOIN empleado_documentos ed ON ed.documento_id = dr.id AND ed.empleado_id = ?
            WHERE dr.activo = 1
            ORDER BY dr.orden, dr.nombre
        ", [$id]);

        $firma = DB::fetch("SELECT * FROM empleado_checklist_firma WHERE empleado_id = ?", [$id]);

        View::render('empleados.checklist_imprimir', [
            'titulo'   => 'FO-TH-027 — ' . $empleado['nombres'] . ' ' . $empleado['apellidos'],
            'empleado' => $empleado,
            'items'    => $items,
            'firma'    => $firma,
        ], layout: 'imprimible');
    }

    // ── Privado ─────────────────────────────────────────────────────────

    private function obtenerEmpleado(string $id): array {
        $empleado = DB::fetch("
            SELECT e.*, c.nombre AS cargo, a.nombre AS area, p.nombre AS programa,
                   COALESCE(u.foto_path, e.foto_path) AS foto_path
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            LEFT JOIN programas_academicos p ON p.id = e.programa_id
            LEFT JOIN usuarios u ON u.empleado_id = e.id
            WHERE e.id = ?
        ", [$id]);
        if (!$empleado) {
            http_response_code(404);
            exit('Empleado no encontrado.');
        }
        return $empleado;
    }
}