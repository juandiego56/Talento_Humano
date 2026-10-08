<div class="page-header">
  <div>
    <div class="page-subtitle"><?= (int)$total ?> empleado(s) encontrados</div>
  </div>
  <div class="page-actions">
    <?php if (Auth::puedeGestionar()): ?>
    <a href="<?= APP_URL ?>/empleados/crear" class="btn btn-primary">+ Nuevo empleado</a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <form method="GET" action="<?= APP_URL ?>/empleados" class="form-row" style="margin-bottom:0" data-autofiltro>
    <label>Buscar
      <input type="text" name="q" value="<?= View::e($q) ?>" placeholder="Nombre, apellido o documento">
    </label>
    <label>Estado
      <select name="estado">
        <option value="">Todos</option>
        <option value="activo"   <?= $estado==='activo'?'selected':'' ?>>Activo</option>
        <option value="inactivo" <?= $estado==='inactivo'?'selected':'' ?>>Inactivo</option>
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
    <?php if (Auth::esDirectorPrograma()): ?>
      <input type="hidden" name="programa" value="<?= View::e($programaSel) ?>">
    <?php else: ?>
    <label>Programa académico
      <select name="programa">
        <option value="">Todos</option>
        <?php foreach ($programas as $p): ?>
          <option value="<?= $p['id'] ?>" <?= (string)$programaSel === (string)$p['id']?'selected':'' ?>><?= View::e($p['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php endif; ?>
    <label style="align-self:end">
      <button type="submit" class="btn btn-primary btn-filtrar" style="width:100%">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        Buscar
      </button>
    </label>
    <?php if ($q !== '' || $estado !== '' || $areaSel !== '' || (!Auth::esDirectorPrograma() && $programaSel !== '')): ?>
    <label style="align-self:end">
      <a href="<?= APP_URL ?>/empleados" class="btn btn-outline" style="width:100%">Limpiar</a>
    </label>
    <?php endif; ?>
  </form>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>Empleado</th><th>Documento</th><th>Cargo</th><th>Área</th><th>Programa</th><th>Ingreso</th><th>Checklist</th><th>Estado</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$empleados): ?>
        <tr><td colspan="9" class="text-muted">No se encontraron empleados con los filtros seleccionados.</td></tr>
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
        <td><?= View::e($e['programa'] ?? '—') ?></td>
        <td><?= View::fecha($e['fecha_ingreso']) ?></td>
        <td>
          <div class="progress-bar" style="width:70px"><div class="progress-fill" style="width:<?= (float)$e['pct_checklist'] ?>%"></div></div>
          <span style="font-size:11px;color:var(--muted)"><?= (float)$e['pct_checklist'] ?>%</span>
        </td>
        <td>
          <span class="badge <?= View::estadoEmpleadoBadge($e['estado']) ?>"><?= View::estadoEmpleadoLabel($e['estado']) ?></span>
          <div style="margin-top:4px"><span class="badge <?= View::hojaVidaEstadoBadge($e['hojavida_estado'] ?? null) ?>" style="font-size:10px;white-space:nowrap" title="Estado de la hoja de vida">HV: <?= View::hojaVidaEstadoLabel($e['hojavida_estado'] ?? null) ?></span></div>
        </td>
        <td style="white-space:nowrap">
          <a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>" class="btn btn-outline btn-sm">Ver</a>
          <?php if (Auth::puedeGestionar()): ?>
          <a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>/editar" class="btn btn-outline btn-sm">Editar</a>
          <?php if ($e['estado'] !== 'retirado'):
              $nuevoEstado = $e['estado'] === 'activo' ? 'inactivo' : 'activo';
              $nombreEmp   = $e['nombres'] . ' ' . $e['apellidos'];
              $msgEstado   = ($nuevoEstado === 'inactivo' ? '¿Inactivar a ' : '¿Activar a ') . $nombreEmp . '?';
          ?>
          <form method="POST" action="<?= APP_URL ?>/empleados/<?= $e['id'] ?>/estado" style="display:inline"
                onsubmit="return confirm(<?= View::e(json_encode($msgEstado, JSON_UNESCAPED_UNICODE)) ?>)">
            <input type="hidden" name="estado" value="<?= $nuevoEstado ?>">
            <?php if ($nuevoEstado === 'inactivo'): ?>
              <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger);border-color:#fecaca">Inactivar</button>
            <?php else: ?>
              <button type="submit" class="btn btn-outline btn-sm">Activar</button>
            <?php endif; ?>
          </form>
          <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php View::partial('layouts._paginacion', ['pagina' => $pagina, 'paginas' => $paginas, 'total' => $total, 'porPagina' => $porPagina]); ?>
