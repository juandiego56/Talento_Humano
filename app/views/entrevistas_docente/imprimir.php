<?php
$chk = function (bool $on, string $label = ''): string {
    return '<span class="hv-mini-chk '.($on?'on':'').'"><span class="sq">'.($on?'✕':'').'</span>'.($label !== '' ? '<span class="t">'.$label.'</span>' : '').'</span>';
};
$v = fn(?string $val) => $val !== null && $val !== '' ? View::e($val) : '<span style="color:var(--faint);font-style:italic">— —</span>';
$e = $entrevista ?? [];
?>

<div class="hv-topbar">
  <div class="hv-brand">
    GESTIÓN DEL TALENTO HUMANO
    <span>Entrevista de Personal Docente</span>
  </div>
  <div class="hv-code-box">
    <div><b>Código</b> <span>FO-TH-031</span></div>
    <div><b>Versión</b> <span>02</span></div>
    <div><b>Fecha impresión</b> <span><?= date('d/m/Y') ?></span></div>
  </div>
</div>
<div class="hv-title">Entrevista de Personal Docente</div>

<table class="hv-tbl" style="margin-bottom:10px">
  <tr>
    <td class="lbl" style="width:200px">Nombre del entrevistador</td>
    <td class="val" colspan="3"><?= $v($e['nombre_entrevistador'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Cargo</td>
    <td class="val" colspan="3"><?= $v($e['cargo_entrevistador'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Programa académico</td>
    <td class="val" colspan="3"><?= $v($e['programa_academico'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Fecha de la entrevista</td>
    <td class="val" colspan="3"><?= View::fecha($e['fecha_entrevista'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Modalidad</td>
    <td class="val" colspan="3">
      <?= $chk(($e['modalidad'] ?? '') === 'presencial', 'Presencial') ?>
      &nbsp;&nbsp;
      <?= $chk(($e['modalidad'] ?? '') === 'virtual', 'Virtual') ?>
    </td>
  </tr>
</table>

<table class="hv-tbl" style="margin-bottom:10px">
  <tr>
    <td class="lbl" style="width:200px">Nombre entrevistado</td>
    <td class="val" colspan="3"><?= $v($e['nombre_entrevistado'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Profesión</td>
    <td class="val" colspan="3"><?= $v($e['profesion'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Posgrados</td>
    <td class="val" colspan="3">
      <?= $chk(($e['posgrados_estado'] ?? '') === 'en_curso', 'En curso') ?>
      &nbsp;&nbsp;
      <?= $chk(($e['posgrados_estado'] ?? '') === 'culminado', 'Culminado') ?>
      <?php if (!empty($e['posgrados_detalle'])): ?> — <?= View::e($e['posgrados_detalle']) ?><?php endif; ?>
    </td>
  </tr>
  <tr>
    <td class="lbl">Documento de identidad</td>
    <td class="val">
      <?= $chk(($e['doc_tipo'] ?? '') === 'CC', 'C.C.') ?>
      <?= $chk(($e['doc_tipo'] ?? '') === 'CE', 'C.E.') ?>
    </td>
    <td class="val">No. <?= $v($e['doc_numero'] ?? null) ?></td>
    <td class="val">Lugar exp.: <?= $v($e['doc_lugar_expedicion'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Tipo de vinculación</td>
    <td class="val" colspan="3">
      <?= $chk(($e['tipo_vinculacion_docente'] ?? '') === 'tiempo_completo', 'Docente Tiempo Completo') ?>
      &nbsp;&nbsp;
      <?= $chk(($e['tipo_vinculacion_docente'] ?? '') === 'medio_tiempo', 'Docente Medio Tiempo') ?>
      &nbsp;&nbsp;
      <?= $chk(($e['tipo_vinculacion_docente'] ?? '') === 'hora_catedra', 'Docente Hora Cátedra') ?>
    </td>
  </tr>
</table>

<div class="hv-subhead">1. Competencias personales</div>
<p class="hv-note" style="margin:0 0 6px">Contexto social, presentación personal, actitud, aspiraciones a corto plazo — fortalezas y debilidades</p>
<div class="hv-section-body"><?= nl2br($v($e['competencias_personales'] ?? null)) ?></div>

<div class="hv-subhead">2. Competencias académicas</div>
<p class="hv-note" style="margin:0 0 6px">Logros académicos, investigaciones, producción intelectual, desarrollos y aportes significativos</p>
<div class="hv-section-body"><?= nl2br($v($e['competencias_academicas'] ?? null)) ?></div>

<div class="hv-subhead">3. Competencias profesionales</div>
<p class="hv-note" style="margin:0 0 6px">Logros profesionales y aportes significativos o fortalezas en el campo profesional</p>
<div class="hv-section-body"><?= nl2br($v($e['competencias_profesionales'] ?? null)) ?></div>

<div class="hv-subhead">4. Concepto general</div>
<p class="hv-note" style="margin:0 0 6px">Concepto cualitativo y asignaturas que puede desarrollar en el programa académico</p>
<div class="hv-section-body"><?= nl2br($v($e['concepto_general'] ?? null)) ?></div>

<table class="hv-tbl" style="margin-bottom:10px">
  <tr>
    <td class="lbl" style="width:340px">5. Disponibilidad de tiempo: ¿el candidato dispone de tiempo para laborar el periodo lectivo académico propuesto según el tipo de vinculación?</td>
    <td class="val" style="width:70px;text-align:center"><?= $chk(($e['disponibilidad_tiempo'] ?? '') === 'si', 'SI') ?></td>
    <td class="val" style="width:70px;text-align:center"><?= $chk(($e['disponibilidad_tiempo'] ?? '') === 'no', 'NO') ?></td>
  </tr>
  <tr>
    <td class="lbl">6. Acepta condiciones laborales: se informa sobre tipo de vinculación laboral, horarios, asignación salarial, asignaturas y responsabilidades administrativas y docentes.</td>
    <td class="val" style="text-align:center"><?= $chk(($e['acepta_condiciones'] ?? '') === 'si', 'SI') ?></td>
    <td class="val" style="text-align:center"><?= $chk(($e['acepta_condiciones'] ?? '') === 'no', 'NO') ?></td>
  </tr>
</table>

<div class="hv-subhead">7. Conclusiones</div>
<div class="hv-section-body"><?= nl2br($v($e['conclusiones'] ?? null)) ?></div>

<div class="hv-firmas">
  <div class="hv-firma">
    <div class="linea">Firma del entrevistador: <?= $v($e['firma_entrevistador'] ?? null) ?></div>
  </div>
</div>
