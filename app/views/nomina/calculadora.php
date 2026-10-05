<div class="page-header">
  <div>
    <div class="page-subtitle">Calcula el costo total de un empleado para la empresa, incluyendo seguridad social, prestaciones y parafiscales</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina" class="btn btn-outline">← Volver a Nómina</a>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Datos de entrada</div></div>
  <form method="GET" action="<?= APP_URL ?>/nomina/calculadora">
    <div class="form-row">
      <label>Empleado (opcional, autocompleta salario y nivel ARL)
        <select id="selEmpleadoCalc" onchange="
          if (!this.value) return;
          var opt = this.options[this.selectedIndex];
          document.getElementById('inpSalario').value = opt.value;
          if (opt.dataset.arl) document.getElementById('selNivelArl').value = opt.dataset.arl;
        ">
          <option value="">— Ingresar salario manualmente —</option>
          <?php foreach ($empleados as $e): ?>
            <option value="<?= (float)$e['salario_base'] ?>" data-arl="<?= (int)$e['arl_nivel_riesgo'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?> (<?= View::money($e['salario_base']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Salario mensual
        <input type="number" id="inpSalario" name="salario" step="1000" min="0" value="<?= (float)$salario ?>" required>
      </label>
      <label>Días laborados (máx. 30)
        <input type="number" name="dias" min="1" max="30" value="<?= (int)$dias ?>" required>
      </label>
    </div>
    <div class="form-row">
      <label>Fecha de ingreso (opcional, recalcula días)
        <input type="date" name="fecha_ingreso" value="<?= View::e($fechaIngreso) ?>">
      </label>
      <label>Fecha de retiro (opcional, recalcula días)
        <input type="date" name="fecha_retiro" value="<?= View::e($fechaRetiro) ?>">
      </label>
      <label>Cantidad de horas extra a valorizar
        <input type="number" name="horas" min="0" value="<?= (int)$horas ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Nivel de riesgo ARL
        <select name="nivel_arl" id="selNivelArl">
          <?php foreach ($nivelesArl as $n): ?>
            <option value="<?= (int)$n['nivel'] ?>" <?= (int)$n['nivel']===$nivelArl?'selected':'' ?>><?= View::e($n['descripcion']) ?> — <?= number_format($n['tasa']*100,3) ?>%</option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <button type="submit" class="btn btn-primary">Calcular</button>
  </form>
</div>

<div class="stats-grid">
  <div class="stat-card stat-total">
    <span class="stat-icon" style="color:var(--primary)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['empleador']['total_costos_empresa']) ?></div>
    <div class="stat-label">Total costo empresa</div>
  </div>
  <div class="stat-card stat-personal">
    <span class="stat-icon" style="color:var(--blue)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['empleado']['pago_empleado']) ?></div>
    <div class="stat-label">Pago al empleado</div>
  </div>
  <div class="stat-card stat-bienestar">
    <span class="stat-icon" style="color:var(--accent)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6 21V9a.75.75 0 01.75-.75h10.5a.75.75 0 01.75.75v12"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['empleado']['pago_acreedor']) ?></div>
    <div class="stat-label">Pago a terceros (EPS, AFP, ARL, Cajas)</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon" style="color:var(--info)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25h5.25M12 14.25v-4.5m5.25 0h-11.25M12 9.75h-1.5m0 0V6.75c0-.621.504-1.125 1.125-1.125h9.75c.621 0 1.125.504 1.125 1.125v2.25"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['entrada']['auxilio_transporte']) ?></div>
    <div class="stat-label">Auxilio de transporte aplicado</div>
  </div>
</div>

