<div class="page-header">
  <div>
    <div class="page-subtitle"><?= count($actividades) ?> actividad(es) registradas</div>
  </div>
  <div class="page-actions">
    <?php if (Auth::puedeGestionar()): ?>
    <a href="<?= APP_URL ?>/bienestar/crear" class="btn btn-primary">+ Nueva actividad</a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <form method="GET" action="<?= APP_URL ?>/bienestar" class="form-row" style="margin-bottom:0">
    <label>Tipo
      <select name="tipo">
        <option value="">Todos</option>
        <?php foreach (['capacitacion','recreacion','salud','integracion','deportivo'] as $t): ?>
          <option value="<?= $t ?>" <?= $tipo===$t?'selected':'' ?>><?= View::tipoActividadLabel($t) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Estado
      <select name="estado">
        <option value="">Todos</option>
        <?php foreach (['programada','en_curso','finalizada','cancelada'] as $e): ?>
          <option value="<?= $e ?>" <?= $estado===$e?'selected':'' ?>><?= View::estadoActividadLabel($e) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label style="align-self:end"><button type="submit" class="btn btn-outline" style="width:100%">Filtrar</button></label>
  </form>
</div>

<div class="card">
  <table>
    <thead><tr><th>Actividad</th><th>Tipo</th><th>Fecha</th><th>Lugar</th><th>Inscritos</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php if (!$actividades): ?><tr><td colspan="7" class="text-muted">No hay actividades registradas.</td></tr><?php endif; ?>
      <?php foreach ($actividades as $a): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/bienestar/<?= $a['id'] ?>"><?= View::e($a['nombre']) ?></a></td>
        <td><span class="badge badge-tipo-<?= $a['tipo'] ?>"><?= View::tipoActividadLabel($a['tipo']) ?></span></td>
        <td><?= View::fecha($a['fecha_inicio']) ?><?= $a['fecha_fin'] && $a['fecha_fin'] !== $a['fecha_inicio'] ? ' – '.View::fecha($a['fecha_fin']) : '' ?></td>
        <td><?= View::e($a['lugar'] ?: '—') ?></td>
        <td><?= (int)$a['inscritos'] ?><?= $a['cupo_maximo'] ? ' / '.$a['cupo_maximo'] : '' ?></td>
        <td><span class="badge <?= View::estadoActividadBadge($a['estado']) ?>"><?= View::estadoActividadLabel($a['estado']) ?></span></td>
        <td>
          <a href="<?= APP_URL ?>/bienestar/<?= $a['id'] ?>" class="btn btn-outline btn-sm">Ver</a>
          <?php if (Auth::puedeGestionar()): ?>
          <a href="<?= APP_URL ?>/bienestar/<?= $a['id'] ?>/editar" class="btn btn-outline btn-sm">Editar</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>