<div class="page-header">
  <div>
    <div class="page-title">Calculadora de Costos Laborales</div>
    <div class="page-subtitle">Simulador de costo total de un empleado para la empresa (seguridad social, prestaciones y parafiscales)</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina" class="btn btn-outline">← Volver a Nómina</a>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Datos de entrada</div></div>
  <form method="GET" action="<?= APP_URL ?>/nomina/calculadora">
    <div class="form-row">
      <label>Empleado (opcional, autocompleta el salario)
        <select onchange="if(this.value) document.getElementById('inpSalario').value = this.value">
          <option value="">— Ingresar salario manualmente —</option>
          <?php foreach ($empleados as $e): ?>
            <option value="<?= (float)$e['salario_base'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?> (<?= View::money($e['salario_base']) ?>)</option>
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
    <button type="submit" class="btn btn-primary">Calcular</button>
  </form>
</div>

<div class="stats-grid">
  <div class="stat-card stat-alerta">
    <span class="stat-icon">🏢</span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['empleador']['total_costos_empresa']) ?></div>
    <div class="stat-label">Total costo empresa</div>
  </div>
  <div class="stat-card stat-nomina">
    <span class="stat-icon">👤</span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['empleado']['pago_empleado']) ?></div>
    <div class="stat-label">Pago al empleado</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon">🏦</span>
    <div class="stat-num" style="font-size:18px"><?= View::money($r['empleado']['pago_acreedor']) ?></div>
    <div class="stat-label">Pago a terceros (EPS, AFP, ARL, Cajas)</div>
  </div>
  <div class="stat-card stat-personal">
    <span class="stat-icon">🚌</span>
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
        <tr><td>ARL (0,522%)</td><td style="text-align:right"><?= View::money($r['empleador']['arl']) ?></td></tr>
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
