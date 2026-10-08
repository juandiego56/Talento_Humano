<div class="page-header">
  <div>
    <div class="page-subtitle"><?= $empleado ? 'Bienvenido, ' . View::e($empleado['nombres']) : 'Tu usuario no está vinculado a un registro de empleado' ?></div>
  </div>
</div>

<?php if (!$empleado): ?>
<div class="card">
  <p class="text-muted">Tu usuario no está vinculado a un registro de empleado todavía. Contacta a Talento Humano si crees que esto es un error.</p>
</div>
<?php else: ?>

<?php $hvEstado = $empleado['hojavida_estado'] ?? 'borrador'; ?>
<div class="card">
  <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
    <div class="card-title">Tu hoja de vida</div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <span class="badge <?= View::hojaVidaEstadoBadge($hvEstado) ?>"><?= View::hojaVidaEstadoLabel($hvEstado) ?></span>
      <span class="badge badge-borrador">Versión <?= (int)($empleado['hojavida_version'] ?? 1) ?></span>
      <?php if (!empty($empleado['hojavida_fecha'])): ?><span class="badge badge-borrador">Fecha: <?= View::fecha($empleado['hojavida_fecha']) ?></span><?php endif; ?>
    </div>
  </div>
  <?php if ($hvEstado === 'devuelta' && !empty($empleado['hojavida_observaciones'])): ?>
    <div class="alert alert-error"><span><strong>Talento Humano te devolvió la hoja de vida:</strong> <?= nl2br(View::e($empleado['hojavida_observaciones'])) ?></span></div>
  <?php elseif ($hvEstado === 'enviada'): ?>
    <p class="text-muted" style="margin-bottom:10px">Enviada. Talento Humano la está revisando.</p>
  <?php elseif ($hvEstado === 'aprobada'): ?>
    <p class="text-muted" style="margin-bottom:10px">Aprobada por Talento Humano. Si tus datos cambian, puedes actualizarla y se genera una nueva versión.</p>
  <?php endif; ?>
  <a href="<?= APP_URL ?>/mi-hoja-de-vida" class="btn btn-primary btn-sm"><?= in_array($hvEstado, ['borrador', 'devuelta'], true) ? 'Diligenciar / corregir mi hoja de vida' : 'Ver mi hoja de vida' ?></a>
</div>

<div class="form-row" style="align-items:start">
  <div class="card">
    <div class="card-header"><div class="card-title">Tu información</div></div>
    <div class="dato-grid">
      <div class="dato-item"><div class="dato-label">Cargo</div><div class="dato-valor"><?= View::e($empleado['cargo'] ?? '—') ?></div></div>
      <div class="dato-item"><div class="dato-label">Área</div><div class="dato-valor"><?= View::e($empleado['area'] ?? '—') ?></div></div>
      <div class="dato-item"><div class="dato-label">Fecha de ingreso</div><div class="dato-valor"><?= View::fecha($empleado['fecha_ingreso']) ?></div></div>
      <div class="dato-item"><div class="dato-label">Estado</div><div class="dato-valor"><span class="badge <?= View::estadoEmpleadoBadge($empleado['estado']) ?>"><?= View::estadoEmpleadoLabel($empleado['estado']) ?></span></div></div>
    </div>
    <div style="margin-top:14px">
      <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>" class="btn btn-outline btn-sm">Ver mi perfil completo</a>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Tu lista de chequeo</div></div>
    <?php $pct = (float)($checklist['pct_completitud'] ?? 0); ?>
    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px">
      <span>Avance</span><strong><?= $pct ?>%</strong>
    </div>
    <div class="progress-bar" style="width:100%">
      <div class="progress-fill" style="width:<?= min(100, $pct) ?>%"></div>
    </div>
    <div style="margin-top:14px">
      <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist" class="btn btn-primary btn-sm">
        <?= $pct >= 100 ? 'Ver mi lista de chequeo' : 'Completar mi lista de chequeo' ?>
      </a>
    </div>
  </div>
</div>

<?php endif; ?>

<div class="card">
  <div class="card-header"><div class="card-title">Próximas actividades de bienestar</div>
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline btn-sm">Ver todas</a>
  </div>
  <p class="text-muted" style="margin-bottom:12px">Inscríbete o escanea el código QR de la actividad para registrar tu asistencia.</p>
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
