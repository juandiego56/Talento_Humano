<div class="page-header">
  <div>
    <div class="page-title">Tablero de Talento Humano</div>
    <div class="page-subtitle">Resumen general de los 3 módulos del sistema</div>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card stat-personal">
    <span class="stat-icon">👥</span>
    <div class="stat-num"><?= (int)$empStats['activos'] ?></div>
    <div class="stat-label">Empleados activos</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon">📋</span>
    <div class="stat-num"><?= $checklistProm ?>%</div>
    <div class="stat-label">Avance lista de chequeo (prom.)</div>
  </div>
  <div class="stat-card stat-nomina">
    <span class="stat-icon">💵</span>
    <div class="stat-num"><?= $ultimaNomina ? View::money($ultimaNomina['total_neto']) : '—' ?></div>
    <div class="stat-label">Última nómina neta<?= $ultimaNomina ? ' (' . View::periodoLabel($ultimaNomina['periodo']) . ')' : '' ?></div>
  </div>
  <div class="stat-card stat-bienestar">
    <span class="stat-icon">🎉</span>
    <div class="stat-num"><?= (int)$bienestarStats['programadas'] ?></div>
    <div class="stat-label">Actividades de bienestar programadas</div>
  </div>
</div>

<div class="form-row" style="align-items:start">

  <div class="card" style="grid-column: span 2">
    <div class="card-header"><div class="card-title">Empleados recientes</div>
      <a href="<?= APP_URL ?>/empleados" class="btn btn-outline btn-sm">Ver todos</a>
    </div>
    <table>
      <thead><tr><th>Nombre</th><th>Cargo</th><th>Área</th><th>Ingreso</th><th>Estado</th></tr></thead>
      <tbody>
        <?php if (!$empleadosRecientes): ?>
          <tr><td colspan="5" class="text-muted">Aún no hay empleados registrados.</td></tr>
        <?php endif; ?>
        <?php foreach ($empleadosRecientes as $e): ?>
        <tr>
          <td><a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?></a></td>
          <td><?= View::e($e['cargo'] ?? '—') ?></td>
          <td><?= View::e($e['area'] ?? '—') ?></td>
          <td><?= View::fecha($e['fecha_ingreso']) ?></td>
          <td><span class="badge <?= View::estadoEmpleadoBadge($e['estado']) ?>"><?= View::estadoEmpleadoLabel($e['estado']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Empleados por área</div></div>
    <?php if (!$porArea): ?>
      <p class="text-muted">Sin datos.</p>
    <?php endif; ?>
    <?php foreach ($porArea as $a): ?>
      <div style="margin-bottom:10px">
        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:3px">
          <span><?= View::e($a['area']) ?></span><strong><?= (int)$a['total'] ?></strong>
        </div>
        <div class="progress-bar" style="width:100%">
          <div class="progress-fill" style="width:<?= min(100, (int)$a['total'] * 12) ?>%"></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<div class="card">
  <div class="card-header"><div class="card-title">Próximas actividades de bienestar</div>
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline btn-sm">Ver todas</a>
  </div>
  <table>
    <thead><tr><th>Actividad</th><th>Tipo</th><th>Fecha</th><th>Lugar</th><th>Estado</th></tr></thead>
    <tbody>
      <?php if (!$proximasActividades): ?>
        <tr><td colspan="5" class="text-muted">No hay actividades próximas programadas.</td></tr>
      <?php endif; ?>
      <?php foreach ($proximasActividades as $a): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/bienestar/<?= $a['id'] ?>"><?= View::e($a['nombre']) ?></a></td>
        <td><span class="badge badge-tipo-<?= $a['tipo'] ?>"><?= View::tipoActividadLabel($a['tipo']) ?></span></td>
        <td><?= View::fecha($a['fecha_inicio']) ?></td>
        <td><?= View::e($a['lugar'] ?? '—') ?></td>
        <td><span class="badge <?= View::estadoActividadBadge($a['estado']) ?>"><?= View::estadoActividadLabel($a['estado']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
