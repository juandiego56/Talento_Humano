<div class="page-header">
  <div>
    <div class="page-title">Nómina</div>
    <div class="page-subtitle"><?= $empleadosActivos ?> empleado(s) activo(s) que se incluirán al generar</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina/calculadora" class="btn btn-outline">🧮 Calculadora de Costos Laborales</a>
  </div>
</div>

<?php if (Auth::puedeGestionar()): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Generar nómina de un periodo</div></div>
  <form method="POST" action="<?= APP_URL ?>/nomina/generar" class="form-row" onsubmit="return confirm('Se calculará la nómina automáticamente para todos los empleados activos. ¿Continuar?')">
    <label>Periodo (Año-Mes)
      <input type="month" name="periodo" value="<?= $periodoSugerido ?>" required>
    </label>
    <label style="align-self:end">
      <button type="submit" class="btn btn-primary" style="width:100%">Generar nómina</button>
    </label>
  </form>
  <p class="text-muted" style="margin-top:8px;font-size:12px">
    Se calculará automáticamente el salario básico, auxilio de transporte, salud y pensión de cada empleado activo.
    Horas extra, bonificaciones u otros descuentos se agregan luego desde la colilla individual de cada empleado.
  </p>
</div>
<?php endif; ?>

<div class="card">
  <table>
    <thead><tr><th>Periodo</th><th>Empleados</th><th>Devengado</th><th>Deducciones</th><th>Neto a pagar</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php if (!$nominas): ?><tr><td colspan="7" class="text-muted">Aún no se ha generado ninguna nómina.</td></tr><?php endif; ?>
      <?php foreach ($nominas as $n): ?>
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
    </tbody>
  </table>
</div>
