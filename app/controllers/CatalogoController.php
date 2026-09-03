<?php
class CatalogoController {
    public function index(): void {
        Auth::requireGestion();
        $areas  = DB::fetchAll("
            SELECT a.*, COUNT(e.id) AS num_empleados
            FROM areas a LEFT JOIN empleados e ON e.area_id = a.id
            GROUP BY a.id ORDER BY a.nombre
        ");
        $cargos = DB::fetchAll("
            SELECT c.*, a.nombre AS area, COUNT(e.id) AS num_empleados
            FROM cargos c LEFT JOIN areas a ON a.id = c.area_id
            LEFT JOIN empleados e ON e.cargo_id = c.id
            GROUP BY c.id ORDER BY c.nombre
        ");
        View::render('catalogos.index', [
            'titulo' => 'Catálogos — Áreas y Cargos',
            'areas'  => $areas,
            'cargos' => $cargos,
        ]);
    }

    public function guardarArea(): void {
        Auth::requireGestion();
        DB::insert("INSERT INTO areas (nombre, descripcion) VALUES (?,?)", [$_POST['nombre'], ($_POST['descripcion'] ?? '') ?: null]);
        Session::flash('success', 'Área creada.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function eliminarArea(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM areas WHERE id = ?", [$id]);
        Session::flash('success', 'Área eliminada.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function guardarCargo(): void {
        Auth::requireGestion();
        DB::insert("
            INSERT INTO cargos (nombre, area_id, salario_base_sugerido) VALUES (?,?,?)
        ", [$_POST['nombre'], ($_POST['area_id'] ?? '') ?: null, $_POST['salario_base_sugerido'] !== '' ? (float)$_POST['salario_base_sugerido'] : null]);
        Session::flash('success', 'Cargo creado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }

    public function eliminarCargo(string $id): void {
        Auth::requireGestion();
        DB::execute("DELETE FROM cargos WHERE id = ?", [$id]);
        Session::flash('success', 'Cargo eliminado.');
        header('Location: ' . APP_URL . '/catalogos');
        exit;
    }
}
