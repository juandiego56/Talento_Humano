<?php
/**
 * Replica exacta de la plantilla "PLANTILLA_CALCULO_SALARIOS.xlsx".
 * Cada método corresponde a una celda/fórmula de la hoja "PLANTILLA SALARIAL".
 */
class CalculadoraSalarial {

    /**
     * Replica el patrón de fórmula de Excel:
     * IF(base_original < piso, IF(base_prorateada >= piso, base_prorateada*tasa, piso*tasa), base_prorateada*tasa)
     */
    private static function conTope(float $baseOriginal, float $baseProrateada, float $piso, float $tasa): float {
        if ($baseOriginal < $piso) {
            return $baseProrateada >= $piso ? $baseProrateada * $tasa : $piso * $tasa;
        }
        return $baseProrateada * $tasa;
    }

    /**
     * Calcula el desglose completo de costos laborales para un salario y unos días laborados.
     *
     * @param float $salario  Salario mensual (B2)
     * @param int   $dias     Días laborados en el periodo (E2), sobre base de 30
     * @param int   $horas    Cantidad de horas extra/recargo a valorizar (E27)
     * @param float $tasaArl  Tasa de ARL según nivel de riesgo (I-V), como fracción. Por defecto Riesgo I (0.522%).
     */
    public static function calcular(float $salario, int $dias = 30, int $horas = 1, float $tasaArl = 0.00522): array {
        $piso = CL_PISO_IBC;

        // Auxilio de transporte: solo si el salario no supera el tope (B3)
        $auxTransporte = $salario < CL_AUX_TRANSPORTE_TOPE ? CL_AUX_TRANSPORTE : 0;

        // Prorrateo según días laborados (B6, B7, B8)
        $salarioProrateado = $salario / 30 * $dias;
        $auxProrateado      = $auxTransporte / 30 * $dias;
        $totalDevengadoBase = $salarioProrateado + $auxProrateado;

        // ── Costos a cargo del empleador (seguridad social) ──────────────
        $saludEmpleador    = self::conTope($salario, $salarioProrateado, $piso, 0.085);   // B9
        $pensionEmpleador  = self::conTope($salario, $salarioProrateado, $piso, 0.12);    // B10
        $arl               = self::conTope($auxTransporte, $auxProrateado, $piso, $tasaArl); // B11
        $totalSegSocialEmp = $saludEmpleador + $pensionEmpleador + $arl;                   // B12

        // ── Prestaciones sociales (comunes a empleador y empleado) ───────
        $prima              = ($salario + $auxTransporte) * $dias / 360;  // B13 / E13
        $cesantias          = ($salario + $auxTransporte) * $dias / 360; // B14 / E14
        $interesesCesantias = $cesantias * 0.12 * $dias / 360;           // B15 / E15
        $vacaciones         = $salario * $dias / 720;                    // B16 / E16
        $totalPrestaciones  = $prima + $cesantias + $interesesCesantias + $vacaciones; // B17 / E17

        // ── Parafiscales (CCF, ICBF, SENA) — base: salario prorateado ────
        $ccf   = self::conTope($salarioProrateado, $salarioProrateado, $piso, 0.04); // B18
        $icbf  = self::conTope($salarioProrateado, $salarioProrateado, $piso, 0.03); // B19
        $sena  = self::conTope($salarioProrateado, $salarioProrateado, $piso, 0.02); // B20
        $totalParafiscales = $ccf + $icbf + $sena; // B21

        // ── Total costo empresa ───────────────────────────────────────────
        $totalCostosEmpresa = $totalPrestaciones + $totalSegSocialEmp + $totalDevengadoBase + $totalParafiscales; // B22

        // ── Lado del empleado (lo que efectivamente recibe) ───────────────
        $saludEmpleado   = $salarioProrateado * 0.04; // E9
        $pensionEmpleado = $salarioProrateado * 0.04; // E10
        $totalDeduccionesEmpleado = $saludEmpleado + $pensionEmpleado; // E11
        $totalDevengoEmpleado     = $totalDevengadoBase - $totalDeduccionesEmpleado; // E12 (neto)
        $totalCostosEmpleado      = $totalDevengoEmpleado + $totalPrestaciones;      // E18

        $pagoEmpleado  = $totalCostosEmpleado; // E20
        $pagoAcreedor  = $totalParafiscales + $totalSegSocialEmp + $totalDeduccionesEmpleado; // E21 (a EPS/AFP/ARL/Cajas/Gobierno)

        // ── Cálculo de valor hora (no incluido en costos laborales) ──────
        $horaNormal = $salario / 220; // B25
        $horas_calc = [
            'hora_normal'                  => $horaNormal,
            'extra_diurna'                 => ($horaNormal * 1.25) * $horas,
            'extra_nocturna'               => ($horaNormal * 1.75) * $horas,
            'extra_diurna_festiva'         => ($horaNormal * 2.05) * $horas,
            'extra_nocturna_festiva'       => ($horaNormal * 2.55) * $horas,
            'recargo_nocturno'             => ($horaNormal * 0.35 * $horas) + $horaNormal,
            'recargo_dominical'            => ($horaNormal * 0.80 * $horas) + $horaNormal,
            'recargo_dominical_nocturno'   => ($horaNormal * 1.15 * $horas) + $horaNormal,
        ];

        return [
            'entrada' => [
                'salario' => $salario, 'dias' => $dias, 'horas' => $horas,
                'auxilio_transporte' => $auxTransporte, 'tasa_arl' => $tasaArl,
            ],
            'empleador' => [
                'salario_prorateado' => $salarioProrateado,
                'aux_prorateado'     => $auxProrateado,
                'total_base'         => $totalDevengadoBase,
                'salud'              => $saludEmpleador,
                'pension'            => $pensionEmpleador,
                'arl'                => $arl,
                'total_seg_social'   => $totalSegSocialEmp,
                'prima'              => $prima,
                'cesantias'          => $cesantias,
                'intereses_cesantias'=> $interesesCesantias,
                'vacaciones'         => $vacaciones,
                'total_prestaciones' => $totalPrestaciones,
                'ccf'                => $ccf,
                'icbf'               => $icbf,
                'sena'               => $sena,
                'total_parafiscales' => $totalParafiscales,
                'total_costos_empresa' => $totalCostosEmpresa,
            ],
            'empleado' => [
                'salario'            => $salarioProrateado,
                'aux_transporte'     => $auxProrateado,
                'total_devengado'    => $totalDevengadoBase,
                'salud'              => $saludEmpleado,
                'pension'            => $pensionEmpleado,
                'total_deducciones'  => $totalDeduccionesEmpleado,
                'neto_a_pagar'       => $totalDevengoEmpleado,
                'prima'              => $prima,
                'cesantias'          => $cesantias,
                'intereses_cesantias'=> $interesesCesantias,
                'vacaciones'         => $vacaciones,
                'total_prestaciones' => $totalPrestaciones,
                'total_costos_empleado' => $totalCostosEmpleado,
                'pago_empleado'      => $pagoEmpleado,
                'pago_acreedor'      => $pagoAcreedor,
            ],
            'horas' => $horas_calc,
        ];
    }
}
