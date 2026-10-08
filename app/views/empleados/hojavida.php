<?php
/**
 * Réplica del formato oficial FO-TH-018 "Formato Único de Hoja de Vida" V7.
 * Los campos que el sistema no captura quedan en blanco, igual que en el formato original en Word.
 */
$hv_dmy = function (?string $fecha): string {
    if (!$fecha) {
        return '<span class="hv-dmy"><span class="box empty">__</span><span class="sep">D</span>
                 <span class="box empty">__</span><span class="sep">M</span>
                 <span class="box empty">____</span><span class="sep">A</span></span>';
    }
    $t = strtotime($fecha);
    return '<span class="hv-dmy"><span class="box">'.date('d',$t).'</span><span class="sep">D</span>
             <span class="box">'.date('m',$t).'</span><span class="sep">M</span>
             <span class="box">'.date('Y',$t).'</span><span class="sep">A</span></span>';
};
$hv_chk = function (bool $on, string $label): string {
    return '<span class="hv-mini-chk '.($on?'on':'').'"><span class="sq">'.($on?'✕':'').'</span><span class="t">'.$label.'</span></span>';
};
$hv_v = function (?string $v): string {
    $v = trim((string)$v);
    return $v !== '' ? View::e($v) : '<span style="color:var(--faint);font-style:italic">— no registrado —</span>';
};

$ec = mb_strtolower((string)($empleado['estado_civil'] ?? ''));
$esSoltero = str_contains($ec, 'soltero');
$esCasado  = str_contains($ec, 'casado');
$esUnion   = str_contains($ec, 'union') || str_contains($ec, 'unión');
$esViudo   = str_contains($ec, 'viudo');

// Agrupar formación académica por nivel (para ubicar en las secciones correctas del formato)
$edBachiller = $edTecnico = $edPregrado = [];
$edPosgrado = [];
foreach ($educacion as $ed) {
    switch ($ed['nivel_educativo']) {
        case 'bachillerato': $edBachiller[] = $ed; break;
        case 'tecnico': case 'tecnologo': $edTecnico[] = $ed; break;
        case 'pregrado': $edPregrado[] = $ed; break;
        case 'especializacion': case 'maestria': case 'doctorado': $edPosgrado[] = $ed; break;
    }
}
$expRecientes = array_slice($experiencia, 0, 3);
while (count($expRecientes) < 3) { $expRecientes[] = null; }
?>

<div class="hv-head">
  <div class="hv-head-logo">
    <img src="<?= APP_URL ?>/assets/img/logo-fup.png" alt="Fundación Universitaria De Popayán">
  </div>
  <div class="hv-head-title">
    <div class="hv-head-title-row top">Gestión del Talento Humano</div>
    <div class="hv-head-title-row">Formato Único Hoja de Vida</div>
  </div>
  <div class="hv-head-meta">
    <div>Código: FO-TH-018</div>
    <div>Formato versión: 07 · Dic. 2024</div>
    <div>Hoja de vida v<?= (int)($empleado['hojavida_version'] ?? 1) ?><?= !empty($empleado['hojavida_fecha']) ? ' · ' . date('d/m/Y', strtotime($empleado['hojavida_fecha'])) : '' ?></div>
  </div>
</div>

<div class="hv-tbl-wrap">
<table class="hv-tbl">

<!-- ═══ I. INFORMACIÓN PERSONAL ═══ -->
<tr><td class="banner" colspan="2">I. Información personal</td></tr>

<tr>
  <td class="lbl" style="width:220px">Nombres y Apellidos Completos</td>
  <td class="val"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></td>
</tr>
<tr>
  <td class="lbl">Documento de identificación</td>
  <td class="val">
    <?= ($hv_chk)($empleado['tipo_documento']==='CC','C.C.') ?>
    <?= ($hv_chk)($empleado['tipo_documento']==='CE','C.E.') ?>
    <?= ($hv_chk)($empleado['tipo_documento']==='TI','T.I.') ?>
    <?= ($hv_chk)($empleado['tipo_documento']==='PA','PAS') ?>
    &nbsp;&nbsp;No. <strong><?= View::e($empleado['numero_documento']) ?></strong>
  </td>
</tr>
<tr>
  <td class="lbl">Fecha y Lugar de Nacimiento</td>
  <td class="val"><?= ($hv_dmy)($empleado['fecha_nacimiento']) ?> &nbsp; Lugar: <?= ($hv_v)(null) ?></td>
