<div class="page-header">
  <div>
    <div class="page-subtitle"><?= $programa ? View::e($programa['nombre']) : 'No tienes un programa académico asignado' ?></div>
  </div>
  <div class="page-actions">
    <?php if ($programa): ?>
      <a href="<?= APP_URL ?>/solicitudes-vinculacion/crear" class="btn btn-primary">+ Nueva solicitud</a>
    <?php endif; ?>
  </div>
</div>

<?php if (!$programa): ?>
<div class="card">
  <p class="text-muted">Tu usuario no tiene un programa académico asignado todavía. Contacta a un administrador para que te lo asigne y puedas crear solicitudes de vinculación.</p>
</div>
<?php else: ?>

<div class="stats-grid">
  <div class="stat-card stat-personal">
    <div class="stat-num"><?= (int)$empleadosPrograma ?></div>
    <div class="stat-label">Empleados activos en tu programa</div>
  </div>
  <div class="stat-card stat-checklist">
    <div class="stat-num"><?= (int)($conteoSolicitudes['pendientes'] ?? 0) ?></div>
    <div class="stat-label">Solicitudes pendientes</div>
  </div>
  <div class="stat-card stat-nomina">
    <div class="stat-num"><?= (int)($conteoSolicitudes['aprobadas'] ?? 0) ?></div>
    <div class="stat-label">Solicitudes aprobadas</div>
  </div>
  <div class="stat-card stat-bienestar">
    <div class="stat-num"><?= (int)($conteoSolicitudes['rechazadas'] ?? 0) ?></div>
    <div class="stat-label">Solicitudes rechazadas</div>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Tus solicitudes de vinculación recientes</div>
    <a href="<?= APP_URL ?>/solicitudes-vinculacion" class="btn btn-outline btn-sm">Ver todas</a>
  </div>
  <table>
    <thead><tr><th>Candidato</th><th>Cargo sugerido</th><th>Fecha</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php if (!$misSolicitudes): ?>
        <tr><td colspan="5" class="text-muted">Aún no has creado ninguna solicitud de vinculación.</td></tr>
      <?php endif; ?>
      <?php foreach ($misSolicitudes as $s): ?>
      <tr>
        <td><?= View::e($s['nombres_candidato'].' '.$s['apellidos_candidato']) ?></td>
        <td><?= View::e($s['cargo_sugerido'] ?: '—') ?></td>
        <td><?= View::fecha($s['fecha_solicitud']) ?></td>
        <td><span class="badge <?= View::estadoSolicitudBadge($s['estado']) ?>"><?= View::estadoSolicitudLabel($s['estado']) ?></span></td>
        <td><a href="<?= APP_URL ?>/solicitudes-vinculacion/<?= $s['id'] ?>" class="btn btn-outline btn-sm">Ver</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<div class="card">
  <div class="card-header"><div class="card-title">Próximas actividades de bienestar</div>
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline btn-sm">Ver todas</a>
  </div>
  <table>
    <thead><tr><th>Actividad</th><th>Tipo</th><th>Fecha</th><th>Lugar</th></tr></thead>
    <tbody>
      <?php if (!$proximasActividades): ?>
        <tr><td colspan="4" class="text-muted">No hay actividades próximas programadas.</td></tr>
      <?php endif; ?>
      <?php foreach ($proximasActividades as $a): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/bienestar/<?= $a['id'] ?>"><?= View::e($a['nombre']) ?></a></td>
        <td><span class="badge badge-tipo-<?= $a['tipo'] ?>"><?= View::tipoActividadLabel($a['tipo']) ?></span></td>
        <td><?= View::fecha($a['fecha_inicio']) ?></td>
        <td><?= View::e($a['lugar'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
