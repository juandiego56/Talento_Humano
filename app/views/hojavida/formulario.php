<?php
/** @var array $empleado, $datos, $pasos, $faltantes, $educacion, $experiencia */
$hoy = date('Y-m-d');
$estado = $empleado['hojavida_estado'] ?? 'borrador';
$base = APP_URL . '/mi-hoja-de-vida';
$bloq = fn(string $c): bool => false;
$exigir = true;

$niveles = ['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'];

/** Formulario de una formación (nueva o en edición). */
$formFormacion = function (?array $ed) use ($base, $niveles, $hoy): void { $id = (int)($ed['id'] ?? 0); $k = 'ed' . $id; ?>
  <form method="POST" action="<?= $base ?>/formacion/guardar">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-row">
      <label>Nivel de estudio *
        <select name="nivel_educativo" required>
          <?php foreach ($niveles as $n): ?>
            <option value="<?= $n ?>" <?= ($ed['nivel_educativo'] ?? '') === $n ? 'selected' : '' ?>><?= View::nivelEducativoLabel($n) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Institución *
        <input type="text" name="institucion" required minlength="3" maxlength="150" value="<?= View::e($ed['institucion'] ?? '') ?>" placeholder="Nombre de la institución">
      </label>
    </div>
    <div class="form-row">
      <label>Título o programa *
        <input type="text" name="titulo_obtenido" required minlength="3" maxlength="150" value="<?= View::e($ed['titulo_obtenido'] ?? '') ?>" placeholder="Ej. Ingeniero de Sistemas">
      </label>
      <label>Fecha de expedición del título *
        <input type="date" name="fecha_expedicion" id="fx_<?= $k ?>" data-req-si-activo min="1950-01-01" max="<?= $hoy ?>"
               <?= !empty($ed['en_curso']) ? '' : 'required' ?> value="<?= View::e($ed['fecha_expedicion'] ?? '') ?>"
               data-msg="Escribe una fecha válida: no puede ser futura ni anterior a 1950.">
      </label>
      <label style="align-self:end;display:flex;align-items:center;gap:6px;margin-bottom:14px">
        <input type="checkbox" name="en_curso" value="1" data-desactiva="#fx_<?= $k ?>" <?= !empty($ed['en_curso']) ? 'checked' : '' ?>> En curso / no culminado
      </label>
    </div>
    <button type="submit" class="btn btn-primary btn-sm"><?= $id ? 'Guardar cambios' : 'Agregar formación' ?></button>
  </form>
<?php };

/** Formulario de una experiencia (nueva o en edición). */
$formExperiencia = function (?array $ex) use ($base, $hoy): void { $id = (int)($ex['id'] ?? 0); $k = 'ex' . $id; ?>
  <form method="POST" action="<?= $base ?>/experiencia/guardar">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-row">
      <label>Empresa *
        <input type="text" name="empresa" required minlength="2" maxlength="150" value="<?= View::e($ex['empresa'] ?? '') ?>">
      </label>
      <label>Cargo *
        <input type="text" name="cargo" required minlength="2" maxlength="120" value="<?= View::e($ex['cargo'] ?? '') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Fecha de inicio *
        <input type="date" name="fecha_inicio" id="fi_<?= $k ?>" required min="1960-01-01" max="<?= $hoy ?>" value="<?= View::e($ex['fecha_inicio'] ?? '') ?>"
               data-msg="Escribe una fecha de inicio válida (no futura).">
      </label>
      <label>Fecha de finalización *
        <input type="date" name="fecha_fin" id="ff_<?= $k ?>" data-req-si-activo min="1960-01-01" max="<?= $hoy ?>"
               <?= ($id && empty($ex['fecha_fin'])) ? '' : 'required' ?> value="<?= View::e($ex['fecha_fin'] ?? '') ?>"
               data-msg="La fecha de finalización no puede ser futura ni anterior a la de inicio.">
      </label>
      <label style="align-self:end;display:flex;align-items:center;gap:6px;margin-bottom:14px">
        <input type="checkbox" name="trabajo_actual" value="1" data-desactiva="#ff_<?= $k ?>" <?= ($id && empty($ex['fecha_fin'])) ? 'checked' : '' ?>> Trabajo aquí actualmente
      </label>
    </div>
    <label>Funciones desempeñadas *
      <textarea name="funciones" rows="3" required minlength="10" maxlength="1000"><?= View::e($ex['funciones'] ?? '') ?></textarea>
    </label>
    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px"><?= $id ? 'Guardar cambios' : 'Agregar experiencia' ?></button>
  </form>
  <script>
  (function(){
    var i = document.getElementById('fi_<?= $k ?>'), f = document.getElementById('ff_<?= $k ?>');
    if (!i || !f) return;
    function sync(){ f.min = i.value || '1960-01-01'; }
    i.addEventListener('change', sync); sync();
  })();
  </script>
<?php };

