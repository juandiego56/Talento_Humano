<div class="page-header">
  <div>
    <div class="page-subtitle">Recopila el concepto "Horas extra" agregado en las colillas de pago de cada empleado</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/nomina" class="btn btn-outline">← Volver a Nómina</a>
  </div>
</div>

<div class="card" style="margin-bottom:16px">
  <form method="GET" action="<?= APP_URL ?>/nomina/horas-extra" class="form-row">
    <label>Periodo
      <select name="periodo" onchange="this.form.submit()">
        <option value="">Todos los periodos</option>
        <?php foreach ($periodos as $p): ?>
          <option value="<?= View::e($p['periodo']) ?>" <?= $periodoFiltro === $p['periodo'] ? 'selected' : '' ?>><?= View::periodoLabel($p['periodo']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Corte académico
      <select name="corte" onchange="this.form.submit()">
        <option value="">Todos los cortes</option>
        <?php foreach ($cortes as $c): ?>
          <option value="<?= View::e($c['corte_academico']) ?>" <?= $corteFiltro === $c['corte_academico'] ? 'selected' : '' ?>><?= View::e($c['corte_academico']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php if ($periodoFiltro !== '' || $corteFiltro !== ''): ?>
      <label style="align-self:end"><a href="<?= APP_URL ?>/nomina/horas-extra" class="btn btn-outline">Quitar filtros</a></label>
    <?php endif; ?>
  </form>
</div>

<div class="stats-grid">
  <div class="stat-card stat-nomina">
    <span class="stat-icon" style="color:var(--success)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <circle cx="12" cy="12" r="9"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3.5 2"/>
      </svg>
    </span>
    <div class="stat-num" style="font-size:18px"><?= View::money($totalGeneral) ?></div>
    <div class="stat-label">Total pagado en horas extra</div>
  </div>
  <div class="stat-card stat-personal">
    <span class="stat-icon" style="color:var(--blue)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </span>
    <div class="stat-num"><?= count($porEmpleado) ?></div>
    <div class="stat-label">Empleados con horas extra</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon" style="color:var(--info)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h10v18l-2.5-1.5L12 21l-2.5-1.5L7 21V3z"/>
        <path stroke-linecap="round" d="M9.5 8h5M9.5 11.5h5M9.5 15h3"/>
      </svg>
    </span>
    <div class="stat-num"><?= count($filas) ?></div>
    <div class="stat-label">Registros de horas extra</div>
  </div>
</div>

<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Resumen por empleado</div></div>
  <table>
    <thead><tr><th>Empleado</th><th>Cargo</th><th>Área</th><th>Veces</th><th>Total pagado</th></tr></thead>
    <tbody>
      <?php if (!$porEmpleado): ?><tr><td colspan="5" class="text-muted">Sin registros de horas extra para el filtro seleccionado.</td></tr><?php endif; ?>
      <?php foreach ($porEmpleado as $r): ?>
      <tr>
        <td><?= View::e($r['nombre']) ?></td>
        <td><?= View::e($r['cargo'] ?? '—') ?></td>
        <td><?= View::e($r['area'] ?? '—') ?></td>
        <td><?= (int)$r['veces'] ?></td>
        <td><strong><?= View::money($r['total']) ?></strong></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Detalle por periodo</div></div>
  <table>
    <thead><tr><th>Periodo</th><th>Corte académico</th><th>Empleado</th><th>Documento</th><th>Cargo</th><th>Valor</th><th></th></tr></thead>
    <tbody>
      <?php if (!$filas): ?><tr><td colspan="7" class="text-muted">Sin registros de horas extra para el filtro seleccionado.</td></tr><?php endif; ?>
      <?php foreach ($filas as $f): ?>
      <tr>
        <td><?= View::periodoLabel($f['periodo']) ?></td>
        <td><?= $f['corte_academico'] ? View::e($f['corte_academico']) : '—' ?></td>
        <td><?= View::e($f['nombres'] . ' ' . $f['apellidos']) ?></td>
        <td><?= View::e($f['numero_documento']) ?></td>
        <td><?= View::e($f['cargo'] ?? '—') ?></td>
        <td><strong><?= View::money($f['valor']) ?></strong></td>
        <td><a href="<?= APP_URL ?>/nomina/<?= $f['nomina_id'] ?>/detalle/<?= $f['detalle_id'] ?>" class="btn btn-outline btn-sm">Ver colilla</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
