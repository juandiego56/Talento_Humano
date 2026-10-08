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
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/hojavida" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width:16px;height:16px">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-1.519-2.019a2.25 2.25 0 10-3.462 0M13.481 15.231L15 17.25m-3.462-2.019L9 17.25m3.75-15h-6a2.25 2.25 0 00-2.25 2.25v15a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25V9.75L14.25 3.75z"/>
      </svg>
      Hoja de vida
    </a>
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

<?php $hvEstado = $empleado['hojavida_estado'] ?? 'borrador'; $esPropio = Auth::empleadoId() === (int)$empleado['id']; ?>
<div class="card">
  <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
    <div class="card-title">Hoja de vida</div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
      <span class="badge <?= View::hojaVidaEstadoBadge($hvEstado) ?>"><?= View::hojaVidaEstadoLabel($hvEstado) ?></span>
      <span class="badge badge-borrador">Versión <?= (int)($empleado['hojavida_version'] ?? 1) ?></span>
      <?php if (!empty($empleado['hojavida_fecha'])): ?><span class="badge badge-borrador">Fecha: <?= View::fecha($empleado['hojavida_fecha']) ?></span><?php endif; ?>
    </div>
  </div>

  <?php if ($esPropio): ?>
    <a href="<?= APP_URL ?>/mi-hoja-de-vida" class="btn btn-primary btn-sm">
      <?= in_array($hvEstado, ['borrador', 'devuelta'], true) ? 'Diligenciar mi hoja de vida' : 'Ver mi hoja de vida' ?>
    </a>
  <?php elseif (Auth::puedeGestionar()): ?>
    <?php if ($hvEstado === 'borrador'): ?>
      <p class="text-muted">El empleado aún no ha enviado su hoja de vida. La diligencia él mismo con su usuario; aquí solo se visualiza y se valida.</p>
    <?php elseif ($hvEstado === 'devuelta'): ?>
      <p class="text-muted">Devuelta al empleado con estas observaciones: <?= nl2br(View::e($empleado['hojavida_observaciones'] ?? '')) ?></p>
    <?php elseif ($hvEstado === 'enviada'): ?>
      <p class="text-muted" style="margin-bottom:10px">El empleado envió su hoja de vida<?= !empty($empleado['hojavida_enviada_en']) ? ' el ' . View::fecha($empleado['hojavida_enviada_en']) : '' ?>. Revísala (botón "Hoja de vida" de arriba) y apruébala o devuélvela indicando qué debe corregir.</p>
      <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/hojavida/revisar">
        <label>Observaciones <span class="text-muted" style="font-weight:400">(obligatorias si la devuelves)</span>
          <textarea name="observaciones" rows="3" maxlength="1000" placeholder="Ej. La dirección está incompleta y falta la fecha de expedición del título profesional."></textarea>
        </label>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
          <button type="submit" name="accion" value="aprobar" class="btn btn-primary btn-sm" onclick="return confirm('¿Aprobar esta hoja de vida?')">Aprobar hoja de vida</button>
          <button type="submit" name="accion" value="devolver" class="btn btn-outline btn-sm" style="color:var(--danger);border-color:#fecaca">Devolver con observaciones</button>
        </div>
      </form>
    <?php else: ?>
      <p class="text-muted">Aprobada<?= !empty($empleado['hojavida_revisada_por']) ? ' por ' . View::e($empleado['hojavida_revisada_por']) : '' ?><?= !empty($empleado['hojavida_revisada_en']) ? ' el ' . View::fecha($empleado['hojavida_revisada_en']) : '' ?>.</p>
    <?php endif; ?>
  <?php endif; ?>
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
      </div>
    <?php endforeach; else: ?>
      <p class="text-muted">El empleado aún no ha registrado su formación académica en la hoja de vida.</p>
    <?php endif; ?>

  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Experiencia laboral</div></div>
    <?php if ($experiencia): foreach ($experiencia as $ex): ?>
      <div class="hoja-entry">
        <div class="hoja-entry-title"><?= View::e($ex['cargo']) ?> — <?= View::e($ex['empresa']) ?></div>
        <div class="hoja-entry-sub"><?= View::fecha($ex['fecha_inicio']) ?> – <?= $ex['fecha_fin'] ? View::fecha($ex['fecha_fin']) : 'Actual' ?></div>
        <?php if ($ex['funciones']): ?><div style="font-size:12.5px;color:var(--txt2);margin-top:3px"><?= View::e($ex['funciones']) ?></div><?php endif; ?>
      </div>
    <?php endforeach; else: ?>
      <p class="text-muted">El empleado aún no ha registrado experiencia laboral en la hoja de vida.</p>
    <?php endif; ?>

  </div>

</div>

<?php if (!empty($complementaria)): ?>
<div class="card">
  <div class="card-header"><div class="card-title">Formación complementaria</div></div>
  <?php foreach ($complementaria as $c): ?>
    <div class="hoja-entry">
      <div class="hoja-entry-title"><?= View::e($c['nombre']) ?></div>
      <div class="hoja-entry-sub"><?= View::e($c['institucion']) ?> · <?= View::fecha($c['fecha']) ?><?= $c['horas'] ? ' · ' . (int)$c['horas'] . ' horas' : '' ?></div>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

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

<?php if (Auth::puedeGestionar()): ?>
<div class="card" style="border-color:#fecaca">
  <div class="card-header"><div class="card-title" style="color:var(--danger)">Gestión de acceso y baja</div></div>
  <?php if (empty($usuario)): ?>
  <p class="text-muted" style="margin-bottom:10px">Este empleado todavía no tiene usuario para ingresar a la plataforma y diligenciar su hoja de vida.</p>
  <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/usuario" style="margin-bottom:14px">
    <button type="submit" class="btn btn-primary">Crear usuario del empleado</button>
  </form>
  <?php else: ?>
  <p class="text-muted" style="margin-bottom:10px">Usuario de acceso: <strong><?= View::e($usuario['email']) ?></strong> · <?= !empty($usuario['activo']) ? 'activo' : 'sin acceso' ?>.</p>
  <?php endif; ?>
  <?php if ($empleado['estado'] !== 'inactivo' || !empty($usuario['activo'])): ?>
  <p class="text-muted" style="margin-bottom:10px">Dar de baja deja al empleado como inactivo, registra la fecha de retiro y le quita el acceso a la plataforma. No se borra su historial (hoja de vida, checklist, nómina).</p>
  <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/eliminar" onsubmit="return confirm('¿Dar de baja a este empleado? Quedará inactivo y no podrá ingresar a la plataforma.')">
    <button type="submit" class="btn btn-outline" style="color:var(--danger);border-color:#fecaca">Dar de baja al empleado</button>
  </form>
  <?php endif; ?>

</div>
<?php endif; ?>
