<div class="page-header">
  <div>
    <div class="page-title">Administración de Personal</div>
    <div class="page-subtitle"><?= count($empleados) ?> empleado(s) encontrados</div>
  </div>
  <div class="page-actions">
    <?php if (Auth::puedeGestionar()): ?>
    <a href="<?= APP_URL ?>/empleados/crear" class="btn btn-primary">+ Nuevo empleado</a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <form method="GET" action="<?= APP_URL ?>/empleados" class="form-row" style="margin-bottom:0">
    <label>Buscar
      <input type="text" name="q" value="<?= View::e($q) ?>" placeholder="Nombre, apellido o documento">
    </label>
    <label>Estado
      <select name="estado">
        <option value="">Todos</option>
        <option value="activo"   <?= $estado==='activo'?'selected':'' ?>>Activo</option>
        <option value="inactivo" <?= $estado==='inactivo'?'selected':'' ?>>Inactivo</option>
        <option value="retirado" <?= $estado==='retirado'?'selected':'' ?>>Retirado</option>
      </select>
    </label>
    <label>Área
      <select name="area">
        <option value="">Todas</option>
        <?php foreach ($areas as $a): ?>
          <option value="<?= $a['id'] ?>" <?= (string)$areaSel === (string)$a['id']?'selected':'' ?>><?= View::e($a['nombre']) ?></option>
        <?php endforeach; ?>
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
      <tr><th>Empleado</th><th>Documento</th><th>Cargo</th><th>Área</th><th>Ingreso</th><th>Checklist</th><th>Estado</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$empleados): ?>
        <tr><td colspan="8" class="text-muted">No se encontraron empleados con los filtros seleccionados.</td></tr>
      <?php endif; ?>
      <?php foreach ($empleados as $e): ?>
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div class="avatar-circle-sm avatar-circle"><?= mb_strtoupper(mb_substr($e['nombres'],0,1)) ?></div>
            <a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?></a>
          </div>
        </td>
        <td><?= View::e($e['tipo_documento'].' '.$e['numero_documento']) ?></td>
        <td><?= View::e($e['cargo'] ?? '—') ?></td>
        <td><?= View::e($e['area'] ?? '—') ?></td>
        <td><?= View::fecha($e['fecha_ingreso']) ?></td>
        <td>
          <div class="progress-bar" style="width:70px"><div class="progress-fill" style="width:<?= (float)$e['pct_checklist'] ?>%"></div></div>
          <span style="font-size:11px;color:var(--muted)"><?= (float)$e['pct_checklist'] ?>%</span>
        </td>
        <td><span class="badge <?= View::estadoEmpleadoBadge($e['estado']) ?>"><?= View::estadoEmpleadoLabel($e['estado']) ?></span></td>
        <td style="white-space:nowrap">
          <a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>" class="btn btn-outline btn-sm">Ver</a>
          <?php if (Auth::puedeGestionar()): ?>
          <a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>/editar" class="btn btn-outline btn-sm">Editar</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
