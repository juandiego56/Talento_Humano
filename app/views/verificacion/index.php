<style>
  .vf-wrap { display:grid; grid-template-columns: 360px 1fr; gap:16px; align-items:start; }
  @media (max-width: 900px) { .vf-wrap { grid-template-columns: 1fr; } }
  .vf-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
  .vf-tab { padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; text-decoration:none; border:1px solid var(--border); color:var(--txt2); }
  .vf-tab.active { background:var(--primary); border-color:var(--primary); color:#fff; }
  .vf-list { max-height:70vh; overflow-y:auto; padding:0; }
  .vf-item { display:block; padding:12px 14px; border-bottom:1px solid var(--border); text-decoration:none; color:inherit; }
  .vf-item:hover { background:#f8fafc; }
  .vf-item.selected { background:#eef4fb; border-left:3px solid var(--primary); }
  .vf-item-doc { font-size:13px; font-weight:600; }
  .vf-item-emp { font-size:12px; color:var(--txt2); margin-top:2px; }
  .vf-item-meta { font-size:11px; color:var(--txt2); margin-top:4px; display:flex; justify-content:space-between; align-items:center; gap:6px; }
  .vf-empty { padding:30px; text-align:center; color:var(--txt2); font-size:13px; }
  .vf-preview { width:100%; height:65vh; border:1px solid var(--border); border-radius:8px; }
  .vf-detail-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:12px; flex-wrap:wrap; }
  .vf-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:14px; }
</style>

<div class="page-header">
  <div>
    <div class="page-title">Verificación de Documentos</div>
    <div class="page-subtitle">Revisa los soportes cargados en el checklist FO-TH-027 y apruébalos o devuélvelos.</div>
  </div>
</div>

<div class="vf-tabs">
  <?php
    $tabs = [
      'pendiente' => 'Pendientes ('.(int)($conteos['pendientes'] ?? 0).')',
      'aprobado'  => 'Aprobados ('.(int)($conteos['aprobados'] ?? 0).')',
      'devuelto'  => 'Devueltos ('.(int)($conteos['devueltos'] ?? 0).')',
      'todos'     => 'Todos ('.(int)($conteos['total'] ?? 0).')',
    ];
  ?>
  <?php foreach ($tabs as $val => $label): ?>
    <a class="vf-tab <?= $estadoFiltro === $val ? 'active' : '' ?>" href="<?= APP_URL ?>/verificacion?estado=<?= $val ?>"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<div class="vf-wrap">
  <div class="card" style="padding:0">
    <div class="vf-list">
      <?php if (!$items): ?>
        <div class="vf-empty">No hay documentos en este filtro.</div>
      <?php endif; ?>
      <?php foreach ($items as $it): ?>
        <?php
          $activo = $seleccionado && (string)$seleccionado['empleado_id'] === (string)$it['empleado_id'] && (string)$seleccionado['documento_id'] === (string)$it['documento_id'];
          $badgeClase = match($it['estado_verificacion']) {
            'aprobado' => 'badge-aprobado',
            'devuelto' => 'badge-borrador',
            default    => 'badge-en-revision',
          };
          $badgeTexto = match($it['estado_verificacion']) {
            'aprobado' => 'Aprobado',
            'devuelto' => 'Devuelto',
            default    => 'Pendiente',
          };
          $peso = !empty($it['peso_kb']) ? ($it['peso_kb'] >= 1024 ? round($it['peso_kb']/1024, 2).' MB' : $it['peso_kb'].' KB') : '';
        ?>
        <a class="vf-item <?= $activo ? 'selected' : '' ?>" href="<?= APP_URL ?>/verificacion/<?= $it['empleado_id'] ?>/<?= $it['documento_id'] ?>?estado=<?= $estadoFiltro ?>">
          <div class="vf-item-doc"><?= View::e($it['documento_nombre']) ?></div>
          <div class="vf-item-emp"><?= View::e($it['empleado_nombre']) ?></div>
          <div class="vf-item-meta">
            <span><?= View::fecha($it['archivo_fecha_subida']) ?><?= $peso ? ' · '.$peso : '' ?></span>
            <span class="badge <?= $badgeClase ?>" style="font-size:10px"><?= $badgeTexto ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <?php if (!$seleccionado): ?>
      <div class="vf-empty">Selecciona un documento de la lista para revisarlo.</div>
    <?php else: ?>
      <?php
        $peso = !empty($seleccionado['peso_kb']) ? ($seleccionado['peso_kb'] >= 1024 ? round($seleccionado['peso_kb']/1024, 2).' MB' : $seleccionado['peso_kb'].' KB') : '—';
      ?>
      <div class="vf-detail-header">
        <div>
          <div style="font-size:15px;font-weight:700"><?= View::e($seleccionado['documento_nombre']) ?></div>
          <div class="text-muted" style="font-size:13px;margin-top:2px"><?= View::e($seleccionado['empleado_nombre']) ?></div>
          <div class="text-muted" style="font-size:12px;margin-top:6px">
            Subido <?= View::fecha($seleccionado['archivo_fecha_subida']) ?> · <?= $peso ?> · <?= View::e($seleccionado['archivo_nombre_original']) ?>
          </div>
        </div>
        <a href="<?= APP_URL ?>/empleados/<?= $seleccionado['empleado_id'] ?>/checklist/documento/<?= $seleccionado['documento_id'] ?>/archivo" target="_blank" class="btn btn-sm btn-outline">Abrir en pestaña nueva ↗</a>
      </div>

      <iframe class="vf-preview" src="<?= APP_URL ?>/empleados/<?= $seleccionado['empleado_id'] ?>/checklist/documento/<?= $seleccionado['documento_id'] ?>/archivo"></iframe>

      <?php if ($seleccionado['estado_verificacion'] === 'devuelto' && $seleccionado['motivo_devolucion']): ?>
        <div class="alert alert-error" style="margin-top:14px"><span>Motivo de devolución: <?= View::e($seleccionado['motivo_devolucion']) ?></span></div>
      <?php endif; ?>

      <?php if ($seleccionado['verificado_por']): ?>
        <div class="text-muted" style="font-size:12px;margin-top:8px">
          Última revisión: <?= View::e($seleccionado['verificado_por']) ?><?= $seleccionado['verificado_en'] ? ' — '.date('d/m/Y H:i', strtotime($seleccionado['verificado_en'])) : '' ?>
        </div>
      <?php endif; ?>

      <div class="vf-actions">
        <?php $accionUrl = APP_URL . '/verificacion/' . $seleccionado['empleado_id'] . '/' . $seleccionado['documento_id'] . '/accion?estado=' . $estadoFiltro; ?>

        <form method="POST" action="<?= $accionUrl ?>">
          <input type="hidden" name="accion" value="aprobar">
          <button type="submit" class="btn btn-primary">✓ Aprobar</button>
        </form>

        <form method="POST" action="<?= $accionUrl ?>" onsubmit="return confirm('¿Marcar este documento como no legible y devolverlo?')">
          <input type="hidden" name="accion" value="no_legible">
          <button type="submit" class="btn btn-outline">👁 No legible</button>
        </form>

        <?php if ($seleccionado['estado_verificacion'] !== 'pendiente'): ?>
        <form method="POST" action="<?= $accionUrl ?>">
          <input type="hidden" name="accion" value="reabrir">
          <button type="submit" class="btn btn-outline">↺ Volver a pendiente</button>
        </form>
        <?php endif; ?>
      </div>

      <form method="POST" action="<?= $accionUrl ?>" style="margin-top:14px;display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">
        <input type="hidden" name="accion" value="devolver">
        <label style="flex:1;min-width:220px">Devolver con motivo
          <input type="text" name="motivo" placeholder="Ej: falta firma, documento vencido, nombre no coincide...">
        </label>
        <button type="submit" class="btn btn-outline">Devolver</button>
      </form>
    <?php endif; ?>
  </div>
</div>
