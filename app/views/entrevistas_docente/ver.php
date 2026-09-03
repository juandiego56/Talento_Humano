<div class="page-header">
  <div>
    <div class="page-title">Entrevista de Personal Docente — FO-TH-031</div>
    <div class="page-subtitle"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></div>
  </div>
  <div class="page-actions">
    <?php if ($entrevista): ?>
      <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente/imprimir" target="_blank" class="btn btn-outline">Ver / Imprimir formato</a>
    <?php endif; ?>
    <?php if (Auth::puedeGestionar()): ?>
      <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente/editar" class="btn btn-primary">
        <?= $entrevista ? 'Editar' : 'Diligenciar entrevista' ?>
      </a>
    <?php endif; ?>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>" class="btn btn-outline">← Volver al empleado</a>
  </div>
</div>

<?php if (!$entrevista): ?>
  <div class="card">
    <p class="text-muted">Todavía no se ha diligenciado la entrevista de personal docente para este empleado.</p>
    <?php if (Auth::puedeGestionar()): ?>
      <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente/editar" class="btn btn-primary" style="margin-top:8px">Diligenciar entrevista</a>
    <?php endif; ?>
  </div>
<?php else: ?>

  <div class="card">
    <div class="card-header"><div class="card-title">Datos del entrevistador</div></div>
    <div class="hv-grid hv-grid-3">
      <div class="hv-field"><label>Nombre</label><div class="hv-val <?= $entrevista['nombre_entrevistador'] ? '' : 'hv-empty' ?>"><?= View::e($entrevista['nombre_entrevistador'] ?: '— no registrado —') ?></div></div>
      <div class="hv-field"><label>Cargo</label><div class="hv-val <?= $entrevista['cargo_entrevistador'] ? '' : 'hv-empty' ?>"><?= View::e($entrevista['cargo_entrevistador'] ?: '— no registrado —') ?></div></div>
      <div class="hv-field"><label>Programa académico</label><div class="hv-val <?= $entrevista['programa_academico'] ? '' : 'hv-empty' ?>"><?= View::e($entrevista['programa_academico'] ?: '— no registrado —') ?></div></div>
      <div class="hv-field"><label>Fecha</label><div class="hv-val"><?= View::fecha($entrevista['fecha_entrevista']) ?></div></div>
      <div class="hv-field"><label>Modalidad</label><div class="hv-val"><?= View::modalidadLabel($entrevista['modalidad']) ?></div></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Datos del entrevistado</div></div>
    <div class="hv-grid hv-grid-3">
      <div class="hv-field"><label>Nombre</label><div class="hv-val"><?= View::e($entrevista['nombre_entrevistado'] ?: '— no registrado —') ?></div></div>
      <div class="hv-field"><label>Profesión</label><div class="hv-val"><?= View::e($entrevista['profesion'] ?: '— no registrado —') ?></div></div>
      <div class="hv-field"><label>Posgrados</label><div class="hv-val"><?= View::posgradosEstadoLabel($entrevista['posgrados_estado']) ?><?= $entrevista['posgrados_detalle'] ? ' — '.View::e($entrevista['posgrados_detalle']) : '' ?></div></div>
      <div class="hv-field"><label>Documento</label><div class="hv-val"><?= View::e(($entrevista['doc_tipo'] ?: '').' '.($entrevista['doc_numero'] ?: '')) ?></div></div>
      <div class="hv-field"><label>Lugar de expedición</label><div class="hv-val"><?= View::e($entrevista['doc_lugar_expedicion'] ?: '— no registrado —') ?></div></div>
      <div class="hv-field"><label>Tipo de vinculación</label><div class="hv-val"><?= View::tipoVinculacionDocenteLabel($entrevista['tipo_vinculacion_docente']) ?></div></div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">1. Competencias personales</div></div>
    <div class="hv-entry-body"><?= nl2br(View::e($entrevista['competencias_personales'] ?: '— no registrado —')) ?></div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">2. Competencias académicas</div></div>
    <div class="hv-entry-body"><?= nl2br(View::e($entrevista['competencias_academicas'] ?: '— no registrado —')) ?></div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">3. Competencias profesionales</div></div>
    <div class="hv-entry-body"><?= nl2br(View::e($entrevista['competencias_profesionales'] ?: '— no registrado —')) ?></div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">4. Concepto general</div></div>
    <div class="hv-entry-body"><?= nl2br(View::e($entrevista['concepto_general'] ?: '— no registrado —')) ?></div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Cierre de la entrevista</div></div>
    <div class="hv-grid hv-grid-3" style="margin-bottom:10px">
      <div class="hv-field"><label>5. Disponibilidad de tiempo</label><div class="hv-val"><?= View::siNoLabel($entrevista['disponibilidad_tiempo']) ?></div></div>
      <div class="hv-field"><label>6. Acepta condiciones laborales</label><div class="hv-val"><?= View::siNoLabel($entrevista['acepta_condiciones']) ?></div></div>
    </div>
    <div class="hv-field"><label>7. Conclusiones</label><div class="hv-entry-body"><?= nl2br(View::e($entrevista['conclusiones'] ?: '— no registrado —')) ?></div></div>
    <div class="hv-field" style="margin-top:8px"><label>Firma del entrevistador</label><div class="hv-val"><?= View::e($entrevista['firma_entrevistador'] ?: '— no registrado —') ?></div></div>
  </div>

  <?php if (Auth::esAdmin()): ?>
  <div class="card">
    <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente/eliminar" onsubmit="return confirm('¿Eliminar esta entrevista? Esta acción no se puede deshacer.')">
      <button type="submit" class="btn btn-outline">Eliminar entrevista</button>
    </form>
  </div>
  <?php endif; ?>

<?php endif; ?>