/** Formulario de una formación complementaria (curso, diplomado, certificación). */
$formComplementaria = function (?array $c) use ($base, $hoy): void { $id = (int)($c['id'] ?? 0); ?>
  <form method="POST" action="<?= $base ?>/complementaria/guardar">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-row">
      <label>Nombre del curso o certificación *
        <input type="text" name="nombre" required minlength="3" maxlength="150" value="<?= View::e($c['nombre'] ?? '') ?>" placeholder="Ej. Diplomado en Gestión Humana">
      </label>
      <label>Institución *
        <input type="text" name="institucion" required minlength="3" maxlength="150" value="<?= View::e($c['institucion'] ?? '') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Fecha de finalización *
        <input type="date" name="fecha" required min="1960-01-01" max="<?= $hoy ?>" value="<?= View::e($c['fecha'] ?? '') ?>"
               data-msg="Escribe una fecha válida: no puede ser futura ni anterior a 1960.">
      </label>
      <label>Intensidad (horas)
        <input type="number" name="horas" min="1" max="5000" step="1" inputmode="numeric" value="<?= View::e($c['horas'] ?? '') ?>" placeholder="Ej. 120">
      </label>
    </div>
    <button type="submit" class="btn btn-primary btn-sm"><?= $id ? 'Guardar cambios' : 'Agregar' ?></button>
  </form>
<?php };
?>

<div class="page-header">
  <div>
    <div class="page-subtitle">Diligencia tu hoja de vida paso a paso. Talento Humano la revisa y la aprueba o te dice qué corregir.</div>
  </div>
  <div class="page-actions">
    <span class="badge <?= View::hojaVidaEstadoBadge($estado) ?>"><?= View::hojaVidaEstadoLabel($estado) ?></span>
    <span class="badge badge-borrador">Versión <?= (int)$empleado['hojavida_version'] ?></span>
    <?php if (!empty($empleado['hojavida_fecha'])): ?><span class="badge badge-borrador">Fecha: <?= View::fecha($empleado['hojavida_fecha']) ?></span><?php endif; ?>
  </div>
</div>

<?php if ($estado === 'devuelta' && !empty($empleado['hojavida_observaciones'])): ?>
<div class="alert alert-error" style="margin-bottom:16px">
  <span><strong>Talento Humano devolvió tu hoja de vida.</strong> Corrige lo siguiente y vuelve a enviarla:<br>
  <?= nl2br(View::e($empleado['hojavida_observaciones'])) ?>
  <?php if (!empty($empleado['hojavida_revisada_por'])): ?><br><small>— <?= View::e($empleado['hojavida_revisada_por']) ?>, <?= View::fecha($empleado['hojavida_revisada_en']) ?></small><?php endif; ?></span>
</div>
<?php endif; ?>

