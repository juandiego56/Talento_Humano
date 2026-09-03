<?php
class DocumentoController {
    public function index(): void {
        Auth::requireGestion();
        $documentos = DB::fetchAll("SELECT * FROM documentos_requeridos ORDER BY obligatorio DESC, nombre");
        View::render('documentos.index', [
            'titulo'     => 'Catálogo de Documentos — Lista de Chequeo',
            'documentos' => $documentos,
        ]);
    }

    public function guardar(): void {
        Auth::requireGestion();
        DB::insert("
            INSERT INTO documentos_requeridos (nombre, descripcion, obligatorio)
            VALUES (?,?,?)
        ", [$_POST['nombre'], ($_POST['descripcion'] ?? '') ?: null, isset($_POST['obligatorio']) ? 1 : 0]);

        Session::flash('success', 'Documento agregado al catálogo. Recuerda actualizarlo en las listas de chequeo existentes si aplica.');
        header('Location: ' . APP_URL . '/documentos');
        exit;
    }

    public function eliminar(string $id): void {
        Auth::requireGestion();
        DB::execute("UPDATE documentos_requeridos SET activo = 0 WHERE id = ?", [$id]);
        Session::flash('success', 'Documento desactivado del catálogo.');
        header('Location: ' . APP_URL . '/documentos');
        exit;
    }
}
