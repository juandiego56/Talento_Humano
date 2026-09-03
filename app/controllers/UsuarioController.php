<?php
class UsuarioController {
    public function index(): void {
        Auth::requireRol(ROL_ADMIN);
        $usuarios = DB::fetchAll("
            SELECT u.*, CONCAT(e.nombres,' ',e.apellidos) AS empleado_nombre
            FROM usuarios u LEFT JOIN empleados e ON e.id = u.empleado_id
            ORDER BY u.nombre
        ");
        $empleados = DB::fetchAll("SELECT id, nombres, apellidos FROM empleados WHERE estado='activo' ORDER BY apellidos");
        View::render('usuarios.index', [
            'titulo'    => 'Usuarios del Sistema',
            'usuarios'  => $usuarios,
            'empleados' => $empleados,
        ]);
    }

    public function guardar(): void {
        Auth::requireRol(ROL_ADMIN);
        try {
            DB::insert("
                INSERT INTO usuarios (nombre, email, password_hash, rol_id, empleado_id, activo)
                VALUES (?,?,?,?,?,1)
            ", [
                $_POST['nombre'], $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                (int)$_POST['rol_id'], ($_POST['empleado_id'] ?? '') ?: null,
            ]);
            Session::flash('success', 'Usuario creado correctamente.');
        } catch (\PDOException $e) {
            Session::flash('error', str_contains($e->getMessage(), 'Duplicate')
                ? 'Ya existe un usuario con ese correo.' : 'Error al crear el usuario.');
        }
        header('Location: ' . APP_URL . '/usuarios');
        exit;
    }

    public function eliminar(string $id): void {
        Auth::requireRol(ROL_ADMIN);
        if ((int)$id === (int)Auth::user()['id']) {
            Session::flash('error', 'No puedes eliminar tu propio usuario.');
        } else {
            DB::execute("DELETE FROM usuarios WHERE id = ?", [$id]);
            Session::flash('success', 'Usuario eliminado.');
        }
        header('Location: ' . APP_URL . '/usuarios');
        exit;
    }
}