<?php
$modificando = ($estado === 'borrador' && (int)$empleado['hojavida_version'] > 1);
$btnCancelar = $modificando
    ? '<button type="submit" form="formCancelar" class="btn btn-outline btn-sm" style="color:var(--danger);border-color:#fecaca">Cancelar cambios</button>'
    : '';
?>
<div class="cv-layout">
<nav class="cv-menu" aria-label="Secciones de la hoja de vida">
  <div class="cv-menu-titulo"><?= View::e($empleado['nombres'] . ' ' . $empleado['apellidos']) ?></div>
  <div class="cv-menu-sub">Hoja de vida · versión <?= (int)$empleado['hojavida_version'] ?></div>
  <?php
    $reqPasos = [1, 2, 3, 4, 6];
    $hechos = 0;
    foreach ($reqPasos as $rp) { if (empty($faltantes[$rp])) $hechos++; }
    $pctHv = (int)round($hechos * 100 / count($reqPasos));
  ?>
  <div class="cv-progreso" title="Secciones obligatorias completas">
    <div class="cv-progreso-txt"><span>Progreso</span><strong><?= $hechos ?> de <?= count($reqPasos) ?> secciones</strong></div>
    <div class="cv-progreso-barra"><div style="width:<?= $pctHv ?>%"></div></div>
  </div>
  <?php foreach ($pasos as $n => $nombre):
        $opcional = ($n === 5);
        $completo = $n < 7 && !$opcional && empty($faltantes[$n]);
        $conDatos = $opcional && !empty($complementaria);
        $clase = ($n === $paso ? 'actual ' : '') . (($completo || $conDatos) ? 'completo' : '');
        $puedeIr = $n === 7 || $editable;
  ?>
    <?php if ($puedeIr): ?><a href="<?= $base ?>/paso/<?= $n ?>" class="cv-item <?= $clase ?>"><?php else: ?><span class="cv-item <?= $clase ?>"><?php endif; ?>
      <span class="cv-item-ico"><?= ($completo || $conDatos) ? '✓' : $n ?></span>
      <span><?= View::e($nombre) ?><?= $opcional ? ' <em class="cv-opcional">Opcional</em>' : '' ?></span>
    <?= $puedeIr ? '</a>' : '</span>' ?>
  <?php endforeach; ?>
</nav>
<div class="cv-main">

<?php if ($estado === 'borrador' && (int)$empleado['hojavida_version'] > 1): ?>
<div class="alert alert-warning cv-modificando" style="margin-bottom:14px">
  <span>Estás <strong>modificando</strong> tu hoja de vida (será la versión <?= (int)$empleado['hojavida_version'] ?>). Si no quieres cambiar nada, puedes cancelar y volver a la versión aprobada.</span>
  <form id="formCancelar" method="POST" action="<?= $base ?>/cancelar-actualizacion" style="margin-left:auto" onsubmit="return confirm('¿Cancelar la modificación? Se descartarán los cambios que hiciste y tu hoja de vida volverá a la versión aprobada anterior.')">
    <button type="submit" class="btn btn-outline btn-sm" style="white-space:nowrap">Cancelar cambios</button>
  </form>
</div>
<?php endif; ?>