</tr>
<tr>
  <td class="lbl">Fecha y Lugar de Expedición del Documento</td>
  <td class="val"><?= ($hv_dmy)(null) ?> &nbsp; Lugar: <?= ($hv_v)(null) ?></td>
</tr>
<tr>
  <td class="lbl">Nacionalidad</td>
  <td class="val"><?= ($hv_v)(null) ?></td>
</tr>
<tr>
  <td class="lbl">Libreta Militar</td>
  <td class="val"><?= ($hv_chk)(false,'Aplica') ?> Primera clase&nbsp;&nbsp;&nbsp; <?= ($hv_chk)(false,'') ?> Segunda clase&nbsp;&nbsp;&nbsp; No. ____&nbsp;&nbsp;&nbsp; D.M. ____&nbsp;&nbsp;&nbsp; <?= ($hv_chk)(false,'No Aplica') ?></td>
</tr>
<tr>
  <td class="lbl">Sexo / Grupo Sanguíneo</td>
  <td class="val">
    <?= ($hv_chk)($empleado['genero']==='F','F') ?>
    <?= ($hv_chk)($empleado['genero']==='M','M') ?>
    <?= ($hv_chk)($empleado['genero']==='Otro','Otro') ?>
    &nbsp;&nbsp; Identificación de género: <?= ($hv_v)(null) ?>
    &nbsp;&nbsp; Grupo sanguíneo: <strong><?= ($hv_v)($empleado['tipo_sangre']) ?></strong>
  </td>
</tr>
<tr>
  <td class="lbl">Estado Civil</td>
  <td class="val">
    <?= ($hv_chk)($esSoltero,'Soltero(a)') ?>
    <?= ($hv_chk)($esCasado,'Casado(a)') ?>
    <?= ($hv_chk)($esUnion,'Unión libre') ?>
    <?= ($hv_chk)($esViudo,'Viudo(a)') ?>
  </td>
</tr>
<tr>
  <td class="lbl">Dirección de Residencia</td>
  <td class="val"><?= ($hv_v)($empleado['direccion']) ?></td>
</tr>
<tr>
  <td class="lbl">Municipio / Departamento</td>
  <td class="val"><?= ($hv_v)(null) ?> / <?= ($hv_v)(null) ?></td>
</tr>
<tr>
  <td class="lbl">Teléfono (Personal)</td>
  <td class="val"><?= ($hv_v)($empleado['telefono']) ?></td>
</tr>
<tr>
  <td class="lbl">E-mail (Personal)</td>
  <td class="val"><?= ($hv_v)($empleado['email']) ?></td>
</tr>
<tr>
  <td class="lbl">Información de Acudiente / Contacto de Emergencia</td>
  <td class="val">
    Nombre: <?= ($hv_v)($empleado['contacto_emergencia_nombre']) ?>
    &nbsp;&nbsp; Parentesco: <?= ($hv_v)(null) ?>
    &nbsp;&nbsp; Teléfono: <?= ($hv_v)($empleado['contacto_emergencia_telefono']) ?>
  </td>
</tr>
<tr>
  <td class="lbl">Fondo de Pensión / EPS / Fondo de Cesantías</td>
  <td class="val"><?= ($hv_v)($empleado['fondo_pension']) ?> / <?= ($hv_v)($empleado['eps']) ?> / <?= ($hv_v)(null) ?></td>
</tr>
<tr>
  <td class="lbl">ARL</td>
  <td class="val"><?= ($hv_v)($empleado['arl']) ?></td>
</tr>

<tr><td class="sub" colspan="2">Nombre del Esposo(a) o Compañero(a)</td></tr>
<tr>
  <td class="lbl">Nombres y Apellidos Completos</td>
  <td class="val"><?= ($hv_v)(null) ?></td>
</tr>

<tr><td class="sub" colspan="2">Hijos</td></tr>
<tr>
  <td class="lbl">Nombres Completos</td>
  <td class="val" style="font-size:10.5px">Sexo (M/F) — Fecha de nacimiento (D/M/A) — Tipo de documento (R.C / T.I / C.C) — Número</td>
</tr>
<?php for ($i = 0; $i < 3; $i++): ?>
<tr class="empty-row"><td colspan="2">&nbsp;</td></tr>
<?php endfor; ?>

