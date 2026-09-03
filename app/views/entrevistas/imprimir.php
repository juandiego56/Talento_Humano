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
    <span>Entrevista de Personal Administrativo</span>
  </div>
  <div class="hv-code-box">
    <div><b>Código</b> <span>FO-TH-009</span></div>
    <div><b>Versión</b> <span>07</span></div>
    <div><b>Fecha impresión</b> <span><?= date('d/m/Y') ?></span></div>
  </div>
</div>
<div class="hv-title">Entrevista de Personal Administrativo</div>

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
    <td class="lbl">Área</td>
    <td class="val" colspan="3"><?= $v($e['area_entrevistador'] ?? null) ?></td>
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
    <td class="lbl" style="width:200px">Nombre del entrevistado</td>
    <td class="val" colspan="3"><?= $v($e['nombre_entrevistado'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Profesión</td>
    <td class="val" colspan="3"><?= $v($e['profesion'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Cargo al que aspira</td>
    <td class="val" colspan="3"><?= $v($e['cargo_aspira'] ?? null) ?></td>
  </tr>
  <tr>
    <td class="lbl">Posgrados</td>
    <td class="val" colspan="3">
      <?= $chk(($e['posgrados_estado'] ?? '') === 'no_aplica', 'No Aplica') ?>
      &nbsp;&nbsp;
      <?= $chk(($e['posgrados_estado'] ?? '') === 'en_curso', 'En curso') ?>
      &nbsp;&nbsp;
      <?= $chk(($e['posgrados_estado'] ?? '') === 'culminado', 'Culminado') ?>
      <?php if (!empty($e['posgrados_detalle'])): ?> — <?= View::e($e['posgrados_detalle']) ?><?php endif; ?>
    </td>
  </tr>
  <tr>
    <td class="lbl">Documento de identidad</td>
    <td class="val">
      <?= $chk(($e['doc_tipo'] ?? '') === 'TI', 'T.I.') ?>
      <?= $chk(($e['doc_tipo'] ?? '') === 'CC', 'C.C.') ?>
      <?= $chk(($e['doc_tipo'] ?? '') === 'CE', 'C.E.') ?>
    </td>
    <td class="val">No. <?= $v($e['doc_numero'] ?? null) ?></td>
    <td class="val">Lugar exp.: <?= $v($e['doc_lugar_expedicion'] ?? null) ?></td>
  </tr>
</table>

<div class="hv-subhead">Información personal (Personales – Familiares – Estado Civil)</div>
<div class="hv-section-body"><?= nl2br($v($e['informacion_personal'] ?? null)) ?></div>

<div class="hv-subhead">Formación académica (Estudios realizados, logros alcanzados e intereses)</div>
<div class="hv-section-body"><?= nl2br($v($e['formacion_academica'] ?? null)) ?></div>

<div class="hv-subhead">Experiencia laboral (Cargos ocupados, tiempo de servicio, motivo de retiro, aspiraciones)</div>
<div class="hv-section-body"><?= nl2br($v($e['experiencia_laboral'] ?? null)) ?></div>

<table class="hv-tbl" style="margin-bottom:10px">
  <tr>
    <td class="lbl" style="width:50%">Resultado prueba psicotécnica</td>
    <td class="lbl" style="width:50%">Resultado prueba técnica</td>
  </tr>
  <tr>
    <td class="val"><?= nl2br($v($e['resultado_prueba_psicotecnica'] ?? null)) ?></td>
    <td class="val"><?= nl2br($v($e['resultado_prueba_tecnica'] ?? null)) ?></td>
  </tr>
</table>

<table class="hv-tbl" style="margin-bottom:10px">
  <tr>
    <td class="lbl" style="width:340px">Disponibilidad de tiempo: ¿El candidato dispone de tiempo para laborar en la institución según el tipo de vinculación?</td>
    <td class="val" style="width:70px;text-align:center"><?= $chk(($e['disponibilidad_tiempo'] ?? '') === 'si', 'SI') ?></td>
    <td class="val" style="width:70px;text-align:center"><?= $chk(($e['disponibilidad_tiempo'] ?? '') === 'no', 'NO') ?></td>
  </tr>
  <tr>
    <td class="lbl">Acepta condiciones laborales: se informa sobre el tipo de vinculación laboral, horarios, asignación salarial, responsabilidades administrativas.</td>
    <td class="val" style="text-align:center"><?= $chk(($e['acepta_condiciones'] ?? '') === 'si', 'SI') ?></td>
    <td class="val" style="text-align:center"><?= $chk(($e['acepta_condiciones'] ?? '') === 'no', 'NO') ?></td>
  </tr>
</table>

<div class="hv-subhead">Conclusiones</div>
<div class="hv-section-body"><?= nl2br($v($e['conclusiones'] ?? null)) ?></div>

<table class="hv-tbl" style="margin-top:10px">
  <tr>
    <td class="val" style="text-align:center"><?= $chk(($e['decision'] ?? '') === 'vincular', 'VINCULAR') ?></td>
    <td class="val" style="text-align:center"><?= $chk(($e['decision'] ?? '') === 'descartar', 'DESCARTAR') ?></td>
  </tr>
</table>

<div class="hv-firmas">
  <div class="hv-firma">
    <div class="linea">Firma del entrevistador: <?= $v($e['firma_entrevistador'] ?? null) ?></div>
  </div>
</div>
