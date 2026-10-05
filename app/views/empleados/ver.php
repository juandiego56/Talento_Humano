<div class="page-header">
  <div style="display:flex;align-items:center;gap:14px">
    <div class="avatar-wrap">
      <div class="avatar-circle">
        <?php if (!empty($empleado['foto_path'])): ?>
          <img src="<?= FOTO_UPLOAD_URL ?>/<?= View::e($empleado['foto_path']) ?>" alt="Foto de <?= View::e($empleado['nombres']) ?>">
        <?php else: ?>
          <?= mb_strtoupper(mb_substr($empleado['nombres'],0,1)) ?>
        <?php endif; ?>
      </div>
      <?php $puedeEditarFoto = Auth::puedeGestionar() || (int)(Auth::user()['empleado_id'] ?? 0) === (int)$empleado['id']; ?>
      <?php if ($puedeEditarFoto): ?>
        <form id="form-foto-<?= $empleado['id'] ?>" method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/foto" enctype="multipart/form-data" style="display:none">
          <input type="file" id="input-foto-<?= $empleado['id'] ?>" name="foto" accept=".jpg,.jpeg,.png,.webp"
                 onchange="this.form.submit()">
        </form>
        <button type="button" class="avatar-edit-btn" title="Cambiar foto — debe ser tipo selfie, con fondo blanco y buena iluminación"
                onclick="document.getElementById('input-foto-<?= $empleado['id'] ?>').click()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:12px;height:12px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 6.75l2.25 2.25"/>
          </svg>
        </button>
      <?php endif; ?>
    </div>
    <div>
      <div class="page-title"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></div>
      <div class="page-subtitle"><?= View::e($empleado['cargo'] ?? 'Sin cargo asignado') ?> · <?= View::e($empleado['area'] ?? 'Sin área') ?></div>
      <?php if ($puedeEditarFoto && !empty($empleado['foto_path'])): ?>
        <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/foto/eliminar" style="display:inline" onsubmit="return confirm('¿Quitar la foto de perfil?')">
          <button type="submit" class="btn-link-sm" style="border:none;background:none;color:var(--muted);font-size:11px;cursor:pointer;padding:0;margin-top:2px">Quitar foto</button>
        </form>
      <?php endif; ?>
      <?php if ($puedeEditarFoto): ?>
        <div class="text-muted" style="font-size:11px;margin-top:2px">Foto tipo selfie, fondo blanco</div>
      <?php endif; ?>
    </div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/hojavida" class="btn btn-outline" target="_blank">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-1.519-2.019a2.25 2.25 0 10-3.462 0M13.481 15.231L15 17.25m-3.462-2.019L9 17.25m3.75-15h-6a2.25 2.25 0 00-2.25 2.25v15a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25V9.75L14.25 3.75z"/>
      </svg>
      Hoja de vida
    </a>
    <button type="button" class="btn btn-outline" onclick="sgthCopiarEnlace(this)" data-url="<?= View::e(rtrim(APP_URL, '/')) ?>/empleados/<?= $empleado['id'] ?>/diligenciar">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
      </svg>
      Copiar enlace
    </button>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.5-1.185A8.959 8.959 0 013 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
      </svg>
      Entrevista
    </a>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
      </svg>
      Entrevista docente
    </a>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Lista de chequeo
    </a>
    <?php if (Auth::puedeGestionar()): ?>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/editar" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
      </svg>
      Editar
    </a>
    <?php endif; ?>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card stat-personal">
    <div class="stat-num" style="font-size:16px"><span class="badge <?= View::estadoEmpleadoBadge($empleado['estado']) ?>"><?= View::estadoEmpleadoLabel($empleado['estado']) ?></span></div>
    <div class="stat-label">Estado laboral</div>
  </div>
  <div class="stat-card stat-nomina">
    <div class="stat-num" style="font-size:20px"><?= View::money($empleado['salario_base']) ?></div>
    <div class="stat-label">Salario básico</div>
  </div>
  <div class="stat-card stat-checklist">
    <div class="stat-num"><?= (float)($checklist['pct_completitud'] ?? 0) ?>%</div>
    <div class="stat-label">Lista de chequeo (<?= (int)($checklist['entregados'] ?? 0) ?>/<?= (int)($checklist['total_documentos'] ?? 0) ?>)</div>
  </div>
  <div class="stat-card stat-bienestar">
    <div class="stat-num"><?= count($actividades) ?></div>
    <div class="stat-label">Actividades de bienestar</div>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Datos generales</div></div>
  <div class="dato-grid">
    <div class="dato-item"><div class="dato-label">Documento</div><div class="dato-valor"><?= View::e($empleado['tipo_documento'].' '.$empleado['numero_documento']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Fecha de nacimiento</div><div class="dato-valor"><?= View::fecha($empleado['fecha_nacimiento']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Género</div><div class="dato-valor"><?= View::e($empleado['genero'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Estado civil</div><div class="dato-valor"><?= View::e($empleado['estado_civil'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Teléfono</div><div class="dato-valor"><?= View::e($empleado['telefono'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Correo</div><div class="dato-valor"><?= View::e($empleado['email'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Dirección</div><div class="dato-valor"><?= View::e($empleado['direccion'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Contacto de emergencia</div><div class="dato-valor"><?= View::e($empleado['contacto_emergencia_nombre'] ?: '—') ?> <?= $empleado['contacto_emergencia_telefono'] ? '('.View::e($empleado['contacto_emergencia_telefono']).')' : '' ?></div></div>
    <div class="dato-item"><div class="dato-label">Fecha de ingreso</div><div class="dato-valor"><?= View::fecha($empleado['fecha_ingreso']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Tipo de contrato</div><div class="dato-valor"><?= View::tipoContratoLabel($empleado['tipo_contrato']) ?></div></div>
    <div class="dato-item"><div class="dato-label">Programa académico</div><div class="dato-valor"><?= View::e($empleado['programa'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Nivel de formación (escalafón)</div><div class="dato-valor"><?= $empleado['nivel_educativo'] ? View::nivelEducativoLabel($empleado['nivel_educativo']) : '—' ?></div></div>
    <div class="dato-item"><div class="dato-label">Dedicación docente</div><div class="dato-valor"><?= $empleado['tipo_vinculacion_docente'] ? View::tipoVinculacionDocenteLabel($empleado['tipo_vinculacion_docente']) : '—' ?></div></div>
    <div class="dato-item"><div class="dato-label">EPS / Pensión / ARL</div><div class="dato-valor"><?= View::e($empleado['eps'] ?: '—') ?> · <?= View::e($empleado['fondo_pension'] ?: '—') ?> · <?= View::e($empleado['arl'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Nivel de riesgo ARL</div><div class="dato-valor"><?= View::arlNivelRiesgoLabel($empleado['arl_nivel_riesgo'] ?? null) ?></div></div>
    <div class="dato-item"><div class="dato-label">Tipo de sangre</div><div class="dato-valor"><?= View::e($empleado['tipo_sangre'] ?: '—') ?></div></div>
    <div class="dato-item">
      <div class="dato-label">Cuenta bancaria</div>
      <div class="dato-valor">
        <?php if (!empty($empleado['banco'])): ?>
          <?= View::e($empleado['banco']) ?> · <?= $empleado['tipo_cuenta'] === 'corriente' ? 'Corriente' : ($empleado['tipo_cuenta'] === 'ahorros' ? 'Ahorros' : '—') ?> · <?= View::e($empleado['numero_cuenta'] ?: '—') ?>
        <?php else: ?>—<?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="form-row" style="align-items:start">

  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
      <div class="card-title">Formación académica</div>
      <?php if (Auth::puedeGestionar()): ?>
        <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/educacion/convalidacion" style="display:inline">
          <button type="submit" class="btn btn-sm <?= !empty($empleado['formacion_convalidada']) ? 'btn-primary' : 'btn-outline' ?>">
            <?= View::convalidadaIcon(!empty($empleado['formacion_convalidada'])) ?> <?= !empty($empleado['formacion_convalidada']) ? 'Convalidado' : 'No convalidado' ?>
          </button>
        </form>
      <?php else: ?>
        <span class="badge <?= !empty($empleado['formacion_convalidada']) ? 'badge-aprobado' : 'badge-borrador' ?>">
          <?= View::convalidadaIcon(!empty($empleado['formacion_convalidada'])) ?> <?= !empty($empleado['formacion_convalidada']) ? 'Convalidado' : 'No convalidado' ?>
        </span>
      <?php endif; ?>
    </div>
    <?php if ($educacion): foreach ($educacion as $ed): ?>
      <div class="hoja-entry">
        <div class="hoja-entry-title">
          <?= View::nivelEducativoLabel($ed['nivel_educativo']) ?> — <?= View::e($ed['titulo_obtenido'] ?: '') ?>
          <?php if (!empty($ed['en_curso'])): ?><span class="badge badge-en-revision" style="font-size:10px;margin-left:4px">En curso</span><?php endif; ?>
        </div>
        <div class="hoja-entry-sub">
          <?= View::e($ed['institucion']) ?><?= $ed['anio_graduacion'] ? ' · '.$ed['anio_graduacion'] : '' ?>
          <?php if (!empty($ed['fecha_expedicion'])): ?> · Título expedido <?= View::fecha($ed['fecha_expedicion']) ?><?php endif; ?>
        </div>
        <?php if (Auth::puedeGestionar()): ?>
        <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/educacion/<?= $ed['id'] ?>/eliminar" style="display:inline" onsubmit="return confirm('¿Eliminar este registro?')">
          <button type="submit" class="btn btn-sm btn-outline" style="margin-top:4px;color:var(--danger);border-color:#fecaca">Eliminar</button>
        </form>
        <?php endif; ?>
      </div>
    <?php endforeach; else: ?>
      <p class="text-muted">Sin registros de formación académica.</p>
    <?php endif; ?>

    <?php if (Auth::puedeGestionar()): ?>
    <details style="margin-top:14px">
      <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Agregar formación</summary>
      <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/educacion/guardar" style="margin-top:10px">
        <div class="form-row">
          <label>Nivel
            <select name="nivel_educativo" required>
              <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
                <option value="<?= $n ?>"><?= View::nivelEducativoLabel($n) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div class="form-row">
          <label>Institución<input type="text" name="institucion" required></label>
          <label>Título obtenido<input type="text" name="titulo_obtenido"></label>
          <label>Año de grado<input type="number" name="anio_graduacion" min="1960" max="2100"></label>
        </div>
        <div class="form-row">
          <label>Fecha de expedición del título<input type="date" name="fecha_expedicion"></label>
          <label style="align-self:end;display:flex;align-items:center;gap:6px;margin-bottom:14px">
            <input type="checkbox" name="en_curso" value="1"> En curso / no culminado
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Agregar</button>
      </form>
    </details>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Experiencia laboral</div></div>
    <?php if ($experiencia): foreach ($experiencia as $ex): ?>
      <div class="hoja-entry">
        <div class="hoja-entry-title"><?= View::e($ex['cargo']) ?> — <?= View::e($ex['empresa']) ?></div>
        <div class="hoja-entry-sub"><?= View::fecha($ex['fecha_inicio']) ?> – <?= $ex['fecha_fin'] ? View::fecha($ex['fecha_fin']) : 'Actual' ?></div>
        <?php if ($ex['funciones']): ?><div style="font-size:12.5px;color:var(--txt2);margin-top:3px"><?= View::e($ex['funciones']) ?></div><?php endif; ?>
        <?php if (Auth::puedeGestionar()): ?>
        <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/experiencia/<?= $ex['id'] ?>/eliminar" style="display:inline" onsubmit="return confirm('¿Eliminar este registro?')">
          <button type="submit" class="btn btn-sm btn-outline" style="margin-top:4px;color:var(--danger);border-color:#fecaca">Eliminar</button>
        </form>
        <?php endif; ?>
      </div>
    <?php endforeach; else: ?>
      <p class="text-muted">Sin registros de experiencia laboral.</p>
    <?php endif; ?>

    <?php if (Auth::puedeGestionar()): ?>
    <details style="margin-top:14px">
      <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Agregar experiencia</summary>
      <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/experiencia/guardar" style="margin-top:10px">
        <div class="form-row">
          <label>Empresa<input type="text" name="empresa" required></label>
          <label>Cargo<input type="text" name="cargo" required></label>
        </div>
        <div class="form-row">
          <label>Fecha inicio<input type="date" name="fecha_inicio"></label>
          <label>Fecha fin<input type="date" name="fecha_fin"></label>
        </div>
        <label>Funciones<textarea name="funciones" rows="2"></textarea></label>
        <button type="submit" class="btn btn-primary btn-sm">Agregar</button>
      </form>
    </details>
    <?php endif; ?>
  </div>

</div>

<?php if (!empty($notasHojaVida)): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Secciones marcadas como "no aplica" en la hoja de vida</div></div>
  <?php foreach ($notasHojaVida as $n): ?>
    <div class="hoja-entry">
      <div class="hoja-entry-title"><?= View::e($n['seccion']) ?></div>
      <?php if ($n['motivo']): ?><div class="hoja-entry-sub">Motivo: <?= View::e($n['motivo']) ?></div><?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><div class="card-title">Historial de nómina reciente</div>
    <a href="<?= APP_URL ?>/nomina" class="btn btn-outline btn-sm">Ver nómina</a>
  </div>
  <table>
    <thead><tr><th>Periodo</th><th>Devengado</th><th>Deducciones</th><th>Neto</th><th>Estado</th></tr></thead>
    <tbody>
      <?php if (!$historialNomina): ?><tr><td colspan="5" class="text-muted">Sin registros de nómina.</td></tr><?php endif; ?>
      <?php foreach ($historialNomina as $h): ?>
      <tr>
        <td><?= View::periodoLabel($h['periodo']) ?></td>
        <td><?= View::money($h['total_devengado']) ?></td>
        <td><?= View::money($h['total_deducciones']) ?></td>
        <td><strong><?= View::money($h['neto_pagar']) ?></strong></td>
        <td><span class="badge <?= View::estadoNominaBadge($h['estado_nomina']) ?>"><?= View::estadoNominaLabel($h['estado_nomina']) ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-header"><div class="card-title">Participación en actividades de bienestar</div></div>
  <table>
    <thead><tr><th>Actividad</th><th>Tipo</th><th>Fecha</th><th>Asistencia</th></tr></thead>
    <tbody>
      <?php if (!$actividades): ?><tr><td colspan="4" class="text-muted">Sin inscripciones a actividades.</td></tr><?php endif; ?>
      <?php foreach ($actividades as $a): ?>
      <tr>
        <td><?= View::e($a['nombre']) ?></td>
        <td><span class="badge badge-tipo-<?= $a['tipo'] ?>"><?= View::tipoActividadLabel($a['tipo']) ?></span></td>
        <td><?= View::fecha($a['fecha_inicio']) ?></td>
        <td><?= $a['asistio'] ? '<span class="badge badge-aprobado">Asistió</span>' : '<span class="badge badge-borrador">Pendiente</span>' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if (Auth::esAdmin()): ?>
<div class="card" style="border-color:#fecaca">
  <div class="card-header"><div class="card-title" style="color:var(--danger)">Zona de riesgo</div></div>
  <p class="text-muted" style="margin-bottom:10px">Eliminar este empleado borrará también su hoja de vida, checklist e historial asociado.</p>
  <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/eliminar" onsubmit="return confirm('Esta acción no se puede deshacer. ¿Eliminar definitivamente a este empleado?')">
    <button type="submit" class="btn btn-outline" style="color:var(--danger);border-color:#fecaca">Eliminar empleado</button>
  </form>
</div>
<?php endif; ?>

<script>
function sgthCopiarEnlace(btn) {
  const url = btn.getAttribute('data-url');
  const htmlOriginal = btn.innerHTML;
  navigator.clipboard.writeText(url).then(function () {
    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Enlace copiado';
    setTimeout(function () { btn.innerHTML = htmlOriginal; }, 2000);
  }).catch(function () {
    window.prompt('Copia el enlace manualmente:', url);
  });
}
</script>