<div class="form-row" style="align-items:start">

  <div class="card">
    <div class="card-header"><div class="card-title">Costos a cargo del empleador</div></div>
    <table>
      <tbody>
        <tr><td>Salario (prorateado)</td><td style="text-align:right"><?= View::money($r['empleador']['salario_prorateado']) ?></td></tr>
        <tr><td>Auxilio de transporte (prorateado)</td><td style="text-align:right"><?= View::money($r['empleador']['aux_prorateado']) ?></td></tr>
        <tr><td colspan="2"><hr></td></tr>
        <tr><td>Salud (8,5%)</td><td style="text-align:right"><?= View::money($r['empleador']['salud']) ?></td></tr>
        <tr><td>Pensión (12%)</td><td style="text-align:right"><?= View::money($r['empleador']['pension']) ?></td></tr>
        <tr><td>ARL (<?= number_format($r['entrada']['tasa_arl']*100,3) ?>%)</td><td style="text-align:right"><?= View::money($r['empleador']['arl']) ?></td></tr>
        <tr><td><strong>Subtotal seguridad social</strong></td><td style="text-align:right"><strong><?= View::money($r['empleador']['total_seg_social']) ?></strong></td></tr>
        <tr><td colspan="2"><hr></td></tr>
        <tr><td>Prima de servicio</td><td style="text-align:right"><?= View::money($r['empleador']['prima']) ?></td></tr>
        <tr><td>Cesantías</td><td style="text-align:right"><?= View::money($r['empleador']['cesantias']) ?></td></tr>
        <tr><td>Intereses sobre cesantías</td><td style="text-align:right"><?= View::money($r['empleador']['intereses_cesantias']) ?></td></tr>
        <tr><td>Vacaciones</td><td style="text-align:right"><?= View::money($r['empleador']['vacaciones']) ?></td></tr>
        <tr><td><strong>Subtotal prestaciones</strong></td><td style="text-align:right"><strong><?= View::money($r['empleador']['total_prestaciones']) ?></strong></td></tr>
        <tr><td colspan="2"><hr></td></tr>
        <tr><td>CCF (4%)</td><td style="text-align:right"><?= View::money($r['empleador']['ccf']) ?></td></tr>
        <tr><td>ICBF (3%)</td><td style="text-align:right"><?= View::money($r['empleador']['icbf']) ?></td></tr>
        <tr><td>SENA (2%)</td><td style="text-align:right"><?= View::money($r['empleador']['sena']) ?></td></tr>
        <tr><td><strong>Subtotal parafiscales</strong></td><td style="text-align:right"><strong><?= View::money($r['empleador']['total_parafiscales']) ?></strong></td></tr>
      </tbody>
    </table>
    <div class="colilla-total">
      <span class="colilla-label">TOTAL COSTO EMPRESA</span>
      <span class="colilla-valor"><?= View::money($r['empleador']['total_costos_empresa']) ?></span>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Lado del empleado</div></div>
    <table>
      <tbody>
        <tr><td>Salario</td><td style="text-align:right"><?= View::money($r['empleado']['salario']) ?></td></tr>
        <tr><td>Auxilio de transporte</td><td style="text-align:right"><?= View::money($r['empleado']['aux_transporte']) ?></td></tr>
        <tr><td><strong>Total devengado</strong></td><td style="text-align:right"><strong><?= View::money($r['empleado']['total_devengado']) ?></strong></td></tr>
        <tr><td colspan="2"><hr></td></tr>
        <tr><td>Salud (4%)</td><td style="text-align:right">- <?= View::money($r['empleado']['salud']) ?></td></tr>
        <tr><td>Pensión (4%)</td><td style="text-align:right">- <?= View::money($r['empleado']['pension']) ?></td></tr>
        <tr><td><strong>Total deducciones</strong></td><td style="text-align:right"><strong>- <?= View::money($r['empleado']['total_deducciones']) ?></strong></td></tr>
        <tr><td colspan="2"><hr></td></tr>
        <tr><td>Prima + cesantías + intereses + vacaciones</td><td style="text-align:right"><?= View::money($r['empleado']['total_prestaciones']) ?></td></tr>
      </tbody>
    </table>
    <div class="colilla-total">
      <span class="colilla-label">NETO A PAGAR AL EMPLEADO</span>
      <span class="colilla-valor"><?= View::money($r['empleado']['neto_a_pagar']) ?></span>
    </div>
    <p class="text-muted" style="margin-top:10px;font-size:12px">
      Total costo empleado (neto + prestaciones): <strong><?= View::money($r['empleado']['total_costos_empleado']) ?></strong><br>
      Pago a terceros (EPS/AFP/ARL/Cajas/Gobierno): <strong><?= View::money($r['empleado']['pago_acreedor']) ?></strong>
    </p>
  </div>

</div>

<div class="card">
  <div class="card-header"><div class="card-title">Valor de la hora (no incluido en costos laborales)</div></div>
  <table>
    <thead><tr><th>Concepto</th><th>Valor</th></tr></thead>
    <tbody>
      <tr><td>Hora ordinaria</td><td><?= View::money($r['horas']['hora_normal']) ?></td></tr>
      <tr><td>Hora extra diurna (×1.25)</td><td><?= View::money($r['horas']['extra_diurna']) ?></td></tr>
      <tr><td>Hora extra nocturna (×1.75)</td><td><?= View::money($r['horas']['extra_nocturna']) ?></td></tr>
      <tr><td>Hora extra diurna festiva (×2.05)</td><td><?= View::money($r['horas']['extra_diurna_festiva']) ?></td></tr>
      <tr><td>Hora extra nocturna festiva (×2.55)</td><td><?= View::money($r['horas']['extra_nocturna_festiva']) ?></td></tr>
      <tr><td>Recargo nocturno (+35%)</td><td><?= View::money($r['horas']['recargo_nocturno']) ?></td></tr>
      <tr><td>Recargo dominical (+80%)</td><td><?= View::money($r['horas']['recargo_dominical']) ?></td></tr>
      <tr><td>Recargo dominical nocturno (+115%)</td><td><?= View::money($r['horas']['recargo_dominical_nocturno']) ?></td></tr>
    </tbody>
  </table>
  <p class="text-muted" style="margin-top:8px;font-size:12px">Calculado sobre una base de 220 horas mensuales y la cantidad de horas indicada arriba.</p>
</div>