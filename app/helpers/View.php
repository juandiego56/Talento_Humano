<?php
class View {
    public static function render(string $view, array $data = [], string $layout = 'main'): void {
        // Evita que el navegador muestre una copia vieja al volver atrás: la página se vuelve a cargar.
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }
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

    /** Ícono SVG inline (check o reloj) para el estado de convalidación de formación académica. */
    public static function convalidadaIcon(bool $convalidada): string {
        $path = $convalidada
            ? '<circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75"/>'
            : '<circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3.5 2"/>';
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px;vertical-align:-1px">'.$path.'</svg>';
    }

    public static function rolLabel(int $rol): string {
        return match($rol) {
            ROL_ADMIN             => 'Administrador',
            ROL_GESTOR            => 'Gestor de Talento Humano',
            ROL_EMPLEADO          => 'Empleado',
            ROL_DIRECTOR_PROGRAMA => 'Director de Programa',
            default               => 'Desconocido',
        };
    }

    public static function rolBadgeClass(int $rol): string {
        return match($rol) {
            ROL_ADMIN             => 'badge-siac',
            ROL_GESTOR            => 'badge-coordinador',
            ROL_EMPLEADO          => 'badge-vicerrectoria',
            ROL_DIRECTOR_PROGRAMA => 'badge-generado',
            default               => '',
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

    /** Igual que nivelEducativoLabel() pero tolera null/vacío (empleados sin nivel de formación registrado). */
    public static function nivelEducativoLabelOrVacio(?string $n): string {
        return $n ? self::nivelEducativoLabel($n) : '';
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
            'anulada'  => 'badge-rechazado',
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

    /** Sugiere el corte académico (semestre) a partir de un periodo 'YYYY-MM'. Ej. '2025-07' -> '2025-2'. */
    public static function corteAcademicoSugerido(string $periodo): string {
        [$y, $m] = array_pad(explode('-', $periodo), 2, null);
        if (!$y || !$m) return '';
        return $y . '-' . ((int)$m <= 6 ? '1' : '2');
    }

    /** 'YYYY-N' -> 'Primer/Segundo semestre YYYY' */
    public static function corteAcademicoLabel(?string $corte): string {
        if (!$corte) return '—';
        [$y, $s] = array_pad(explode('-', $corte), 2, null);
        if (!$y || !$s) return $corte;
        $semestre = (int)$s === 1 ? 'Primer semestre' : (((int)$s === 2) ? 'Segundo semestre' : "Semestre $s");
        return "$semestre $y ($corte)";
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

    public static function estadoSolicitudLabel(string $e): string {
        return match($e) {
            'pendiente' => 'Pendiente',
            'aprobada'  => 'Aprobada',
            'rechazada' => 'Rechazada',
            default     => ucfirst($e),
        };
    }

    public static function estadoSolicitudBadge(string $e): string {
        return match($e) {
            'pendiente' => 'badge-en-revision',
            'aprobada'  => 'badge-aprobado',
            'rechazada' => 'badge-rechazado',
            default     => '',
        };
    }

    public static function arlNivelRiesgoLabel(?int $nivel): string {
        $romano = match((int)$nivel) {
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', default => null,
        };
        return $romano ? "Riesgo $romano" : '—';
    }

    // ── Hoja de vida del empleado ────────────────────────────────────────
    public static function hojaVidaEstadoLabel(?string $e): string {
        return match($e) {
            'enviada'  => 'En revisión',
            'aprobada' => 'Aprobada',
            'devuelta' => 'Devuelta con observaciones',
            default    => 'Pendiente de diligenciar',
        };
    }

    public static function hojaVidaEstadoBadge(?string $e): string {
        return match($e) {
            'enviada'  => 'badge-en-revision',
            'aprobada' => 'badge-aprobado',
            'devuelta' => 'badge-rechazado',
            default    => 'badge-borrador',
        };
    }

    // ── Paginación ───────────────────────────────────────────────────────
    /** Calcula la página actual y el desplazamiento. Devuelve [pagina, totalPaginas, offset]. */
    public static function paginar(int $total, int $porPagina, mixed $paginaPedida): array {
        $paginas = max(1, (int)ceil($total / $porPagina));
        $pagina  = min($paginas, max(1, (int)$paginaPedida));
        return [$pagina, $paginas, ($pagina - 1) * $porPagina];
    }

    /** URL de la lista actual conservando los filtros y cambiando solo la página. */
    public static function urlPagina(int $pagina): string {
        $q = $_GET;
        $q['pagina'] = $pagina;
        $ruta = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
        return $ruta . '?' . http_build_query($q);
    }
}