<?php if ($paso === 1): ?>
<form method="POST" action="<?= $base ?>/paso/1/guardar" class="card">
  <div class="card-header"><div class="card-title">1. Identificación</div></div>
  <div class="dato-grid" style="margin-bottom:14px">
    <div class="dato-item"><div class="dato-label">Documento</div><div class="dato-valor"><?= View::e($empleado['tipo_documento'] . ' ' . $empleado['numero_documento']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Nombres y apellidos</div><div class="dato-valor"><?= View::e($empleado['nombres'] . ' ' . $empleado['apellidos']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Correo</div><div class="dato-valor"><?= View::e($empleado['email'] ?: '—') ?></div></div>
  </div>
  <p class="text-muted" style="margin-bottom:12px">Documento, nombre y correo los registró Talento Humano. Si hay un error, pídeles que lo corrijan.</p>
  <?php $grupo = 'personal'; include ROOT . '/app/views/empleados/_campos_personales.php'; ?>
  <div class="page-actions" style="margin-top:16px">
    <button type="submit" class="btn btn-primary">Guardar y continuar →</button>
    <?= $btnCancelar ?>
  </div>
</form>

<?php elseif ($paso === 2): ?>
<form method="POST" action="<?= $base ?>/paso/2/guardar" class="card">
  <div class="card-header"><div class="card-title">2. Dirección residencial y contacto</div></div>
  <?php $grupo = 'contacto'; include ROOT . '/app/views/empleados/_campos_personales.php'; ?>
  <div class="page-actions" style="margin-top:16px">
    <a href="<?= $base ?>/paso/1" class="btn btn-outline btn-sm">← Anterior</a>
    <button type="submit" class="btn btn-primary">Guardar y continuar →</button>
    <?= $btnCancelar ?>
  </div>
</form>

<?php elseif ($paso === 3): ?>
<form method="POST" action="<?= $base ?>/paso/3/guardar" class="card">
  <div class="card-header"><div class="card-title">3. Seguridad social y cuenta bancaria</div></div>
  <?php $grupo = 'seguridad'; include ROOT . '/app/views/empleados/_campos_personales.php'; ?>
  <p class="text-muted" style="margin:6px 0 10px">El soporte de la certificación bancaria lo adjuntas luego en tu Lista de chequeo.</p>
  <?php $grupo = 'banco'; include ROOT . '/app/views/empleados/_campos_personales.php'; ?>
  <div class="page-actions" style="margin-top:16px">
    <a href="<?= $base ?>/paso/2" class="btn btn-outline btn-sm">← Anterior</a>
    <button type="submit" class="btn btn-primary">Guardar y continuar →</button>
    <?= $btnCancelar ?>
  </div>
</form>

<?php elseif ($paso === 4): ?>
<div class="card">
  <div class="card-header"><div class="card-title">4. Formación académica</div></div>
  <?php if (!$educacion): ?><p class="text-muted">Aún no has agregado estudios. Agrega al menos uno (por ejemplo, tu bachillerato).</p><?php endif; ?>
  <?php foreach ($educacion as $ed): ?>
    <div class="hoja-entry">
      <div class="hoja-entry-title">
        <?= View::nivelEducativoLabel($ed['nivel_educativo']) ?> — <?= View::e($ed['titulo_obtenido'] ?: '') ?>
        <?php if (!empty($ed['en_curso'])): ?><span class="badge badge-en-revision" style="font-size:10px;margin-left:4px">En curso</span><?php endif; ?>
      </div>
      <div class="hoja-entry-sub"><?= View::e($ed['institucion']) ?><?= !empty($ed['fecha_expedicion']) ? ' · Título expedido el ' . View::fecha($ed['fecha_expedicion']) : '' ?></div>
      <details style="margin-top:6px" <?= $editarEduId === (int)$ed['id'] ? 'open' : '' ?>>
        <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">Editar</summary>
        <div style="margin-top:10px"><?php $formFormacion($ed); ?></div>
      </details>
    </div>
  <?php endforeach; ?>
  <details style="margin-top:14px" <?= !$educacion ? 'open' : '' ?>>
    <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Agregar formación</summary>
    <div style="margin-top:10px"><?php $formFormacion(null); ?></div>
  </details>
  <div class="page-actions" style="margin-top:18px">
    <a href="<?= $base ?>/paso/3" class="btn btn-outline btn-sm">← Anterior</a>
    <a href="<?= $base ?>/paso/5" class="btn btn-primary">Continuar →</a>
    <?= $btnCancelar ?>
  </div>
</div>

<?php elseif ($paso === 5): ?>
<div class="card">
  <div class="card-header"><div class="card-title">5. Formación complementaria</div></div>
  <p class="text-muted" style="margin-bottom:10px">Cursos, diplomados, seminarios y certificaciones. Esta sección es opcional.</p>
  <?php if (!$complementaria): ?><p class="text-muted">Aún no has agregado formación complementaria.</p><?php endif; ?>
  <?php foreach ($complementaria as $c): ?>
    <div class="hoja-entry">
      <div class="hoja-entry-title"><?= View::e($c['nombre']) ?></div>
      <div class="hoja-entry-sub"><?= View::e($c['institucion']) ?> · <?= View::fecha($c['fecha']) ?><?= $c['horas'] ? ' · ' . (int)$c['horas'] . ' horas' : '' ?></div>
      <details style="margin-top:6px" <?= $editarCompId === (int)$c['id'] ? 'open' : '' ?>>
        <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">Editar</summary>
        <div style="margin-top:10px"><?php $formComplementaria($c); ?></div>
      </details>
    </div>
  <?php endforeach; ?>
  <details style="margin-top:14px">
    <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Agregar formación complementaria</summary>
    <div style="margin-top:10px"><?php $formComplementaria(null); ?></div>
  </details>
  <div class="page-actions" style="margin-top:18px">
    <a href="<?= $base ?>/paso/4" class="btn btn-outline btn-sm">← Anterior</a>
    <a href="<?= $base ?>/paso/6" class="btn btn-primary">Continuar →</a>
    <?= $btnCancelar ?>
  </div>
</div>

<?php elseif ($paso === 6): ?>
<div class="card">
  <div class="card-header"><div class="card-title">6. Experiencia profesional</div></div>
  <?php if (!$experiencia): ?>
    <p class="text-muted">Agrega tu experiencia laboral o, si es tu primer empleo, marca la casilla de abajo.</p>
  <?php endif; ?>
  <?php foreach ($experiencia as $ex): ?>
    <div class="hoja-entry">
      <div class="hoja-entry-title"><?= View::e($ex['cargo']) ?> — <?= View::e($ex['empresa']) ?></div>
      <div class="hoja-entry-sub"><?= View::fecha($ex['fecha_inicio']) ?> – <?= $ex['fecha_fin'] ? View::fecha($ex['fecha_fin']) : 'Actual' ?></div>
      <?php if ($ex['funciones']): ?><div style="font-size:12.5px;color:var(--txt2);margin-top:3px"><?= View::e($ex['funciones']) ?></div><?php endif; ?>
      <details style="margin-top:6px" <?= $editarExpId === (int)$ex['id'] ? 'open' : '' ?>>
        <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">Editar</summary>
        <div style="margin-top:10px"><?php $formExperiencia($ex); ?></div>
      </details>
    </div>
  <?php endforeach; ?>
  <details style="margin-top:14px" <?= (!$experiencia && !$sinExp) ? 'open' : '' ?>>
    <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Agregar experiencia</summary>
    <div style="margin-top:10px"><?php $formExperiencia(null); ?></div>
  </details>
  <?php if (!$experiencia): ?>
  <form method="POST" action="<?= $base ?>/sin-experiencia" style="margin-top:14px">
    <button type="submit" class="btn btn-outline btn-sm"><?= $sinExp ? '✓ Marcado: aún no tengo experiencia laboral (clic para quitar)' : 'Aún no tengo experiencia laboral' ?></button>
  </form>
  <?php endif; ?>
  <div class="page-actions" style="margin-top:18px">
    <a href="<?= $base ?>/paso/5" class="btn btn-outline btn-sm">← Anterior</a>
    <a href="<?= $base ?>/paso/7" class="btn btn-primary">Ver mi hoja de vida →</a>
    <?= $btnCancelar ?>
  </div>
</div>

<?php else: /* paso 7 */ ?>
<div class="card">
  <div class="card-header"><div class="card-title">7. Ver y enviar hoja de vida</div></div>

  <?php if ($faltantes && $editable): ?>
    <div class="alert alert-error" style="margin-bottom:14px">
      <span><strong>Tu hoja de vida aún tiene campos por completar:</strong>
        <ul style="margin:6px 0 0 18px">
          <?php foreach ($faltantes as $n => $msgs): foreach ($msgs as $m): ?>
            <li>Paso <?= $n ?> (<?= View::e($pasos[$n]) ?>): <?= View::e($m) ?> <a href="<?= $base ?>/paso/<?= $n ?>">Ir a corregir</a></li>
          <?php endforeach; endforeach; ?>
        </ul>
      </span>
    </div>
  <?php elseif ($editable): ?>
    <div class="alert alert-success" style="margin-bottom:14px"><span>Todo está completo. Revisa cómo quedó y envíala a Talento Humano.</span></div>
  <?php elseif ($estado === 'enviada'): ?>
    <div class="alert alert-warning" style="margin-bottom:14px"><span>Tu hoja de vida está <strong>en revisión</strong> por Talento Humano. Mientras tanto no se puede modificar.</span></div>
  <?php elseif ($estado === 'aprobada'): ?>
    <div class="alert alert-success" style="margin-bottom:14px"><span>Tu hoja de vida fue <strong>aprobada</strong><?= !empty($empleado['hojavida_revisada_por']) ? ' por ' . View::e($empleado['hojavida_revisada_por']) : '' ?>.</span></div>
  <?php endif; ?>

  <div class="hv-preview-wrap">
    <div class="hv-preview-bar">
      <div class="hv-preview-titulo">Vista previa · Formato FO-TH-018 · versión <?= (int)$empleado['hojavida_version'] ?></div>
      <div class="hv-preview-acciones">
        <a href="<?= APP_URL ?>/empleados/<?= (int)$empleado['id'] ?>/hojavida" class="btn btn-outline btn-sm">Pantalla completa</a>
        <button type="button" class="btn btn-primary btn-sm" onclick="var f=document.getElementById('hvFrame'); f.contentWindow.focus(); f.contentWindow.print();">Imprimir / Guardar PDF</button>
      </div>
    </div>
    <iframe id="hvFrame" src="<?= APP_URL ?>/empleados/<?= (int)$empleado['id'] ?>/hojavida?embed=1" title="Vista de mi hoja de vida" class="hv-preview" scrolling="no"></iframe>
  </div>
  <script>
  (function () {
    var f = document.getElementById('hvFrame');
    function ajustar() {
      try { f.style.height = (f.contentDocument.documentElement.scrollHeight + 4) + 'px'; } catch (e) {}
    }
    f.addEventListener('load', function () { ajustar(); setTimeout(ajustar, 400); });
    window.addEventListener('resize', ajustar);
  })();
  </script>

  <div class="page-actions" style="margin-top:16px">
    <?php if ($editable): ?>
      <a href="<?= $base ?>/paso/6" class="btn btn-outline btn-sm">← Anterior</a>
    <?php endif; ?>
    <?php if ($editable && !$faltantes): ?>
      <form method="POST" action="<?= $base ?>/enviar" style="display:inline" onsubmit="return confirm('¿Enviar tu hoja de vida a Talento Humano? No podrás modificarla mientras la revisan.')">
        <button type="submit" class="btn btn-primary">Enviar a revisión</button>
      </form>
    <?php endif; ?>
    <?php if ($estado === 'aprobada'): ?>
      <form method="POST" action="<?= $base ?>/actualizar" style="display:inline" onsubmit="return confirm('Se creará una nueva versión de tu hoja de vida y deberás enviarla de nuevo a revisión. ¿Continuar?')">
        <button type="submit" class="btn btn-primary">Actualizar mi hoja de vida (nueva versión)</button>
      </form>
    <?php endif; ?>
    <?= $btnCancelar ?>
  </div>
</div>
<?php endif; ?>

</div><!-- /cv-main -->
</div><!-- /cv-layout -->
