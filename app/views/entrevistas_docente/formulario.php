<div class="page-header">
  <div>
    <div class="page-title">Entrevista de Personal Docente — FO-TH-031</div>
    <div class="page-subtitle"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista-docente/guardar">

  <div class="card">
    <div class="card-header"><div class="card-title">Datos del entrevistador</div></div>
    <div class="form-row">
      <label>Nombre del entrevistador
        <input type="text" name="nombre_entrevistador" value="<?= View::e($entrevista['nombre_entrevistador'] ?? '') ?>">
      </label>
      <label>Cargo
        <input type="text" name="cargo_entrevistador" value="<?= View::e($entrevista['cargo_entrevistador'] ?? '') ?>">
      </label>
      <label>Programa académico
        <input type="text" name="programa_academico" value="<?= View::e($entrevista['programa_academico'] ?? '') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Fecha de la entrevista
        <input type="date" name="fecha_entrevista" value="<?= View::e($entrevista['fecha_entrevista'] ?? '') ?>">
      </label>
      <label>Modalidad
        <select name="modalidad">
          <option value="">Seleccionar…</option>
          <option value="presencial" <?= ($entrevista['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
          <option value="virtual" <?= ($entrevista['modalidad'] ?? '') === 'virtual' ? 'selected' : '' ?>>Virtual</option>
        </select>
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Datos del entrevistado</div></div>
    <div class="form-row">
      <label>Nombre del entrevistado
        <input type="text" name="nombre_entrevistado" value="<?= View::e($entrevista['nombre_entrevistado'] ?? ($empleado['nombres'].' '.$empleado['apellidos'])) ?>">
      </label>
      <label>Profesión
        <input type="text" name="profesion" value="<?= View::e($entrevista['profesion'] ?? '') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Posgrados
        <select name="posgrados_estado">
          <option value="">Seleccionar…</option>
          <option value="en_curso" <?= ($entrevista['posgrados_estado'] ?? '') === 'en_curso' ? 'selected' : '' ?>>En curso</option>
          <option value="culminado" <?= ($entrevista['posgrados_estado'] ?? '') === 'culminado' ? 'selected' : '' ?>>Culminado</option>
        </select>
      </label>
      <label>Detalle de posgrado
        <input type="text" name="posgrados_detalle" value="<?= View::e($entrevista['posgrados_detalle'] ?? '') ?>" placeholder="Nombre del posgrado">
      </label>
    </div>
    <div class="form-row">
      <label>Tipo de documento
        <select name="doc_tipo">
          <option value="">Seleccionar…</option>
          <option value="CC" <?= ($entrevista['doc_tipo'] ?? '') === 'CC' ? 'selected' : '' ?>>C.C.</option>
          <option value="CE" <?= ($entrevista['doc_tipo'] ?? '') === 'CE' ? 'selected' : '' ?>>C.E.</option>
        </select>
      </label>
      <label>No. Documento
        <input type="text" name="doc_numero" value="<?= View::e($entrevista['doc_numero'] ?? ($empleado['numero_documento'] ?? '')) ?>">
      </label>
      <label>Lugar de expedición
        <input type="text" name="doc_lugar_expedicion" value="<?= View::e($entrevista['doc_lugar_expedicion'] ?? '') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Tipo de vinculación docente
        <select name="tipo_vinculacion_docente">
          <option value="">Seleccionar…</option>
          <option value="tiempo_completo" <?= ($entrevista['tipo_vinculacion_docente'] ?? '') === 'tiempo_completo' ? 'selected' : '' ?>>Docente Tiempo Completo</option>
          <option value="medio_tiempo" <?= ($entrevista['tipo_vinculacion_docente'] ?? '') === 'medio_tiempo' ? 'selected' : '' ?>>Docente Medio Tiempo</option>
          <option value="hora_catedra" <?= ($entrevista['tipo_vinculacion_docente'] ?? '') === 'hora_catedra' ? 'selected' : '' ?>>Docente Hora Cátedra</option>
        </select>
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">1. Competencias personales</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Contexto social, presentación personal, actitud, aspiraciones a corto plazo — fortalezas y debilidades</p>
    <label>
      <textarea name="competencias_personales" rows="4"><?= View::e($entrevista['competencias_personales'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">2. Competencias académicas</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Logros académicos, investigaciones, producción intelectual, desarrollos y aportes significativos</p>
    <label>
      <textarea name="competencias_academicas" rows="4"><?= View::e($entrevista['competencias_academicas'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">3. Competencias profesionales</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Logros profesionales y aportes significativos o fortalezas en el campo profesional</p>
    <label>
      <textarea name="competencias_profesionales" rows="4"><?= View::e($entrevista['competencias_profesionales'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">4. Concepto general</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Concepto cualitativo frente al candidato y asignaturas que puede desarrollar en el programa académico</p>
    <label>
      <textarea name="concepto_general" rows="4"><?= View::e($entrevista['concepto_general'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Cierre de la entrevista</div></div>
    <div class="form-row">
      <label>5. ¿Disponibilidad de tiempo?
        <select name="disponibilidad_tiempo">
          <option value="">Seleccionar…</option>
          <option value="si" <?= ($entrevista['disponibilidad_tiempo'] ?? '') === 'si' ? 'selected' : '' ?>>SI</option>
          <option value="no" <?= ($entrevista['disponibilidad_tiempo'] ?? '') === 'no' ? 'selected' : '' ?>>NO</option>
        </select>
      </label>
      <label>6. ¿Acepta condiciones laborales?
        <select name="acepta_condiciones">
          <option value="">Seleccionar…</option>
          <option value="si" <?= ($entrevista['acepta_condiciones'] ?? '') === 'si' ? 'selected' : '' ?>>SI</option>
          <option value="no" <?= ($entrevista['acepta_condiciones'] ?? '') === 'no' ? 'selected' : '' ?>>NO</option>
        </select>
      </label>
    </div>
    <label>7. Conclusiones
      <textarea name="conclusiones" rows="4"><?= View::e($entrevista['conclusiones'] ?? '') ?></textarea>
    </label>
    <label style="margin-top:10px">Firma del entrevistador
      <input type="text" name="firma_entrevistador" value="<?= View::e($entrevista['firma_entrevistador'] ?? '') ?>" placeholder="Nombre de quien firma">
    </label>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary" style="width:100%">Guardar entrevista</button>
  </div>
</form>
