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
        <button type="button" class="avatar-edit-btn" title="Cambiar foto"
                onclick="document.getElementById('input-foto-<?= $empleado['id'] ?>').click()">✎</button>
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
    </div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/hojavida" class="btn btn-outline" target="_blank">📄 Hoja de vida</a>
    <button type="button" class="btn btn-outline" onclick="sgthCopiarEnlace(this)" data-url="<?= View::e(rtrim(APP_URL, '/')) ?>/empleados/<?= $empleado['id'] ?>/diligenciar">🔗 Copiar enlace</button>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista" class="btn btn-outline">🗣️ Entrevista</a>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente" class="btn btn-outline">🎓 Entrevista docente</a>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/checklist" class="btn btn-outline">📋 Lista de chequeo</a>
    <?php if (Auth::puedeGestionar()): ?>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/editar" class="btn btn-primary">Editar</a>
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
    <div class="dato-item"><div class="dato-label">EPS / Pensión / ARL</div><div class="dato-valor"><?= View::e($empleado['eps'] ?: '—') ?> · <?= View::e($empleado['fondo_pension'] ?: '—') ?> · <?= View::e($empleado['arl'] ?: '—') ?></div></div>
    <div class="dato-item"><div class="dato-label">Tipo de sangre</div><div class="dato-valor"><?= View::e($empleado['tipo_sangre'] ?: '—') ?></div></div>
  </div>
</div>

<div class="form-row" style="align-items:start">

  <div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
      <div class="card-title">Formación académica</div>
      <?php if (Auth::puedeGestionar()): ?>
        <form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/educacion/convalidacion" style="display:inline">
          <button type="submit" class="btn btn-sm <?= !empty($empleado['formacion_convalidada']) ? 'btn-primary' : 'btn-outline' ?>">
            <?= !empty($empleado['formacion_convalidada']) ? '✅ Convalidado' : '⏳ No convalidado' ?>
          </button>
        </form>
      <?php else: ?>
        <span class="badge <?= !empty($empleado['formacion_convalidada']) ? 'badge-aprobado' : 'badge-borrador' ?>">
          <?= !empty($empleado['formacion_convalidada']) ? '✅ Convalidado' : '⏳ No convalidado' ?>
        </span>
      <?php endif; ?>
    </div>
    <?php if ($educacion): foreach ($educacion as $ed): ?>
      <div class="hoja-entry">
        <div class="hoja-entry-title"><?= View::nivelEducativoLabel($ed['nivel_educativo']) ?> — <?= View::e($ed['titulo_obtenido'] ?: '') ?></div>
        <div class="hoja-entry-sub"><?= View::e($ed['institucion']) ?><?= $ed['anio_graduacion'] ? ' · '.$ed['anio_graduacion'] : '' ?></div>
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
  const textoOriginal = btn.textContent;
  navigator.clipboard.writeText(url).then(function () {
    btn.textContent = '✅ Enlace copiado';
    setTimeout(function () { btn.textContent = textoOriginal; }, 2000);
  }).catch(function () {
    window.prompt('Copia el enlace manualmente:', url);
  });
}
</script>