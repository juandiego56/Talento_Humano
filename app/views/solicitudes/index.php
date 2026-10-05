<div class="page-header">
  <div>
    <div class="page-title">Solicitudes de Vinculación Laboral</div>
    <div class="page-subtitle"><?= count($solicitudes) ?> solicitud(es)</div>
  </div>
  <div class="page-actions">
    <?php if (Auth::esDirectorPrograma()): ?>
    <a href="<?= APP_URL ?>/solicitudes-vinculacion/crear" class="btn btn-primary">+ Nueva solicitud</a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <form method="GET" action="<?= APP_URL ?>/solicitudes-vinculacion" class="form-row" style="margin-bottom:0">
    <label>Estado
      <select name="estado">
        <option value="">Todos</option>
        <option value="pendiente" <?= $estado==='pendiente'?'selected':'' ?>>Pendiente</option>
        <option value="aprobada"  <?= $estado==='aprobada'?'selected':'' ?>>Aprobada</option>
        <option value="rechazada" <?= $estado==='rechazada'?'selected':'' ?>>Rechazada</option>
      </select>
    </label>
    <label style="align-self:end">
      <button type="submit" class="btn btn-outline" style="width:100%">Filtrar</button>
    </label>
  </form>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>Candidato</th><th>Programa</th><th>Cargo sugerido</th><th>Solicitado por</th><th>Fecha</th><th>Estado</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$solicitudes): ?>
        <tr><td colspan="7" class="text-muted">No hay solicitudes registradas.</td></tr>
      <?php endif; ?>
      <?php foreach ($solicitudes as $s): ?>
      <tr>
        <td><?= View::e($s['nombres_candidato'].' '.$s['apellidos_candidato']) ?></td>
        <td><?= View::e($s['programa_nombre']) ?></td>
        <td><?= View::e($s['cargo_sugerido'] ?: '—') ?></td>
        <td><?= View::e($s['solicitante_nombre']) ?></td>
        <td><?= View::fecha($s['fecha_solicitud']) ?></td>
        <td><span class="badge <?= View::estadoSolicitudBadge($s['estado']) ?>"><?= View::estadoSolicitudLabel($s['estado']) ?></span></td>
        <td><a href="<?= APP_URL ?>/solicitudes-vinculacion/<?= $s['id'] ?>" class="btn btn-outline btn-sm">Ver</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