<!-- ═══ II. INFORMACIÓN ACADÉMICA ═══ -->
<tr><td class="banner" colspan="2">II. Información académica</td></tr>

<tr><td class="sub" colspan="2">Educación Básica y Media (Bachiller)</td></tr>
<?php if ($edBachiller): foreach ($edBachiller as $ed): ?>
<tr>
  <td class="lbl">Título obtenido</td>
  <td class="val"><?= ($hv_v)($ed['titulo_obtenido']) ?></td>
</tr>
<tr>
  <td class="lbl">Nombre de la Institución</td>
  <td class="val"><?= ($hv_v)($ed['institucion']) ?> &nbsp;&nbsp; Año: <strong><?= ($hv_v)((string)$ed['anio_graduacion']) ?></strong></td>
</tr>
<?php endforeach; else: ?>
<tr><td class="lbl">Nombre de la Institución</td><td class="val"><?= ($hv_v)(null) ?></td></tr>
<?php endif; ?>

<tr><td class="sub" colspan="2">Técnico (TC) / Tecnología (TL) / Especialización Tecnológica</td></tr>
<?php if ($edTecnico): foreach ($edTecnico as $ed): ?>
<tr>
  <td class="lbl"><?= View::nivelEducativoLabel($ed['nivel_educativo']) ?> — Título obtenido</td>
  <td class="val"><?= ($hv_v)($ed['titulo_obtenido']) ?></td>
</tr>
<tr>
  <td class="lbl">Nombre de la Institución</td>
  <td class="val"><?= ($hv_v)($ed['institucion']) ?> &nbsp;&nbsp; Año: <strong><?= ($hv_v)((string)$ed['anio_graduacion']) ?></strong></td>
</tr>
<?php endforeach; else: ?>
<tr><td class="lbl">Nombre de la Institución</td><td class="val"><?= ($hv_v)(null) ?></td></tr>
<?php endif; ?>

<tr><td class="sub" colspan="2">Educación Superior Pregrado — Universidad (UN)</td></tr>
<?php if ($edPregrado): foreach ($edPregrado as $ed): ?>
<tr><td class="lbl">Título obtenido</td><td class="val"><?= ($hv_v)($ed['titulo_obtenido']) ?></td></tr>
<tr><td class="lbl">Nombre de la Institución</td><td class="val"><?= ($hv_v)($ed['institucion']) ?></td></tr>
<tr><td class="lbl">Fecha de grado</td><td class="val">Año: <strong><?= ($hv_v)((string)$ed['anio_graduacion']) ?></strong></td></tr>
<tr><td class="lbl">No. Tarjeta Profesional (si aplica)</td><td class="val"><?= ($hv_v)(null) ?></td></tr>
<?php endforeach; else: ?>
<tr><td class="lbl">Título obtenido</td><td class="val"><?= ($hv_v)(null) ?></td></tr>
<?php endif; ?>

<tr><td class="sub" colspan="2">Educación Superior de Posgrado (hasta 3 registros)</td></tr>
<?php
$posSlots = $edPosgrado ?: [];
for ($i = 0; $i < 3; $i++):
    $ed = $posSlots[$i] ?? null;
?>
<tr>
  <td class="lbl">Título <?= $i+1 ?></td>
  <td class="val">
    <?= ($hv_chk)($ed && $ed['nivel_educativo']==='especializacion','Esp.') ?>
    <?= ($hv_chk)($ed && $ed['nivel_educativo']==='maestria','Mg.') ?>
    <?= ($hv_chk)($ed && $ed['nivel_educativo']==='doctorado','Dr. / PhD') ?>
    &nbsp;&nbsp; <?= ($hv_chk)($ed && empty($ed['en_curso']),'Culminada') ?> <?= ($hv_chk)($ed && !empty($ed['en_curso']),'En curso') ?>
  </td>
</tr>
<tr><td class="lbl">Título obtenido</td><td class="val"><?= ($hv_v)($ed['titulo_obtenido'] ?? null) ?></td></tr>
<tr><td class="lbl">Nombre de la Institución</td><td class="val"><?= ($hv_v)($ed['institucion'] ?? null) ?></td></tr>
<tr><td class="lbl">Fecha de grado</td><td class="val">Año: <strong><?= ($hv_v)($ed ? (string)$ed['anio_graduacion'] : null) ?></strong></td></tr>
<?php endfor; ?>

