<?php
class ConceptoController {
    public function index(): void {
        Auth::requireGestion();
        $conceptos = DB::fetchAll("SELECT * FROM conceptos_nomina ORDER BY tipo DESC, nombre");
        View::render('conceptos.index', [
            'titulo'     => 'Conceptos de Nómina',
            'conceptos'  => $conceptos,
        ]);
    }

    public function guardar(): void {
        Auth::requireGestion();
        $esPorcentaje = isset($_POST['es_porcentaje']) ? 1 : 0;
        DB::insert("
            INSERT INTO conceptos_nomina (nombre, tipo, es_porcentaje, porcentaje, valor_fijo, automatico)
            VALUES (?,?,?,?,?,?)
        ", [
            $_POST['nombre'], $_POST['tipo'], $esPorcentaje,
            $esPorcentaje ? (float)($_POST['porcentaje'] ?? 0) : null,
            !$esPorcentaje ? (float)($_POST['valor_fijo'] ?? 0) : null,
            isset($_POST['automatico']) ? 1 : 0,
        ]);
        Session::flash('success', 'Concepto de nómina creado.');
        header('Location: ' . APP_URL . '/conceptos');
        exit;
    }

    public function eliminar(string $id): void {
        Auth::requireGestion();
        DB::execute("UPDATE conceptos_nomina SET activo = 0 WHERE id = ?", [$id]);
        Session::flash('success', 'Concepto desactivado.');
        header('Location: ' . APP_URL . '/conceptos');
        exit;
    }
}
