<?php
class ReporteController {

    public function index(): void {
        Auth::requireAuth();

        $programas = DB::fetchAll("SELECT * FROM programas_academicos WHERE activo = 1 ORDER BY nombre");
        $filtros   = $this->leerFiltros();

        $empleados = $this->obtenerEmpleadosFiltrados($filtros);
        $cualificacion = $this->obtenerCualificacionDocente($filtros);

        View::render('reportes.index', [
            'titulo'        => 'Reportes',
            'programas'     => $programas,
            'empleados'     => $empleados,
            'cualificacion' => $cualificacion,
            'filtros'       => $filtros,
        ]);
    }

    public function exportarCsv(): void {
        Auth::requireAuth();
        $empleados = $this->obtenerEmpleadosFiltrados($this->leerFiltros());

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_personal_' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        // BOM UTF-8 para que Excel abra correctamente los acentos
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Nombres', 'Apellidos', 'Documento', 'Cargo', 'Área', 'Programa académico', 'Nivel de formación', 'Dedicación docente', 'Estado', 'Salario básico']);
        foreach ($empleados as $e) {
            fputcsv($out, [
                $e['nombres'], $e['apellidos'], $e['numero_documento'], $e['cargo'] ?? '', $e['area'] ?? '',
                $e['programa'] ?? '', View::nivelEducativoLabelOrVacio($e['nivel_educativo']),
                View::tipoVinculacionDocenteLabel($e['tipo_vinculacion_docente']) === '—' ? '' : View::tipoVinculacionDocenteLabel($e['tipo_vinculacion_docente']),
                $e['estado'], $e['salario_base'],
            ]);
        }
        fclose($out);
        exit;
    }

    /** Exporta a Excel usando el formato nativo SpreadsheetML (XML), sin dependencias externas. */
    public function exportarExcel(): void {
        Auth::requireAuth();
        $empleados = $this->obtenerEmpleadosFiltrados($this->leerFiltros());

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_personal_' . date('Y-m-d') . '.xls"');

        $cols = ['Nombres', 'Apellidos', 'Documento', 'Cargo', 'Área', 'Programa académico', 'Nivel de formación', 'Dedicación docente', 'Estado', 'Salario básico'];

        echo '<?xml version="1.0"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        echo '<Styles><Style ss:ID="encabezado"><Font ss:Bold="1"/><Interior ss:Color="#DCE6F1" ss:Pattern="Solid"/></Style></Styles>' . "\n";
        echo '<Worksheet ss:Name="Reporte de Personal">' . "\n<Table>\n";

        echo '<Row>';
        foreach ($cols as $c) {
            echo '<Cell ss:StyleID="encabezado"><Data ss:Type="String">' . htmlspecialchars($c, ENT_XML1) . '</Data></Cell>';
        }
        echo "</Row>\n";

        foreach ($empleados as $e) {
            $fila = [
                $e['nombres'], $e['apellidos'], $e['numero_documento'], $e['cargo'] ?? '', $e['area'] ?? '',
                $e['programa'] ?? '', View::nivelEducativoLabelOrVacio($e['nivel_educativo']),
                View::tipoVinculacionDocenteLabel($e['tipo_vinculacion_docente']) === '—' ? '' : View::tipoVinculacionDocenteLabel($e['tipo_vinculacion_docente']),
                $e['estado'],
            ];
            echo '<Row>';
            foreach ($fila as $v) {
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$v, ENT_XML1) . '</Data></Cell>';
            }
            echo '<Cell><Data ss:Type="Number">' . (float)$e['salario_base'] . '</Data></Cell>';
            echo "</Row>\n";
        }

        echo "</Table>\n</Worksheet>\n</Workbook>";
        exit;
    }

    /** Exporta a PDF reutilizando el layout "imprimible" ya usado en el sistema (hoja de vida, colillas): el navegador genera el PDF con "Guardar como PDF". */
    public function exportarPdf(): void {
        Auth::requireAuth();
        $filtros   = $this->leerFiltros();
        $empleados = $this->obtenerEmpleadosFiltrados($filtros);
        $cualificacion = $this->obtenerCualificacionDocente($filtros);
        $programas = DB::fetchAll("SELECT * FROM programas_academicos WHERE activo = 1 ORDER BY nombre");

        $programaNombre = '';
        if ($filtros['programa_id'] !== '') {
            foreach ($programas as $p) {
                if ((string)$p['id'] === (string)$filtros['programa_id']) { $programaNombre = $p['nombre']; break; }
            }
        }

        View::render('reportes.pdf', [
            'titulo'         => 'Reporte de Personal',
            'empleados'      => $empleados,
            'cualificacion'  => $cualificacion,
            'filtros'        => $filtros,
            'programaNombre' => $programaNombre,
        ], layout: 'imprimible');
    }

    // ── Privado ─────────────────────────────────────────────────────────

    private function leerFiltros(): array {
        return [
            'programa_id' => trim($_GET['programa_id'] ?? ''),
            'estado'      => trim($_GET['estado'] ?? 'activo'),
        ];
    }

    private function obtenerEmpleadosFiltrados(array $filtros): array {
        $where  = [];
        $params = [];
        if ($filtros['programa_id'] !== '') { $where[] = 'e.programa_id = ?'; $params[] = $filtros['programa_id']; }
        if ($filtros['estado'] !== '')      { $where[] = 'e.estado = ?';      $params[] = $filtros['estado']; }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        return DB::fetchAll("
            SELECT e.*, c.nombre AS cargo, a.nombre AS area, p.nombre AS programa
            FROM empleados e
            LEFT JOIN cargos c ON c.id = e.cargo_id
            LEFT JOIN areas  a ON a.id = e.area_id
            LEFT JOIN programas_academicos p ON p.id = e.programa_id
            $whereSql
            ORDER BY e.apellidos, e.nombres
        ", $params);
    }

    /** Cuenta los docentes (empleados con dedicación docente) agrupados por nivel de formación, respetando los mismos filtros. */
    private function obtenerCualificacionDocente(array $filtros): array {
        $where  = ["e.tipo_vinculacion_docente IS NOT NULL"];
        $params = [];
        if ($filtros['programa_id'] !== '') { $where[] = 'e.programa_id = ?'; $params[] = $filtros['programa_id']; }
        if ($filtros['estado'] !== '')      { $where[] = 'e.estado = ?';      $params[] = $filtros['estado']; }
        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $filas = DB::fetchAll("
            SELECT COALESCE(e.nivel_educativo, '') AS nivel_educativo, COUNT(*) AS total
            FROM empleados e
            $whereSql
            GROUP BY e.nivel_educativo
        ", $params);

        $orden = ['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado',''];
        $porNivel = array_fill_keys($orden, 0);
        foreach ($filas as $f) {
            $porNivel[$f['nivel_educativo']] = (int)$f['total'];
        }

        $resultado = [];
        foreach ($orden as $nivel) {
            if ($porNivel[$nivel] === 0) continue;
            $resultado[] = [
                'nivel'    => $nivel,
                'etiqueta' => $nivel === '' ? 'Sin especificar' : View::nivelEducativoLabel($nivel),
                'total'    => $porNivel[$nivel],
            ];
        }
        return $resultado;
    }
}
