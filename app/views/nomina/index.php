<div class="page-header">
  <div>
    <div class="page-subtitle"><?= $empleadosActivos ?> empleado(s) activo(s) que se incluirán al generar</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina/horas-extra" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:14px;height:14px">
        <circle cx="12" cy="12" r="9"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3.5 2"/>
      </svg>
      Reporte de Horas Extra
    </a>
    <a href="<?= APP_URL ?>/nomina/calculadora" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:14px;height:14px">
        <rect x="5" y="3" width="14" height="18" rx="2"/>
        <path stroke-linecap="round" d="M8 7.5h8"/>
        <circle cx="8.5" cy="12" r=".6" fill="currentColor" stroke="none"/>
        <circle cx="12" cy="12" r=".6" fill="currentColor" stroke="none"/>
        <circle cx="15.5" cy="12" r=".6" fill="currentColor" stroke="none"/>
        <circle cx="8.5" cy="15.5" r=".6" fill="currentColor" stroke="none"/>
        <circle cx="12" cy="15.5" r=".6" fill="currentColor" stroke="none"/>
        <circle cx="15.5" cy="15.5" r=".6" fill="currentColor" stroke="none"/>
      </svg>
      Calculadora de Costos Laborales
    </a>
  </div>
</div>

<?php if (Auth::puedeGestionar()): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Generar nómina de un periodo</div></div>
  <form method="POST" action="<?= APP_URL ?>/nomina/generar" class="form-row" onsubmit="return confirm('Se calculará la nómina automáticamente para todos los empleados activos. ¿Continuar?')">
    <label>Periodo (Año-Mes)
      <input type="month" name="periodo" id="inpPeriodoGenerar" value="<?= $periodoSugerido ?>" required>
    </label>
    <label>Corte académico
      <input type="text" name="corte_academico" id="inpCorteGenerar" value="<?= View::e($corteSugerido) ?>" placeholder="Ej. 2025-2" pattern="\d{4}-\d+">
    </label>
    <label style="align-self:end">
      <button type="submit" class="btn btn-primary" style="width:100%">Generar nómina</button>
    </label>
  </form>
  <p class="text-muted" style="margin-top:8px;font-size:12px">
    Se calculará automáticamente el salario básico, auxilio de transporte, salud y pensión de cada empleado activo.
    Horas extra, bonificaciones u otros descuentos se agregan luego desde la colilla individual de cada empleado.
    El corte académico (ej. "2025-2") agrupa varios periodos mensuales bajo un mismo semestre — se sugiere automáticamente según el periodo, pero puedes editarlo.
  </p>
  <script>
  (function(){
    var inpPeriodo = document.getElementById('inpPeriodoGenerar');
    var inpCorte   = document.getElementById('inpCorteGenerar');
    if (!inpPeriodo || !inpCorte) return;
    inpPeriodo.addEventListener('change', function(){
      var v = this.value; // YYYY-MM
      var partes = v.split('-');
      if (partes.length !== 2) return;
      var anio = partes[0], mes = parseInt(partes[1], 10);
      inpCorte.value = anio + '-' + (mes <= 6 ? '1' : '2');
    });
  })();
  </script>
</div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <form method="GET" action="<?= APP_URL ?>/nomina" class="form-row">
    <label>Filtrar por corte académico
      <select name="corte" onchange="this.form.submit()">
        <option value="">Todos los cortes</option>
        <?php foreach ($cortes as $c): ?>
          <option value="<?= View::e($c['corte_academico']) ?>" <?= $corteFiltro === $c['corte_academico'] ? 'selected' : '' ?>>
            <?= View::e($c['corte_academico']) ?> — <?= View::corteAcademicoLabel($c['corte_academico']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php if ($corteFiltro !== ''): ?>
      <label style="align-self:end"><a href="<?= APP_URL ?>/nomina" class="btn btn-outline">Quitar filtro</a></label>
    <?php endif; ?>
  </form>
</div>

<?php
  // Agrupa las nóminas por corte académico (los datos ya vienen ordenados por corte DESC, periodo DESC)
  $grupos = [];
  foreach ($nominas as $n) {
      $clave = $n['corte_academico'] ?: '';
      $grupos[$clave][] = $n;
  }
?>

<?php if (!$nominas): ?>
  <div class="card"><p class="text-muted">Aún no se ha generado ninguna nómina.</p></div>
<?php endif; ?>

<?php foreach ($grupos as $corte => $filas): ?>
<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div class="card-title">
      <?= $corte !== '' ? View::e($corte) . ' — ' . View::corteAcademicoLabel($corte) : 'Sin corte académico asignado' ?>
    </div>
  </div>
  <table>
    <thead><tr><th>Periodo</th><th>Empleados</th><th>Devengado</th><th>Deducciones</th><th>Neto a pagar</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($filas as $n): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/nomina/<?= $n['id'] ?>"><strong><?= View::periodoLabel($n['periodo']) ?></strong></a></td>
        <td><?= (int)$n['num_empleados'] ?></td>
        <td><?= View::money($n['total_devengado']) ?></td>
        <td><?= View::money($n['total_deducciones']) ?></td>
        <td><strong><?= View::money($n['total_neto']) ?></strong></td>
        <td><span class="badge <?= View::estadoNominaBadge($n['estado']) ?>"><?= View::estadoNominaLabel($n['estado']) ?></span></td>
        <td><a href="<?= APP_URL ?>/nomina/<?= $n['id'] ?>" class="btn btn-outline btn-sm">Ver detalle</a></td>
      </tr>
      <?php endforeach; ?>
      <?php
        $subDev = array_sum(array_column($filas, 'total_devengado'));
        $subDed = array_sum(array_column($filas, 'total_deducciones'));
        $subNeto = array_sum(array_column($filas, 'total_neto'));
      ?>
      <tr style="background:var(--surface-alt,#f8fafc)">
        <td colspan="2"><strong>Subtotal del corte</strong></td>
        <td><strong><?= View::money($subDev) ?></strong></td>
        <td><strong><?= View::money($subDed) ?></strong></td>
        <td><strong><?= View::money($subNeto) ?></strong></td>
        <td colspan="2"></td>
      </tr>
    </tbody>
  </table>
</div>
<?php endforeach; ?>