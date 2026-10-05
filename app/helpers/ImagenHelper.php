<?php
class ImagenHelper {
    /**
     * Heurística simple (no es reconocimiento de imágenes con IA): mira el color en el
     * borde de la foto —donde normalmente está el fondo en una foto tipo selfie/documento—
     * y evalúa si luce razonablemente blanco/claro. Devuelve null si no se pudo analizar
     * (formato no soportado, imagen corrupta, etc.), en cuyo caso simplemente no se avisa nada.
     */
    public static function pareceTenerFondoBlanco(string $rutaImagen): ?bool {
        // Si la extensión GD no está instalada en el servidor, no hay forma de analizar
        // la imagen: se omite el aviso en lugar de arriesgar un error fatal.
        if (!extension_loaded('gd')) return null;

        $info = @getimagesize($rutaImagen);
        if (!$info) return null;

        $tipo = $info[2] ?? null;
        $img = null;
        if ($tipo === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) {
            $img = @imagecreatefromjpeg($rutaImagen);
        } elseif ($tipo === IMAGETYPE_PNG && function_exists('imagecreatefrompng')) {
            $img = @imagecreatefrompng($rutaImagen);
        } elseif ($tipo === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            $img = @imagecreatefromwebp($rutaImagen);
        }
        if (!$img) return null;

        $w = imagesx($img);
        $h = imagesy($img);
        if ($w < 10 || $h < 10) { imagedestroy($img); return null; }

        // Muestrea puntos a lo largo de los 4 bordes de la imagen.
        $muestras = [];
        $pasos = 12;
        for ($i = 1; $i < $pasos; $i++) {
            $muestras[] = [(int)($w * $i / $pasos), 2];
            $muestras[] = [(int)($w * $i / $pasos), $h - 3];
            $muestras[] = [2, (int)($h * $i / $pasos)];
            $muestras[] = [$w - 3, (int)($h * $i / $pasos)];
        }

        $totalBrillo = 0;
        $n = 0;
        foreach ($muestras as [$x, $y]) {
            $rgb = @imagecolorat($img, $x, $y);
            if ($rgb === false) continue;
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $totalBrillo += ($r + $g + $b) / 3;
            $n++;
        }
        imagedestroy($img);
        if ($n === 0) return null;

        // Bordes con brillo promedio alto (>=200 de 255) sugieren fondo blanco/claro.
        return ($totalBrillo / $n) >= 200;
    }
}
