<?php
require_once ROOT . '/app/helpers/Schema.php';

class UsuarioController {
    public function index(): void {
        Auth::requireRol(ROL_ADMIN);
        $porPagina = 15;
        $total = (int)(DB::fetch("SELECT COUNT(*) AS t FROM usuarios")['t'] ?? 0);
        [$pagina, $paginas, $offset] = View::paginar($total, $porPagina, $_GET['pagina'] ?? 1);
        $usuarios = DB::fetchAll("
            SELECT u.*, CONCAT(e.nombres,' ',e.apellidos) AS empleado_nombre, p.nombre AS programa_nombre
            FROM usuarios u
            LEFT JOIN empleados e ON e.id = u.empleado_id
            LEFT JOIN programas_academicos p ON p.id = u.programa_id
            ORDER BY u.nombre
            LIMIT $porPagina OFFSET $offset
        ");
        $empleados = DB::fetchAll("SELECT id, nombres, apellidos FROM empleados WHERE estado='activo' ORDER BY apellidos");
        $programas = DB::fetchAll("SELECT id, nombre FROM programas_academicos WHERE activo = 1 ORDER BY nombre");
        $solicitudes = DB::fetchAll("
            SELECT sp.id, sp.usuario_id, sp.fecha_solicitud, u.nombre, u.email
            FROM solicitudes_password sp
            JOIN usuarios u ON u.id = sp.usuario_id
            WHERE sp.estado = 'pendiente'
            ORDER BY sp.fecha_solicitud ASC
        ");
        View::render('usuarios.index', [
            'titulo'      => 'Usuarios del Sistema',
            'usuarios'    => $usuarios,
            'pagina'      => $pagina, 'paginas' => $paginas, 'total' => $total, 'porPagina' => $porPagina,
            'empleados'   => $empleados,
            'programas'   => $programas,
            'solicitudes' => $solicitudes,
        ]);
    }

    public function guardar(): void {
        Auth::requireRol(ROL_ADMIN);
        Schema::asegurarCambioClave();
        try {
            $rolId = (int)$_POST['rol_id'];
            DB::insert("
                INSERT INTO usuarios (nombre, email, password_hash, rol_id, programa_id, empleado_id, activo, debe_cambiar_password)
                VALUES (?,?,?,?,?,?,1,1)
            ", [
                $_POST['nombre'], $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $rolId,
                $rolId === ROL_DIRECTOR_PROGRAMA ? (($_POST['programa_id'] ?? '') ?: null) : null,
                ($_POST['empleado_id'] ?? '') ?: null,
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

    public function restablecerPassword(string $id): void {
        Auth::requireRol(ROL_ADMIN);
        $pass = $_POST['password'] ?? '';

        if (strlen($pass) < 6) {
            Session::flash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
            header('Location: ' . APP_URL . '/usuarios');
            exit;
        }

        Schema::asegurarCambioClave();
        DB::execute("UPDATE usuarios SET password_hash = ?, debe_cambiar_password = 1 WHERE id = ?", [
            password_hash($pass, PASSWORD_DEFAULT), $id,
        ]);
        DB::execute("
            UPDATE solicitudes_password
            SET estado = 'atendida', atendida_por = ?, fecha_atendida = NOW()
            WHERE usuario_id = ? AND estado = 'pendiente'
        ", [Auth::user()['id'], $id]);

        Session::flash('success', 'Contraseña restablecida. La persona deberá crear una nueva al ingresar.');
        header('Location: ' . APP_URL . '/usuarios');
        exit;
    }
}
