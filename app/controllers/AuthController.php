<?php
require_once ROOT . '/app/helpers/Schema.php';

class AuthController {
    public function loginForm(): void {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
        View::render('auth.login', ['titulo' => 'Iniciar sesión'], layout: '');
    }

    public function procesar(): void {
        Schema::asegurarCambioClave();
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
            'debe_cambiar_password' => !empty($usuario['debe_cambiar_password']),
        ]);

        header('Location: ' . APP_URL . (!empty($usuario['debe_cambiar_password']) ? '/cambiar-password' : '/'));
        exit;
    }

    /** Pantalla obligatoria de primer ingreso: el usuario crea su propia contraseña. */
    public function cambiarForm(): void {
        Auth::requireAuth(true);
        if (empty(Session::get('user')['debe_cambiar_password'])) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
        View::render('auth.cambiar_password', ['titulo' => 'Crea tu contraseña'], layout: '');
    }

    public function cambiarGuardar(): void {
        Auth::requireAuth(true);
        $u = Session::get('user');
        if (empty($u['debe_cambiar_password']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/');
            exit;
        }
        $nueva = $_POST['password'] ?? '';
        $conf  = $_POST['password_confirm'] ?? '';
        $fila  = DB::fetch('SELECT password_hash FROM usuarios WHERE id = ?', [$u['id']]);

        $error = null;
        if (strlen($nueva) < 8)                                   $error = 'La contraseña debe tener al menos 8 caracteres.';
        elseif (!preg_match('/[A-Za-z]/', $nueva) || !preg_match('/\d/', $nueva)) $error = 'La contraseña debe tener letras y números.';
        elseif ($nueva !== $conf)                                 $error = 'Las contraseñas no coinciden.';
        elseif ($fila && password_verify($nueva, $fila['password_hash'])) $error = 'La nueva contraseña no puede ser igual a la anterior.';

        if ($error) {
            Session::flash('error', $error);
            header('Location: ' . APP_URL . '/cambiar-password');
            exit;
        }

        DB::execute('UPDATE usuarios SET password_hash = ?, debe_cambiar_password = 0 WHERE id = ?',
            [password_hash($nueva, PASSWORD_DEFAULT), $u['id']]);
        $u['debe_cambiar_password'] = false;
        Session::set('user', $u);
        Session::flash('success', 'Listo, tu contraseña quedó guardada. Desde ahora ingresa con ella.');
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
