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
                    CONCAT(e.nombres, " ", e.apellidos) AS empleado_nombre,
                    p.nombre AS programa_nombre
             FROM usuarios u
             LEFT JOIN empleados e ON e.id = u.empleado_id
             LEFT JOIN programas_academicos p ON p.id = u.programa_id
             WHERE u.email = ? AND u.activo = 1',
            [$email]
        );

        if (!$usuario || !password_verify($pass, $usuario['password_hash'])) {
            Session::flash('error', 'Correo o contraseña incorrectos.');
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }

        Session::set('user', [
            'id'              => (int)$usuario['id'],
            'nombre'          => $usuario['nombre'],
            'email'           => $usuario['email'],
            'rol_id'          => (int)$usuario['rol_id'],
            'rol_nombre'      => View::rolLabel((int)$usuario['rol_id']),
            'empleado_id'     => $usuario['empleado_id'] ? (int)$usuario['empleado_id'] : null,
            'programa_id'     => $usuario['programa_id'] ? (int)$usuario['programa_id'] : null,
            'programa_nombre' => $usuario['programa_nombre'],
            'foto_path'       => $usuario['foto_path'],
        ]);

        header('Location: ' . APP_URL . '/');
        exit;
    }

    public function logout(): void {
        Session::destroy();
        header('Location: ' . APP_URL . '/auth/login');
        exit;
    }

    public function recuperarForm(): void {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
        View::render('auth.recuperar', ['titulo' => 'Recuperar contraseña'], layout: '');
    }

    public function recuperarEnviar(): void {
        $email = trim($_POST['email'] ?? '');

        if ($email !== '') {
            $usuario = DB::fetch('SELECT id FROM usuarios WHERE email = ? AND activo = 1', [$email]);

            // Solo se crea la solicitud si el correo corresponde a un usuario real y activo,
            // pero el mensaje mostrado es siempre el mismo (evita revelar qué correos existen).
            if ($usuario) {
                $yaPendiente = DB::fetch(
                    "SELECT id FROM solicitudes_password WHERE usuario_id = ? AND estado = 'pendiente'",
                    [$usuario['id']]
                );
                if (!$yaPendiente) {
                    DB::insert(
                        "INSERT INTO solicitudes_password (usuario_id, estado) VALUES (?, 'pendiente')",
                        [$usuario['id']]
                    );
                }
            }
        }

        Session::flash('success', 'Si el correo está registrado en el sistema, tu solicitud fue enviada. Un administrador se pondrá en contacto contigo para restablecer tu contraseña.');
        header('Location: ' . APP_URL . '/auth/login');
        exit;
    }
}
