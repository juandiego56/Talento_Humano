<?php
class Router {
    /** @var array<string, array{0:string,1:string}> patrón regex => [Controlador, método] */
    private array $routes = [];

    public function __construct() {
        $this->routes = [
            '#^$#'                                            => ['DashboardController', 'index'],

            // ── Autenticación ──────────────────────────────────────────
            '#^auth/login$#'                                  => ['AuthController', 'loginForm'],
            '#^auth/procesar$#'                                => ['AuthController', 'procesar'],
            '#^auth/logout$#'                                 => ['AuthController', 'logout'],

            // ── Módulo 1: Administración de Personal ───────────────────
            '#^empleados$#'                                   => ['EmpleadoController', 'index'],
            '#^empleados/crear$#'                             => ['EmpleadoController', 'crear'],
            '#^empleados/guardar$#'                           => ['EmpleadoController', 'guardar'],
            '#^empleados/(\d+)$#'                             => ['EmpleadoController', 'ver'],
            '#^empleados/(\d+)/editar$#'                      => ['EmpleadoController', 'editar'],
            '#^empleados/(\d+)/actualizar$#'                  => ['EmpleadoController', 'actualizar'],
            '#^empleados/(\d+)/eliminar$#'                    => ['EmpleadoController', 'eliminar'],
            '#^empleados/(\d+)/hojavida$#'                    => ['EmpleadoController', 'hojaVida'],
            '#^empleados/(\d+)/educacion/guardar$#'           => ['EmpleadoController', 'guardarEducacion'],
            '#^empleados/(\d+)/educacion/(\d+)/eliminar$#'    => ['EmpleadoController', 'eliminarEducacion'],
            '#^empleados/(\d+)/educacion/convalidacion$#'     => ['EmpleadoController', 'toggleConvalidacion'],
            '#^empleados/(\d+)/experiencia/guardar$#'         => ['EmpleadoController', 'guardarExperiencia'],
            '#^empleados/(\d+)/experiencia/(\d+)/eliminar$#'  => ['EmpleadoController', 'eliminarExperiencia'],
            '#^empleados/(\d+)/checklist$#'                            => ['EmpleadoController', 'checklist'],
            '#^empleados/(\d+)/checklist/actualizar$#'                 => ['EmpleadoController', 'actualizarChecklist'],
            '#^empleados/(\d+)/checklist/imprimir$#'                   => ['EmpleadoController', 'imprimirChecklist'],
            '#^empleados/(\d+)/checklist/firma$#'                      => ['EmpleadoController', 'guardarFirmaChecklist'],
            '#^empleados/(\d+)/checklist/documento/(\d+)/archivo$#'    => ['EmpleadoController', 'verArchivoChecklist'],
            '#^empleados/(\d+)/checklist/documento/(\d+)/eliminar-archivo$#' => ['EmpleadoController', 'eliminarArchivoChecklist'],
            '#^empleados/(\d+)/foto$#'                                 => ['EmpleadoController', 'subirFoto'],
            '#^empleados/(\d+)/foto/eliminar$#'                        => ['EmpleadoController', 'eliminarFoto'],

            '#^mi-perfil$#'                                            => ['PerfilController', 'ver'],
            '#^mi-perfil/foto$#'                                       => ['PerfilController', 'subirFoto'],
            '#^mi-perfil/foto/eliminar$#'                              => ['PerfilController', 'eliminarFoto'],

            '#^empleados/(\d+)/entrevista$#'                           => ['EntrevistaController', 'ver'],
            '#^empleados/(\d+)/entrevista/editar$#'                    => ['EntrevistaController', 'formulario'],
            '#^empleados/(\d+)/entrevista/guardar$#'                   => ['EntrevistaController', 'guardar'],
            '#^empleados/(\d+)/entrevista/imprimir$#'                  => ['EntrevistaController', 'imprimir'],
            '#^empleados/(\d+)/entrevista/eliminar$#'                  => ['EntrevistaController', 'eliminar'],

            '#^empleados/(\d+)/entrevista-docente$#'                   => ['EntrevistaDocenteController', 'ver'],
            '#^empleados/(\d+)/entrevista-docente/editar$#'            => ['EntrevistaDocenteController', 'formulario'],
            '#^empleados/(\d+)/entrevista-docente/guardar$#'           => ['EntrevistaDocenteController', 'guardar'],
            '#^empleados/(\d+)/entrevista-docente/imprimir$#'          => ['EntrevistaDocenteController', 'imprimir'],
            '#^empleados/(\d+)/entrevista-docente/eliminar$#'          => ['EntrevistaDocenteController', 'eliminar'],

            '#^documentos$#'                                  => ['DocumentoController', 'index'],
            '#^documentos/guardar$#'                          => ['DocumentoController', 'guardar'],
            '#^documentos/(\d+)/eliminar$#'                   => ['DocumentoController', 'eliminar'],

            // ── Módulo 2: Nómina ────────────────────────────────────────
            '#^nomina$#'                                      => ['NominaController', 'index'],
            '#^nomina/calculadora$#'                          => ['NominaController', 'calculadora'],
            '#^nomina/generar$#'                              => ['NominaController', 'generar'],
            '#^nomina/(\d+)$#'                                => ['NominaController', 'ver'],
            '#^nomina/(\d+)/pagar$#'                          => ['NominaController', 'pagar'],
            '#^nomina/(\d+)/anular$#'                         => ['NominaController', 'anular'],
            '#^nomina/(\d+)/eliminar$#'                       => ['NominaController', 'eliminar'],
            '#^nomina/(\d+)/detalle/(\d+)$#'                  => ['NominaController', 'verDetalle'],
            '#^nomina/(\d+)/detalle/(\d+)/actualizar$#'       => ['NominaController', 'actualizarDetalle'],
            '#^nomina/(\d+)/detalle/(\d+)/concepto/(\d+)/eliminar$#' => ['NominaController', 'eliminarConceptoDetalle'],

            '#^conceptos$#'                                   => ['ConceptoController', 'index'],
            '#^conceptos/guardar$#'                           => ['ConceptoController', 'guardar'],
            '#^conceptos/(\d+)/eliminar$#'                    => ['ConceptoController', 'eliminar'],

            // ── Módulo 3: Bienestar Laboral ─────────────────────────────
            '#^bienestar$#'                                   => ['BienestarController', 'index'],
            '#^bienestar/crear$#'                             => ['BienestarController', 'crear'],
            '#^bienestar/guardar$#'                           => ['BienestarController', 'guardar'],
            '#^bienestar/(\d+)$#'                             => ['BienestarController', 'ver'],
            '#^bienestar/(\d+)/editar$#'                      => ['BienestarController', 'editar'],
            '#^bienestar/(\d+)/actualizar$#'                  => ['BienestarController', 'actualizar'],
            '#^bienestar/(\d+)/eliminar$#'                    => ['BienestarController', 'eliminar'],
            '#^bienestar/(\d+)/inscribir$#'                   => ['BienestarController', 'inscribir'],
            '#^bienestar/(\d+)/inscripcion/(\d+)/asistio$#'   => ['BienestarController', 'marcarAsistio'],
            '#^bienestar/(\d+)/inscripcion/(\d+)/eliminar$#'  => ['BienestarController', 'eliminarInscripcion'],

            // ── Sistema ──────────────────────────────────────────────────
            '#^usuarios$#'                                    => ['UsuarioController', 'index'],
            '#^usuarios/guardar$#'                            => ['UsuarioController', 'guardar'],
            '#^usuarios/(\d+)/eliminar$#'                     => ['UsuarioController', 'eliminar'],

            '#^catalogos$#'                                   => ['CatalogoController', 'index'],
            '#^catalogos/area/guardar$#'                      => ['CatalogoController', 'guardarArea'],
            '#^catalogos/area/(\d+)/eliminar$#'               => ['CatalogoController', 'eliminarArea'],
            '#^catalogos/cargo/guardar$#'                     => ['CatalogoController', 'guardarCargo'],
            '#^catalogos/cargo/(\d+)/eliminar$#'              => ['CatalogoController', 'eliminarCargo'],

            // ── Formulario público de hoja de vida (sin sesión) ─────────
            '#^hoja-de-vida/nueva$#'                          => ['PublicHojaVidaController', 'formulario'],
            '#^hoja-de-vida/guardar$#'                        => ['PublicHojaVidaController', 'guardar'],
            '#^empleados/(\d+)/diligenciar$#'                 => ['PublicHojaVidaController', 'formularioEmpleado'],
            '#^empleados/(\d+)/diligenciar/guardar$#'         => ['PublicHojaVidaController', 'guardarEmpleado'],
        ];
    }

    public function dispatch(string $url): void {
        $url = trim($url, '/');

        foreach ($this->routes as $pattern => [$ctrl, $met]) {
            if (preg_match($pattern, $url, $m)) {
                array_shift($m);
                $this->run($ctrl, $met, array_values($m));
                return;
            }
        }
        $this->notFound();
    }

    private function run(string $ctrl, string $met, array $params): void {
        $file = ROOT . '/app/controllers/' . $ctrl . '.php';
        if (!file_exists($file)) { $this->notFound(); return; }
        require_once $file;
        if (!class_exists($ctrl)) { $this->notFound(); return; }
        $obj = new $ctrl();
        if (!method_exists($obj, $met)) { $this->notFound(); return; }
        call_user_func_array([$obj, $met], $params);
    }

    private function notFound(): void {
        http_response_code(404);
        echo '<div style="font-family:sans-serif;padding:40px;max-width:500px;margin:80px auto">
              <h1 style="color:#1e3a5f">404</h1>
              <p style="color:#666">Página no encontrada.</p>
              <a href="' . APP_URL . '/" style="color:#4f46e5">← Volver al inicio</a>
              </div>';
    }
}