<tr><td class="sub" colspan="2">Educación Continuada (cursos, talleres, diplomados, seminarios)</td></tr>
<tr>
  <td class="lbl">Título obtenido</td>
  <td class="val" style="font-size:10.5px">Curso / Taller / Diplomado / Seminario / Actualización / Otro — Fecha (D/M/A)</td>
</tr>
<?php $compl = $complementaria ?? []; foreach ($compl as $cp): ?>
<tr>
  <td class="lbl" style="white-space:normal"><?= View::e($cp['nombre']) ?></td>
  <td class="val"><?= View::e($cp['institucion']) ?> &nbsp;·&nbsp; <?= View::fecha($cp['fecha']) ?><?= !empty($cp['horas']) ? ' &nbsp;·&nbsp; ' . (int)$cp['horas'] . ' h' : '' ?></td>
</tr>
<?php endforeach; ?>
<?php for ($i = count($compl); $i < 3; $i++): ?>
<tr class="empty-row"><td colspan="2">&nbsp;</td></tr>
<?php endfor; ?>

<tr>
  <td class="lbl">CvLAC (Currículum Vitae Latinoamérica y el Caribe)</td>
  <td class="val"><?= ($hv_chk)(false,'Aplica') ?> <?= ($hv_chk)(false,'No aplica') ?></td>
</tr>

<tr><td class="sub" colspan="2">Idiomas (diferentes al español)</td></tr>
<tr>
  <td class="lbl">Idioma / Institución</td>
  <td class="val" style="font-size:10.5px">Lo habla / Lo escribe / Lo lee (MB / B / R) — En curso / Culminado</td>
</tr>
<?php for ($i = 0; $i < 2; $i++): ?>
<tr class="empty-row"><td colspan="2">&nbsp;</td></tr>
<?php endfor; ?>

<!-- ═══ III. EXPERIENCIA LABORAL ═══ -->
<tr><td class="banner" colspan="2">III. Experiencia laboral (últimas 3, comenzando por la actual)</td></tr>

<?php foreach ($expRecientes as $i => $ex): ?>
<tr><td class="sub" colspan="2">Experiencia <?= $i+1 ?></td></tr>
<tr>
  <td class="lbl">Nombre de la organización o Institución</td>
  <td class="val"><?= ($hv_v)($ex['empresa'] ?? null) ?></td>
</tr>
<tr>
  <td class="lbl">Cargo desempeñado</td>
  <td class="val"><?= ($hv_v)($ex['cargo'] ?? null) ?></td>
</tr>
<tr>
  <td class="lbl">Área o Dependencia</td>
  <td class="val"><?= ($hv_v)(null) ?></td>
</tr>
<tr>
  <td class="lbl">Dedicación</td>
  <td class="val"><?= ($hv_chk)(false,'Tiempo Completo') ?> <?= ($hv_chk)(false,'Medio Tiempo') ?> <?= ($hv_chk)(false,'Tiempo Parcial') ?></td>
</tr>
<tr>
  <td class="lbl">Tipo de Vinculación</td>
  <td class="val"><?= ($hv_chk)(false,'Término Fijo') ?> <?= ($hv_chk)(false,'Término Indefinido') ?> <?= ($hv_chk)(false,'Prestación de Servicios') ?></td>
</tr>
<tr>
  <td class="lbl">Fecha de Ingreso / Terminación</td>
  <td class="val"><?= ($hv_dmy)($ex['fecha_inicio'] ?? null) ?> &nbsp;&nbsp;—&nbsp;&nbsp; <?= $ex && !empty($ex['fecha_fin']) ? ($hv_dmy)($ex['fecha_fin']) : '<em style="color:var(--muted);font-size:11px">Actual</em>' ?></td>
</tr>
<?php if (!empty($ex['funciones'])): ?>
<tr><td class="lbl">Funciones</td><td class="val"><?= View::e($ex['funciones']) ?></td></tr>
<?php endif; ?>
<?php endforeach; ?>

</table>
</div>

<div class="hv-cert">
  Para todos los efectos legales, certifico que los datos por mí anotados en el presente Formato Único de Hoja de Vida son veraces.
</div>

<div class="hv-firmas">
  <div class="hv-firma"><div class="linea">Firma del empleado<br><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></div></div>
  <div class="hv-firma"><div class="linea">Fecha&nbsp;&nbsp; Día: ____&nbsp;&nbsp; Mes: ____&nbsp;&nbsp; Año: ______</div></div>
</div>