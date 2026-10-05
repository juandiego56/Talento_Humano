<?php
class NominaController {

    public function index(): void {
        Auth::requireAuth();

        $corteFiltro = trim($_GET['corte'] ?? '');
        $where  = $corteFiltro !== '' ? "WHERE n.corte_academico = ?" : '';
        $params = $corteFiltro !== '' ? [$corteFiltro] : [];

        $nominas = DB::fetchAll("
            SELECT n.*, COUNT(nd.id) AS num_empleados
            FROM nominas n LEFT JOIN nomina_detalle nd ON nd.nomina_id = n.id
            $where
            GROUP BY n.id ORDER BY n.corte_academico DESC, n.periodo DESC
        ", $params);

        $cortes = DB::fetchAll("
            SELECT DISTINCT corte_academico FROM nominas
            WHERE corte_academico IS NOT NULL AND corte_academico <> ''
            ORDER BY corte_academico DESC
        ");

        $empleadosActivos = (int)(DB::fetch("SELECT COUNT(*) t FROM empleados WHERE estado='activo'")['t'] ?? 0);
        $periodoSugerido  = date('Y-m');

        View::render('nomina.index', [
            'titulo'           => 'Nómina',
            'nominas'          => $nominas,
            'cortes'           => $cortes,
            'corteFiltro'      => $corteFiltro,
            'empleadosActivos' => $empleadosActivos,
            'periodoSugerido'  => $periodoSugerido,
            'corteSugerido'    => View::corteAcademicoSugerido($periodoSugerido),
        ]);
    }

    /** Calculadora de costos laborales (simulador ad-hoc, no genera registros de nómina) */
    public function calculadora(): void {
        Auth::requireAuth();

        $empleados   = DB::fetchAll("SELECT id, nombres, apellidos, salario_base, arl_nivel_riesgo FROM empleados WHERE estado='activo' ORDER BY apellidos");
        $nivelesArl  = DB::fetchAll("SELECT * FROM arl_niveles_riesgo ORDER BY nivel");

        $salario  = isset($_GET['salario']) ? (float)$_GET['salario'] : 3200000;
        $dias     = isset($_GET['dias']) ? (int)$_GET['dias'] : 30;
        $horas    = isset($_GET['horas']) ? (int)$_GET['horas'] : 1;
        $nivelArl = isset($_GET['nivel_arl']) ? (int)$_GET['nivel_arl'] : 1;

        // Si viene fecha de ingreso y retiro, se recalculan los días laborados (equivalente a G3:I3 del Excel)
        $fechaIngreso = $_GET['fecha_ingreso'] ?? '';
        $fechaRetiro  = $_GET['fecha_retiro'] ?? '';
        if ($fechaIngreso && $fechaRetiro) {
            $ini = new DateTime($fechaIngreso);
            $fin = new DateTime($fechaRetiro);
            if ($fin >= $ini) {
                $dias = min(30, $ini->diff($fin)->days + 1);
            }
        }

        $tasaArl = 0.00522;
        foreach ($nivelesArl as $n) {
            if ((int)$n['nivel'] === $nivelArl) { $tasaArl = (float)$n['tasa']; break; }
        }

        $resultado = CalculadoraSalarial::calcular($salario, max(1, $dias), max(0, $horas), $tasaArl);

        View::render('nomina.calculadora', [
            'titulo'       => 'Calculadora de Costos Laborales',
            'empleados'    => $empleados,
            'nivelesArl'   => $nivelesArl,
            'nivelArl'     => $nivelArl,
            'salario'      => $salario,
            'dias'         => $dias,
            'horas'        => $horas,
            'fechaIngreso' => $fechaIngreso,
            'fechaRetiro'  => $fechaRetiro,
            'r'            => $resultado,
        ]);
    }

    /** Reporte de horas extra: recopila el concepto "Horas extra" agregado manualmente en las colillas de pago. */
    public function reporteHorasExtra(): void {
        Auth::requireAuth();

        $periodo = trim($_GET['periodo'] ?? '');
        $corte   = trim($_GET['corte'] ?? '');

        $where  = ["(ndc.nombre_concepto = 'Horas extra' OR cn.nombre = 'Horas extra')"];
        $params = [];
        if ($periodo !== '') { $where[] = 'n.periodo = ?'; $params[] = $periodo; }
        if ($corte !== '')   { $where[] = 'n.corte_academico = ?'; $params[] = $corte; }
        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $filas = DB::fetchAll("
            SELECT n.id AS nomina_id, n.periodo, n.corte_academico,
                   e.id AS empleado_id, e.nombres, e.apellidos, e.numero_documento,
                   c.nombre AS cargo, a.nombre AS area,
                   ndc.valor, nd.id AS detalle_id
            FROM nomina_detalle_conceptos ndc
            JOIN nomina_detalle nd ON nd.id = ndc.nomina_detalle_id
            JOIN nominas n         ON n.id = nd.nomina_id
            JOIN empleados e       ON e.id = nd.empleado_id
            LEFT JOIN cargos c     ON c.id = e.cargo_id
            LEFT JOIN areas  a     ON a.id = e.area_id
            LEFT JOIN conceptos_nomina cn ON cn.id = ndc.concepto_id
            $whereSql
            ORDER BY n.periodo DESC, e.apellidos, e.nombres
        ", $params);

        $totalGeneral = 0;
        $porEmpleado  = [];
        foreach ($filas as $f) {
            $totalGeneral += (float)$f['valor'];
            $key = $f['empleado_id'];
            if (!isset($porEmpleado[$key])) {
                $porEmpleado[$key] = [
                    'nombre' => $f['nombres'] . ' ' . $f['apellidos'],
                    'cargo'  => $f['cargo'],
                    'area'   => $f['area'],
                    'total'  => 0,
                    'veces'  => 0,
                ];
            }
            $porEmpleado[$key]['total'] += (float)$f['valor'];
            $porEmpleado[$key]['veces']++;
        }
        uasort($porEmpleado, fn($a, $b) => $b['total'] <=> $a['total']);

        $periodos = DB::fetchAll("SELECT DISTINCT periodo FROM nominas ORDER BY periodo DESC");
        $cortes   = DB::fetchAll("
            SELECT DISTINCT corte_academico FROM nominas
            WHERE corte_academico IS NOT NULL AND corte_academico <> ''
            ORDER BY corte_academico DESC
        ");

        View::render('nomina.horas_extra', [
            'titulo'        => 'Reporte de Horas Extra',
            'filas'         => $filas,
            'porEmpleado'   => $porEmpleado,
            'totalGeneral'  => $totalGeneral,
            'periodos'      => $periodos,
            'cortes'        => $cortes,
            'periodoFiltro' => $periodo,
            'corteFiltro'   => $corte,
        ]);
    }

    /** Genera una nueva nómina para un periodo, calculando devengados/deducciones automáticos */
    public function generar(): void {
        Auth::requireGestion();
        $periodo = trim($_POST['periodo'] ?? '');

        if (!preg_match('/^\d{4}-\d{2}$/', $periodo)) {
            Session::flash('error', 'Periodo inválido. Usa el formato AAAA-MM.');
            header('Location: ' . APP_URL . '/nomina');
            exit;
        }

        $corte = trim($_POST['corte_academico'] ?? '');
        if ($corte === '') {
            $corte = View::corteAcademicoSugerido($periodo);
        } elseif (!preg_match('/^\d{4}-\d+$/', $corte)) {
            Session::flash('error', 'Corte académico inválido. Usa el formato AAAA-N (ej. 2025-2).');
            header('Location: ' . APP_URL . '/nomina');
            exit;
        }

        $existe = DB::fetch("SELECT id FROM nominas WHERE periodo = ?", [$periodo]);
        if ($existe) {
            Session::flash('error', 'Ya existe una nómina generada para ese periodo.');
            header('Location: ' . APP_URL . '/nomina');
            exit;
        }

        $nominaId = DB::insert("
            INSERT INTO nominas (periodo, corte_academico, estado, generado_por) VALUES (?, ?, 'borrador', ?)
        ", [$periodo, $corte, Auth::user()['id']]);

        $empleados = DB::fetchAll("SELECT * FROM empleados WHERE estado = 'activo'");
        $conceptosAuto = DB::fetchAll("SELECT * FROM conceptos_nomina WHERE automatico = 1 AND activo = 1");

        $totalDev = 0; $totalDed = 0;

        foreach ($empleados as $emp) {
            $devengado = 0; $deduccion = 0;
            $lineas = [];

            foreach ($conceptosAuto as $c) {
                if ($c['nombre'] === 'Salario básico') {
                    $valor = (float)$emp['salario_base'];
                } elseif ($c['es_porcentaje']) {
                    $valor = round((float)$emp['salario_base'] * ((float)$c['porcentaje'] / 100), 0);
                } else {
                    $valor = (float)$c['valor_fijo'];
                }
                if ($c['tipo'] === 'devengado') { $devengado += $valor; } else { $deduccion += $valor; }
                $lineas[] = [$c['id'], $c['nombre'], $c['tipo'], $valor];
            }

            $neto = $devengado - $deduccion;
            $detalleId = DB::insert("
                INSERT INTO nomina_detalle (nomina_id, empleado_id, dias_trabajados, salario_base, total_devengado, total_deducciones, neto_pagar)
                VALUES (?,?,30,?,?,?,?)
            ", [$nominaId, $emp['id'], $emp['salario_base'], $devengado, $deduccion, $neto]);

            foreach ($lineas as [$cid, $nombre, $tipo, $valor]) {
                DB::insert("
                    INSERT INTO nomina_detalle_conceptos (nomina_detalle_id, concepto_id, nombre_concepto, tipo, valor)
                    VALUES (?,?,?,?,?)
                ", [$detalleId, $cid, $nombre, $tipo, $valor]);
            }

            $totalDev += $devengado;
            $totalDed += $deduccion;
        }

        DB::execute("UPDATE nominas SET total_devengado=?, total_deducciones=?, total_neto=? WHERE id=?",
            [$totalDev, $totalDed, $totalDev - $totalDed, $nominaId]);

        Session::flash('success', 'Nómina de ' . View::periodoLabel($periodo) . ' generada para ' . count($empleados) . ' empleado(s) activo(s).');
        header('Location: ' . APP_URL . '/nomina/' . $nominaId);
        exit;
    }

    public function ver(string $id): void {
        Auth::requireAuth();
        $nomina = $this->obtenerNomina($id);
        $detalle = DB::fetchAll("
            SELECT nd.*, e.nombres, e.apellidos, e.numero_documento, c.nombre AS cargo
            FROM nomina_detalle nd
            JOIN empleados e ON e.id = nd.empleado_id
            LEFT JOIN cargos c ON c.id = e.cargo_id
            WHERE nd.nomina_id = ? ORDER BY e.apellidos, e.nombres
        ", [$id]);

        View::render('nomina.ver', [
            'titulo'  => 'Nómina — ' . View::periodoLabel($nomina['periodo']),
            'nomina'  => $nomina,
            'detalle' => $detalle,
        ]);
    }

    public function pagar(string $id): void {
        Auth::requireGestion();
        DB::execute("UPDATE nominas SET estado='pagada', fecha_pago=? WHERE id=? AND estado='borrador'", [date('Y-m-d'), $id]);
        Session::flash('success', 'Nómina marcada como pagada.');
        header('Location: ' . APP_URL . '/nomina/' . $id);
        exit;
    }

    public function anular(string $id): void {
        Auth::requireRol(ROL_ADMIN);
        DB::execute("UPDATE nominas SET estado='anulada' WHERE id=?", [$id]);
        Session::flash('success', 'Nómina anulada.');
        header('Location: ' . APP_URL . '/nomina/' . $id);
        exit;
    }

    public function eliminar(string $id): void {
        Auth::requireRol(ROL_ADMIN);
        DB::execute("DELETE FROM nominas WHERE id = ? AND estado = 'borrador'", [$id]);
        Session::flash('success', 'Nómina eliminada.');
        header('Location: ' . APP_URL . '/nomina');
        exit;
    }

    public function verDetalle(string $nominaId, string $detalleId): void {
        Auth::requireAuth();
        $nomina = $this->obtenerNomina($nominaId);
        $detalle = DB::fetch("
            SELECT nd.*, e.nombres, e.apellidos, e.numero_documento, e.email, c.nombre AS cargo, a.nombre AS area
            FROM nomina_detalle nd
            JOIN empleados e ON e.id = nd.empleado_id
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            WHERE nd.id = ? AND nd.nomina_id = ?
        ", [$detalleId, $nominaId]);
        if (!$detalle) { http_response_code(404); exit('Detalle no encontrado.'); }

        $conceptos = DB::fetchAll("SELECT * FROM nomina_detalle_conceptos WHERE nomina_detalle_id = ? ORDER BY tipo DESC, id", [$detalleId]);
        $catalogoConceptos = DB::fetchAll("SELECT * FROM conceptos_nomina WHERE activo = 1 ORDER BY tipo DESC, nombre");

        View::render('nomina.detalle', [
            'titulo'     => 'Colilla de Pago — ' . $detalle['nombres'] . ' ' . $detalle['apellidos'],
            'nomina'     => $nomina,
            'detalle'    => $detalle,
            'conceptos'  => $conceptos,
            'catalogo'   => $catalogoConceptos,
        ]);
    }

    public function actualizarDetalle(string $nominaId, string $detalleId): void {
        Auth::requireGestion();
        $conceptoId = $_POST['concepto_id'];
        $valor      = (float)($_POST['valor'] ?? 0);

        $concepto = DB::fetch("SELECT * FROM conceptos_nomina WHERE id = ?", [$conceptoId]);
        if ($concepto && $valor > 0) {
            DB::insert("
                INSERT INTO nomina_detalle_conceptos (nomina_detalle_id, concepto_id, nombre_concepto, tipo, valor)
                VALUES (?,?,?,?,?)
            ", [$detalleId, $concepto['id'], $concepto['nombre'], $concepto['tipo'], $valor]);
            $this->recalcularDetalle($detalleId, $nominaId);
            Session::flash('success', 'Concepto agregado a la colilla de pago.');
        }

        header('Location: ' . APP_URL . '/nomina/' . $nominaId . '/detalle/' . $detalleId);
        exit;
    }

    public function eliminarConceptoDetalle(string $nominaId, string $detalleId, string $conceptoDetalleId): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM nomina_detalle_conceptos WHERE id = ? AND nomina_detalle_id = ?", [$conceptoDetalleId, $detalleId]);
        $this->recalcularDetalle($detalleId, $nominaId);
        header('Location: ' . APP_URL . '/nomina/' . $nominaId . '/detalle/' . $detalleId);
        exit;
    }

    // ── Privado ─────────────────────────────────────────────────────────

    private function recalcularDetalle(string $detalleId, string $nominaId): void {
        $tot = DB::fetch("
            SELECT
              COALESCE(SUM(CASE WHEN tipo='devengado' THEN valor ELSE 0 END),0) AS dev,
              COALESCE(SUM(CASE WHEN tipo='deduccion' THEN valor ELSE 0 END),0) AS ded
            FROM nomina_detalle_conceptos WHERE nomina_detalle_id = ?
        ", [$detalleId]);
        DB::execute("UPDATE nomina_detalle SET total_devengado=?, total_deducciones=?, neto_pagar=? WHERE id=?",
            [$tot['dev'], $tot['ded'], $tot['dev'] - $tot['ded'], $detalleId]);

        $totNom = DB::fetch("
            SELECT COALESCE(SUM(total_devengado),0) AS dev, COALESCE(SUM(total_deducciones),0) AS ded
            FROM nomina_detalle WHERE nomina_id = ?
        ", [$nominaId]);
        DB::execute("UPDATE nominas SET total_devengado=?, total_deducciones=?, total_neto=? WHERE id=?",
            [$totNom['dev'], $totNom['ded'], $totNom['dev'] - $totNom['ded'], $nominaId]);
    }

    private function obtenerNomina(string $id): array {
        $nomina = DB::fetch("SELECT * FROM nominas WHERE id = ?", [$id]);
        if (!$nomina) { http_response_code(404); exit('Nómina no encontrada.'); }
        return $nomina;
    }
}
