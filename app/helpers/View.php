<?php
class View {
    public static function render(string $view, array $data = [], string $layout = 'main'): void {
        extract($data);
        $content    = ROOT . '/app/views/' . str_replace('.', '/', $view) . '.php';
        $layoutFile = ROOT . '/app/views/layouts/' . $layout . '.php';
        if (!file_exists($content)) throw new RuntimeException("Vista no encontrada: $view");
        ob_start();
        require $content;
        $pageContent = ob_get_clean();
        if ($layout && file_exists($layoutFile)) require $layoutFile;
        else echo $pageContent;
    }

    public static function partial(string $partial, array $data = []): void {
        extract($data);
        require ROOT . '/app/views/' . str_replace('.', '/', $partial) . '.php';
    }

    public static function e(mixed $val): string {
        return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }

    public static function money(float|int|null $val): string {
        return '$ ' . number_format((float)$val, 0, ',', '.');
    }

    public static function fecha(?string $val): string {
        if (!$val) return '—';
        $t = strtotime($val);
        return $t ? date('d/m/Y', $t) : '—';
    }

    public static function rolLabel(int $rol): string {
        return match($rol) {
            ROL_ADMIN    => 'Administrador',
            ROL_GESTOR   => 'Gestor de Talento Humano',
            ROL_EMPLEADO => 'Empleado',
            default      => 'Desconocido',
        };
    }

    public static function rolBadgeClass(int $rol): string {
        return match($rol) {
            ROL_ADMIN    => 'badge-siac',
            ROL_GESTOR   => 'badge-coordinador',
            ROL_EMPLEADO => 'badge-vicerrectoria',
            default      => '',
        };
    }

    public static function estadoEmpleadoLabel(string $e): string {
        return match($e) {
            'activo'   => 'Activo',
            'inactivo' => 'Inactivo',
            'retirado' => 'Retirado',
            default    => ucfirst($e),
        };
    }

    public static function estadoEmpleadoBadge(string $e): string {
        return match($e) {
            'activo'   => 'badge-aprobado',
            'inactivo' => 'badge-en-revision',
            'retirado' => 'badge-borrador',
            default    => '',
        };
    }

    public static function tipoContratoLabel(string $t): string {
        return match($t) {
            'termino_fijo'         => 'Término Fijo',
            'termino_indefinido'   => 'Término Indefinido',
            'obra_labor'           => 'Obra o Labor',
            'prestacion_servicios' => 'Prestación de Servicios',
            default                => ucfirst($t),
        };
    }

    public static function nivelEducativoLabel(string $n): string {
        return match($n) {
            'bachillerato'     => 'Bachillerato',
            'tecnico'          => 'Técnico',
            'tecnologo'        => 'Tecnólogo',
            'pregrado'         => 'Pregrado',
            'especializacion'  => 'Especialización',
            'maestria'         => 'Maestría',
            'doctorado'        => 'Doctorado',
            default            => ucfirst($n),
        };
    }

    public static function estadoNominaLabel(string $e): string {
        return match($e) {
            'borrador' => 'Borrador',
            'pagada'   => 'Pagada',
            'anulada'  => 'Anulada',
            default    => ucfirst($e),
        };
    }

    public static function estadoNominaBadge(string $e): string {
        return match($e) {
            'borrador' => 'badge-en-revision',
            'pagada'   => 'badge-aprobado',
            'anulada'  => 'badge-borrador',
            default    => '',
        };
    }

    public static function tipoActividadLabel(string $t): string {
        return match($t) {
            'capacitacion' => 'Capacitación',
            'recreacion'   => 'Recreación',
            'salud'        => 'Salud y Bienestar',
            'integracion'  => 'Integración',
            'deportivo'    => 'Deportivo',
            default        => ucfirst($t),
        };
    }

    public static function estadoActividadLabel(string $e): string {
        return match($e) {
            'programada' => 'Programada',
            'en_curso'   => 'En curso',
            'finalizada' => 'Finalizada',
            'cancelada'  => 'Cancelada',
            default      => ucfirst($e),
        };
    }

    public static function estadoActividadBadge(string $e): string {
        return match($e) {
            'programada' => 'badge-en-revision',
            'en_curso'   => 'badge-generado',
            'finalizada' => 'badge-aprobado',
            'cancelada'  => 'badge-borrador',
            default      => '',
        };
    }

    public static function periodoLabel(string $p): string {
        // 'YYYY-MM' -> 'Mes YYYY'
        $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                  7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        [$y, $m] = array_pad(explode('-', $p), 2, null);
        if (!$y || !$m) return $p;
        return ($meses[(int)$m] ?? $m) . ' ' . $y;
    }

    public static function siNoLabel(?string $v): string {
        return match($v) {
            'si' => 'SI',
            'no' => 'NO',
            default => '—',
        };
    }

    public static function modalidadLabel(?string $v): string {
        return match($v) {
            'presencial' => 'Presencial',
            'virtual'    => 'Virtual',
            default      => '—',
        };
    }

    public static function posgradosEstadoLabel(?string $v): string {
        return match($v) {
            'no_aplica'  => 'No Aplica',
            'en_curso'   => 'En curso',
            'culminado'  => 'Culminado',
            default      => '—',
        };
    }

    public static function decisionLabel(?string $v): string {
        return match($v) {
            'vincular'  => 'Vincular',
            'descartar' => 'Descartar',
            default     => '—',
        };
    }

    public static function tipoVinculacionDocenteLabel(?string $v): string {
        return match($v) {
            'tiempo_completo' => 'Docente Tiempo Completo',
            'medio_tiempo'    => 'Docente Medio Tiempo',
            'hora_catedra'    => 'Docente Hora Cátedra',
            default           => '—',
        };
    }
}
