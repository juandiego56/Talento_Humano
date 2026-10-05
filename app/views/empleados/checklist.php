<div class="page-header">
  <div>
    <div class="page-title">Lista de Chequeo — FO-TH-027</div>
    <div class="page-subtitle"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?> — Vinculaciones Laborales</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/imprimir" target="_blank" class="btn btn-outline">Ver / Imprimir formato</a>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>" class="btn btn-outline">← Volver al empleado</a>
  </div>
</div>

<?php
  $total = count($items);
  $entregados = count(array_filter($items, fn($i) => (int)$i['entregado'] === 1));
  $conSoporte = count(array_filter($items, fn($i) => !empty($i['archivo_path'])));
  $pct = $total ? round($entregados / $total * 100, 1) : 0;
?>

<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
    <strong style="font-size:13px"><?= $entregados ?> de <?= $total ?> documentos marcados SI — <?= $conSoporte ?> con soporte adjunto</strong>
    <strong style="font-size:13px;color:var(--primary)"><?= $pct ?>%</strong>
  </div>
  <div class="progress-bar" style="width:100%"><div class="progress-fill" style="width:<?= $pct ?>%"></div></div>
</div>

<div class="card">
  <?php foreach ($items as $it): ?>
    <div class="check-item <?= $it['entregado'] ? 'check-done' : '' ?>" style="align-items:flex-start;flex-wrap:wrap">
      <div class="check-info" style="min-width:220px">
        <div class="check-nombre">
          <?php if ($it['codigo']): ?><span class="text-muted" style="font-weight:400"><?= View::e($it['codigo']) ?> — </span><?php endif; ?>
          <?= View::e($it['nombre']) ?>
          <?php if ($it['obligatorio']): ?><span class="badge badge-en-revision" style="margin-left:6px">Obligatorio</span><?php endif; ?>
        </div>
        <?php if ($it['descripcion']): ?><div class="check-desc"><?= View::e($it['descripcion']) ?></div><?php endif; ?>
        <?php if ($it['entregado'] && $it['fecha_entrega']): ?>
          <div class="check-desc">Marcado SI el <?= View::fecha($it['fecha_entrega']) ?><?= $it['observaciones'] ? ' — '.View::e($it['observaciones']) : '' ?></div>
        <?php endif; ?>
      </div>

      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <?php if (Auth::puedeGestionar()): ?>
        <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/actualizar" style="display:flex;align-items:center;gap:8px">
          <input type="hidden" name="documento_id" value="<?= $it['documento_id'] ?>">
          <input type="hidden" name="accion" value="toggle">
          <label class="check-group" style="margin:0;display:flex;align-items:center;gap:6px;font-size:12px">
            <input type="checkbox" name="entregado" <?= $it['entregado'] ? 'checked' : '' ?> onchange="this.form.submit()">
            SI
          </label>
        </form>
        <?php else: ?>
          <span class="badge <?= $it['entregado'] ? 'badge-aprobado' : 'badge-borrador' ?>"><?= $it['entregado'] ? 'SI' : 'NO' ?></span>
        <?php endif; ?>

        <!-- Espacio para archivo / documento adjunto -->
        <?php if ($it['archivo_path']): ?>
          <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/documento/<?= $it['documento_id'] ?>/archivo"
             target="_blank" class="badge badge-aprobado" style="text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:11px;height:11px;vertical-align:-1px;margin-right:2px">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32a1.5 1.5 0 01-2.122-2.122l8.472-8.472"/>
            </svg>
            <?= View::e($it['archivo_nombre_original']) ?><?php if (!empty($it['peso_kb'])): ?> (<?= $it['peso_kb'] >= 1024 ? round($it['peso_kb']/1024, 2).' MB' : $it['peso_kb'].' KB' ?>)<?php endif; ?>
          </a>
          <?php if (Auth::puedeGestionar()): ?>
          <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/documento/<?= $it['documento_id'] ?>/eliminar-archivo"
                onsubmit="return confirm('¿Eliminar este soporte adjunto?')">
            <button type="submit" class="btn btn-sm btn-outline">Quitar</button>
          </form>
          <?php endif; ?>
        <?php elseif (Auth::puedeGestionar()): ?>
          <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/actualizar"
                enctype="multipart/form-data" style="display:flex;align-items:center;gap:6px">
            <input type="hidden" name="documento_id" value="<?= $it['documento_id'] ?>">
            <input type="hidden" name="accion" value="archivo">
            <input type="file" name="archivo" required accept="application/pdf,.pdf" style="font-size:11px;max-width:170px">
            <button type="submit" class="btn btn-sm btn-primary">Adjuntar (solo PDF)</button>
          </form>
        <?php else: ?>
          <span class="badge badge-borrador">Sin soporte</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (!$items): ?>
    <p class="text-muted">No hay documentos configurados en el catálogo. Ejecuta la migración <code>migration_fo_th_027.sql</code> o ve a "Catálogo de Documentos".</p>
  <?php endif; ?>
</div>

<?php if (Auth::puedeGestionar()): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Agregar observación a un documento</div></div>
  <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/actualizar" class="form-row">
    <input type="hidden" name="accion" value="observacion">
    <label>Documento
      <select name="documento_id" required>
        <?php foreach ($items as $it): ?>
          <option value="<?= $it['documento_id'] ?>"><?= View::e($it['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Observaciones
      <input type="text" name="observaciones" placeholder="Ej: pendiente por firma del jefe inmediato">
    </label>
    <label style="align-self:end">
      <button type="submit" class="btn btn-outline" style="width:100%">Guardar observación</button>
    </label>
  </form>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Revisó en archivo</div></div>
  <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist/firma" class="form-row">
    <label>Reviso en archivo
      <input type="text" name="reviso_en_archivo" value="<?= View::e($firma['reviso_en_archivo'] ?? '') ?>" placeholder="Nombre de quien revisó">
    </label>
    <label>Firma
      <input type="text" name="firma_reviso" value="<?= View::e($firma['firma_reviso'] ?? '') ?>" placeholder="Firma / nombre de confirmación">
    </label>
    <label style="align-self:end">
      <button type="submit" class="btn btn-primary" style="width:100%">Guardar</button>
    </label>
  </form>
  <?php if (!empty($firma['fecha_revision'])): ?>
    <div class="check-desc" style="margin-top:6px">Última revisión: <?= View::fecha($firma['fecha_revision']) ?></div>
  <?php endif; ?>
</div>
<?php endif; ?>
