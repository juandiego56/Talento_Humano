<div class="page-header">
  <div>
    <div class="page-title"><?= View::e($actividad['nombre']) ?></div>
    <div class="page-subtitle">
      <span class="badge badge-tipo-<?= $actividad['tipo'] ?>"><?= View::tipoActividadLabel($actividad['tipo']) ?></span>
      <span class="badge <?= View::estadoActividadBadge($actividad['estado']) ?>" style="margin-left:6px"><?= View::estadoActividadLabel($actividad['estado']) ?></span>
    </div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline">← Volver</a>
    <?php if (Auth::puedeGestionar()): ?>
    <a href="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/editar" class="btn btn-primary">Editar</a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Información de la actividad</div></div>
  <?php if ($actividad['descripcion']): ?><p style="margin-bottom:14px;color:var(--txt2)"><?= nl2br(View::e($actividad['descripcion'])) ?></p><?php endif; ?>
  <div class="dato-grid">
    <div class="dato-item"><div class="dato-label">Fecha</div><div class="dato-valor"><?= View::fecha($actividad['fecha_inicio']) ?><?= $actividad['fecha_fin'] && $actividad['fecha_fin'] !== $actividad['fecha_inicio'] ? ' – '.View::fecha($actividad['fecha_fin']) : '' ?></div></div>
    <div class="dato-item"><div class="dato-label">Hora</div><div class="dato-valor"><?= $actividad['hora_inicio'] ? substr($actividad['hora_inicio'],0,5) : '—' ?></div></div>
    <div class="dato-item"><div class="dato-label">Lugar</div><div class="dato-valor"><?= View::e($actividad['lugar'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Responsable</div><div class="dato-valor"><?= View::e($actividad['responsable'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Cupo</div><div class="dato-valor"><?= count($inscritos) ?><?= $actividad['cupo_maximo'] ? ' / '.$actividad['cupo_maximo'] : ' (sin límite)' ?></div></div>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Asistencia mediante código QR</div></div>
  <p class="text-muted" style="margin-bottom:14px">
    Los empleados escanean este código con su celular, inician sesión (si no lo han hecho) y su asistencia queda registrada automáticamente — sin pasar por un gestor.
  </p>
  <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($checkinUrl) ?>"
         alt="Código QR de asistencia" width="200" height="200" style="border-radius:8px;border:1px solid var(--border,#e5e7eb)">
    <div style="flex:1;min-width:220px">
      <div class="dato-label" style="margin-bottom:4px">Enlace directo</div>
      <div style="font-size:13px;word-break:break-all;background:var(--bg-soft,#f8fafc);padding:8px 10px;border-radius:6px;margin-bottom:12px"><?= View::e($checkinUrl) ?></div>
      <?php if (Auth::puedeGestionar()): ?>
      <form method="POST" action="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/qr/regenerar" onsubmit="return confirm('¿Regenerar el código QR? El código actual dejará de funcionar.')">
        <button type="submit" class="btn btn-outline btn-sm">Regenerar código</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="form-row" style="align-items:start">
  <div class="card" style="grid-column: span 2">
    <div class="card-header"><div class="card-title">Empleados inscritos (<?= count($inscritos) ?>)</div></div>
    <table>
      <thead><tr><th>Empleado</th><th>Cargo</th><th>Inscripción</th><th>Asistió</th><?php if (Auth::puedeGestionar()): ?><th></th><?php endif; ?></tr></thead>
      <tbody>
        <?php if (!$inscritos): ?><tr><td colspan="5" class="text-muted">Aún no hay empleados inscritos.</td></tr><?php endif; ?>
        <?php foreach ($inscritos as $i): ?>
        <tr>
          <td><?= View::e($i['nombres'].' '.$i['apellidos']) ?></td>
          <td><?= View::e($i['cargo'] ?? '—') ?></td>
          <td><?= View::fecha($i['fecha_inscripcion']) ?></td>
          <td>
            <?php if (Auth::puedeGestionar()): ?>
            <form method="POST" action="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/inscripcion/<?= $i['id'] ?>/asistio">
              <button type="submit" class="badge <?= $i['asistio'] ? 'badge-aprobado' : 'badge-borrador' ?>" style="border:none;cursor:pointer">
                <?= $i['asistio'] ? 'Asistió' : 'Marcar asistencia' ?>
              </button>
            </form>
            <?php else: ?>
              <span class="badge <?= $i['asistio'] ? 'badge-aprobado' : 'badge-borrador' ?>"><?= $i['asistio'] ? 'Asistió' : 'Pendiente' ?></span>
            <?php endif; ?>
          </td>
          <?php if (Auth::puedeGestionar()): ?>
          <td>
            <form method="POST" action="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/inscripcion/<?= $i['id'] ?>/eliminar" onsubmit="return confirm('¿Quitar esta inscripción?')">
              <button type="submit" class="btn btn-outline btn-sm">✕</button>
            </form>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if (Auth::puedeGestionar()): ?>
  <div class="card">
    <div class="card-header"><div class="card-title">Inscribir empleado</div></div>
    <?php if (!$disponibles): ?>
      <p class="text-muted">Todos los empleados activos ya están inscritos.</p>
    <?php else: ?>
    <form method="POST" action="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/inscribir">
      <label>Empleado
        <select name="empleado_id" required>
          <?php foreach ($disponibles as $e): ?>
            <option value="<?= $e['id'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit" class="btn btn-primary" style="width:100%;margin-top:8px">Inscribir</button>
    </form>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<?php if (Auth::esAdmin()): ?>
<div class="card" style="border-color:#fecaca;margin-top:20px">
  <div class="card-header"><div class="card-title" style="color:var(--danger)">Zona de riesgo</div></div>
  <form method="POST" action="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar esta actividad y todas sus inscripciones?')">
    <button type="submit" class="btn btn-outline" style="color:var(--danger);border-color:#fecaca">Eliminar actividad</button>
  </form>
</div>
<?php endif; ?>
