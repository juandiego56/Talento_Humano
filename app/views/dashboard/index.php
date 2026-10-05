<div class="page-header">
  <div>
    <div class="page-subtitle">Resumen general de los 3 módulos del sistema</div>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card stat-personal">
    <span class="stat-icon" style="color:var(--blue)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </span>
    <div class="stat-num"><?= (int)$empStats['activos'] ?></div>
    <div class="stat-label">Empleados activos</div>
  </div>
  <div class="stat-card stat-checklist">
    <span class="stat-icon" style="color:var(--info)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </span>
    <div class="stat-num"><?= $checklistProm ?>%</div>
    <div class="stat-label">Avance lista de chequeo (prom.)</div>
  </div>
  <div class="stat-card stat-nomina">
    <span class="stat-icon" style="color:var(--success)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </span>
    <div class="stat-num"><?= $ultimaNomina ? View::money($ultimaNomina['total_neto']) : '—' ?></div>
    <div class="stat-label">Última nómina neta<?= $ultimaNomina ? ' (' . View::periodoLabel($ultimaNomina['periodo']) . ')' : '' ?></div>
  </div>
  <div class="stat-card stat-bienestar">
    <span class="stat-icon" style="color:var(--accent)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
      </svg>
    </span>
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