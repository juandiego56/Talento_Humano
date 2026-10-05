<?php $qs = http_build_query($filtros); ?>
<div class="page-header">
  <div>
    <div class="page-subtitle">Reporte de personal con filtros y exportación</div>
  </div>
  <div class="page-actions">
    <div class="btn-group">
      <a href="<?= APP_URL ?>/reportes/exportar/csv?<?= $qs ?>" class="btn btn-outline btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:12px;height:12px">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        CSV
      </a>
      <a href="<?= APP_URL ?>/reportes/exportar/excel?<?= $qs ?>" class="btn btn-outline btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:12px;height:12px">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        Excel
      </a>
      <a href="<?= APP_URL ?>/reportes/exportar/pdf?<?= $qs ?>" class="btn btn-outline btn-sm" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width:12px;height:12px">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        PDF
      </a>
    </div>
  </div>
</div>

<div class="card" style="margin-bottom:16px">
  <form method="GET" action="<?= APP_URL ?>/reportes" class="form-row">
    <label>Programa académico
      <select name="programa_id" onchange="this.form.submit()">
        <option value="">Todos los programas</option>
        <?php foreach ($programas as $p): ?>
          <option value="<?= $p['id'] ?>" <?= (string)$filtros['programa_id'] === (string)$p['id'] ? 'selected' : '' ?>><?= View::e($p['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Estado
      <select name="estado" onchange="this.form.submit()">
        <option value="" <?= $filtros['estado'] === '' ? 'selected' : '' ?>>Todos</option>
        <option value="activo" <?= $filtros['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="inactivo" <?= $filtros['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        <option value="retirado" <?= $filtros['estado'] === 'retirado' ? 'selected' : '' ?>>Retirado</option>
      </select>
    </label>
    <?php if ($filtros['programa_id'] !== '' || $filtros['estado'] !== 'activo'): ?>
      <label style="align-self:end"><a href="<?= APP_URL ?>/reportes" class="btn btn-outline">Quitar filtros</a></label>
    <?php endif; ?>
  </form>
</div>

<div class="stats-grid">
  <div class="stat-card stat-personal">
    <span class="stat-icon" style="color:var(--blue)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
    </span>
    <div class="stat-num"><?= count($empleados) ?></div>
    <div class="stat-label">Empleados en el reporte</div>
  </div>
  <div class="stat-card stat-nomina">
    <span class="stat-icon" style="color:var(--success)">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" style="width:22px;height:22px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3L2 8l10 5 10-5-10-5z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 10.5V16c0 1.5 2.7 3 6 3s6-1.5 6-3v-5.5"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M22 8v6"/>
      </svg>
    </span>
    <div class="stat-num"><?= array_sum(array_column($cualificacion, 'total')) ?></div>
    <div class="stat-label">Docentes con dedicación registrada</div>
  </div>
</div>

<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Cualificación docente (nivel de formación)</div></div>
  <?php if (!$cualificacion): ?>
    <p class="text-muted">No hay docentes que coincidan con el filtro seleccionado.</p>
  <?php else: ?>
    <?php $max = max(array_column($cualificacion, 'total')); ?>
    <div style="display:flex;flex-direction:column;gap:10px">
      <?php foreach ($cualificacion as $c): ?>
        <?php $pct = $max > 0 ? round($c['total'] / $max * 100) : 0; ?>
        <div>
          <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:3px">
            <span><?= View::e($c['etiqueta']) ?></span>
            <strong><?= $c['total'] ?></strong>
          </div>
          <div style="background:var(--border,#e5e7eb);border-radius:6px;height:14px;overflow:hidden">
            <div style="background:var(--primary,#1c5fa8);height:100%;width:<?= $pct ?>%;border-radius:6px"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Personal (<?= count($empleados) ?>)</div></div>
  <table>
    <thead><tr><th>Nombre</th><th>Documento</th><th>Cargo</th><th>Área</th><th>Programa</th><th>Nivel de formación</th><th>Dedicación docente</th><th>Estado</th></tr></thead>
    <tbody>
      <?php if (!$empleados): ?><tr><td colspan="8" class="text-muted">Sin resultados para el filtro seleccionado.</td></tr><?php endif; ?>
      <?php foreach ($empleados as $e): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/empleados/<?= $e['id'] ?>"><?= View::e($e['nombres'] . ' ' . $e['apellidos']) ?></a></td>
        <td><?= View::e($e['numero_documento']) ?></td>
        <td><?= View::e($e['cargo'] ?? '—') ?></td>
        <td><?= View::e($e['area'] ?? '—') ?></td>
        <td><?= View::e($e['programa'] ?? '—') ?></td>
        <td><?= $e['nivel_educativo'] ? View::nivelEducativoLabel($e['nivel_educativo']) : '—' ?></td>
        <td><?= View::tipoVinculacionDocenteLabel($e['tipo_vinculacion_docente']) ?></td>
        <td><span class="badge <?= View::estadoEmpleadoBadge($e['estado']) ?>"><?= View::estadoEmpleadoLabel($e['estado']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
