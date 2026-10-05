<?php
class PerfilController {

    public function ver(): void {
        Auth::requireAuth();
        $usuario = DB::fetch("SELECT * FROM usuarios WHERE id = ?", [Auth::user()['id']]);

        View::render('perfil.ver', [
            'titulo'  => 'Mi perfil',
            'usuario' => $usuario,
        ]);
    }

    public function subirFoto(): void {
        Auth::requireAuth();
        $userId = Auth::user()['id'];

        if (empty($_FILES['foto']['name'])) {
            Session::flash('error', 'No se seleccionó ninguna imagen.');
            header('Location: ' . APP_URL . '/mi-perfil');
            exit;
        }

        $file = $_FILES['foto'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No se pudo subir la imagen. Intenta nuevamente.');
            header('Location: ' . APP_URL . '/mi-perfil');
            exit;
        }
        if ($file['size'] > FOTO_MAX_SIZE) {
            Session::flash('error', 'La imagen supera el tamaño máximo permitido (2 MB).');
            header('Location: ' . APP_URL . '/mi-perfil');
            exit;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, FOTO_EXT_PERMITIDAS, true)) {
            Session::flash('error', 'Formato no permitido. Usa JPG, PNG o WEBP.');
            header('Location: ' . APP_URL . '/mi-perfil');
            exit;
        }

        if (!is_dir(FOTO_UPLOAD_DIR)) mkdir(FOTO_UPLOAD_DIR, 0775, true);

        // Borra la foto anterior si existía
        $anterior = DB::fetch("SELECT foto_path FROM usuarios WHERE id = ?", [$userId]);
        if ($anterior && $anterior['foto_path']) {
            $rutaAnterior = FOTO_UPLOAD_DIR . '/' . $anterior['foto_path'];
            if (is_file($rutaAnterior)) @unlink($rutaAnterior);
        }

        $nombreArchivo = 'usuario_' . $userId . '_' . time() . '.' . $ext;
        $rutaCompleta  = FOTO_UPLOAD_DIR . '/' . $nombreArchivo;

        if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            Session::flash('error', 'Error al guardar la imagen en el servidor.');
            header('Location: ' . APP_URL . '/mi-perfil');
            exit;
        }

        DB::execute("UPDATE usuarios SET foto_path = ? WHERE id = ?", [$nombreArchivo, $userId]);

        // Refresca la sesión para que la barra superior muestre la foto sin re-loguearse
        $user = Auth::user();
        $user['foto_path'] = $nombreArchivo;
        Session::set('user', $user);

        $fondoBlanco = ImagenHelper::pareceTenerFondoBlanco($rutaCompleta);
        if ($fondoBlanco === false) {
            Session::flash('warning', 'Foto guardada, pero el fondo no parece blanco/claro. Recuerda que debe ser tipo selfie con fondo blanco.');
        } else {
            Session::flash('success', 'Foto de perfil actualizada.');
        }
        header('Location: ' . APP_URL . '/mi-perfil');
        exit;
    }

    public function eliminarFoto(): void {
        Auth::requireAuth();
        $userId = Auth::user()['id'];

        $reg = DB::fetch("SELECT foto_path FROM usuarios WHERE id = ?", [$userId]);
        if ($reg && $reg['foto_path']) {
            $rutaCompleta = FOTO_UPLOAD_DIR . '/' . $reg['foto_path'];
            if (is_file($rutaCompleta)) @unlink($rutaCompleta);
        }
        DB::execute("UPDATE usuarios SET foto_path = NULL WHERE id = ?", [$userId]);

        $user = Auth::user();
        $user['foto_path'] = null;
        Session::set('user', $user);

        Session::flash('success', 'Foto de perfil eliminada.');
        header('Location: ' . APP_URL . '/mi-perfil');
        exit;
    }
}
