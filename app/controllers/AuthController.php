<?php
class AuthController {
    public function loginForm(): void {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
        View::render('auth.login', ['titulo' => 'Iniciar sesión'], layout: '');
    }

    public function procesar(): void {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        $usuario = DB::fetch(
            'SELECT u.*, 
                    CONCAT(e.nombres, " ", e.apellidos) AS empleado_nombre
             FROM usuarios u
             LEFT JOIN empleados e ON e.id = u.empleado_id
             WHERE u.email = ? AND u.activo = 1',
            [$email]
        );

        if (!$usuario || !password_verify($pass, $usuario['password_hash'])) {
            Session::flash('error', 'Correo o contraseña incorrectos.');
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }

        Session::set('user', [
            'id'         => (int)$usuario['id'],
            'nombre'     => $usuario['nombre'],
            'email'      => $usuario['email'],
            'rol_id'     => (int)$usuario['rol_id'],
            'rol_nombre' => View::rolLabel((int)$usuario['rol_id']),
            'empleado_id'=> $usuario['empleado_id'] ? (int)$usuario['empleado_id'] : null,
        ]);

        header('Location: ' . APP_URL . '/');
        exit;
    }

    public function logout(): void {
        Session::destroy();
        header('Location: ' . APP_URL . '/auth/login');
        exit;
    }
}
