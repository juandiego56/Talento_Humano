<div class="page-header">
  <div>
    <div class="page-title">Entrevista de Personal Administrativo — FO-TH-009</div>
    <div class="page-subtitle"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/entrevista/guardar">

  <div class="card">
    <div class="card-header"><div class="card-title">Datos del entrevistador</div></div>
    <div class="form-row">
      <label>Nombre del entrevistador
        <input type="text" name="nombre_entrevistador" value="<?= View::e($entrevista['nombre_entrevistador'] ?? '') ?>">
      </label>
      <label>Cargo
        <input type="text" name="cargo_entrevistador" value="<?= View::e($entrevista['cargo_entrevistador'] ?? '') ?>">
      </label>
      <label>Área
        <input type="text" name="area_entrevistador" value="<?= View::e($entrevista['area_entrevistador'] ?? '') ?>">
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
      <label>Cargo al que aspira
        <input type="text" name="cargo_aspira" value="<?= View::e($entrevista['cargo_aspira'] ?? ($empleado['cargo'] ?? '')) ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Posgrados
        <select name="posgrados_estado">
          <option value="">Seleccionar…</option>
          <option value="no_aplica" <?= ($entrevista['posgrados_estado'] ?? '') === 'no_aplica' ? 'selected' : '' ?>>No Aplica</option>
          <option value="en_curso" <?= ($entrevista['posgrados_estado'] ?? '') === 'en_curso' ? 'selected' : '' ?>>En curso</option>
          <option value="culminado" <?= ($entrevista['posgrados_estado'] ?? '') === 'culminado' ? 'selected' : '' ?>>Culminado</option>
        </select>
      </label>
      <label>Detalle de posgrado
        <input type="text" name="posgrados_detalle" value="<?= View::e($entrevista['posgrados_detalle'] ?? '') ?>" placeholder="Nombre del posgrado, si aplica">
      </label>
    </div>
    <div class="form-row">
      <label>Tipo de documento
        <select name="doc_tipo">
          <option value="">Seleccionar…</option>
          <option value="TI" <?= ($entrevista['doc_tipo'] ?? '') === 'TI' ? 'selected' : '' ?>>T.I.</option>
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
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Información personal</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Personales – Familiares – Estado Civil</p>
    <label>
      <textarea name="informacion_personal" rows="4"><?= View::e($entrevista['informacion_personal'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Formación académica</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Estudios realizados, logros alcanzados e intereses</p>
    <label>
      <textarea name="formacion_academica" rows="4"><?= View::e($entrevista['formacion_academica'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Experiencia laboral</div></div>
    <p class="text-muted" style="margin:-4px 0 8px;font-size:12px">Cargos ocupados, tiempo de servicio, motivo de retiro, aspiraciones</p>
    <label>
      <textarea name="experiencia_laboral" rows="4"><?= View::e($entrevista['experiencia_laboral'] ?? '') ?></textarea>
    </label>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Resultados de pruebas</div></div>
    <div class="form-row">
      <label>Resultado prueba psicotécnica
        <textarea name="resultado_prueba_psicotecnica" rows="3"><?= View::e($entrevista['resultado_prueba_psicotecnica'] ?? '') ?></textarea>
      </label>
      <label>Resultado prueba técnica
        <textarea name="resultado_prueba_tecnica" rows="3"><?= View::e($entrevista['resultado_prueba_tecnica'] ?? '') ?></textarea>
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Cierre de la entrevista</div></div>
    <div class="form-row">
      <label>¿Disponibilidad de tiempo?
        <select name="disponibilidad_tiempo">
          <option value="">Seleccionar…</option>
          <option value="si" <?= ($entrevista['disponibilidad_tiempo'] ?? '') === 'si' ? 'selected' : '' ?>>SI</option>
          <option value="no" <?= ($entrevista['disponibilidad_tiempo'] ?? '') === 'no' ? 'selected' : '' ?>>NO</option>
        </select>
      </label>
      <label>¿Acepta condiciones laborales?
        <select name="acepta_condiciones">
          <option value="">Seleccionar…</option>
          <option value="si" <?= ($entrevista['acepta_condiciones'] ?? '') === 'si' ? 'selected' : '' ?>>SI</option>
          <option value="no" <?= ($entrevista['acepta_condiciones'] ?? '') === 'no' ? 'selected' : '' ?>>NO</option>
        </select>
      </label>
    </div>
    <label>Conclusiones
      <textarea name="conclusiones" rows="4"><?= View::e($entrevista['conclusiones'] ?? '') ?></textarea>
    </label>
    <div class="form-row">
      <label>Decisión
        <select name="decision">
          <option value="">Seleccionar…</option>
          <option value="vincular" <?= ($entrevista['decision'] ?? '') === 'vincular' ? 'selected' : '' ?>>Vincular</option>
          <option value="descartar" <?= ($entrevista['decision'] ?? '') === 'descartar' ? 'selected' : '' ?>>Descartar</option>
        </select>
      </label>
      <label>Firma del entrevistador
        <input type="text" name="firma_entrevistador" value="<?= View::e($entrevista['firma_entrevistador'] ?? '') ?>" placeholder="Nombre de quien firma">
      </label>
    </div>
  </div>

  <div class="card">
    <button type="submit" class="btn btn-primary" style="width:100%">Guardar entrevista</button>
  </div>
</form>
