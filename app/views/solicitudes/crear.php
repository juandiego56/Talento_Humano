<div class="page-header">
  <div>
    <div class="page-title">Nueva Solicitud de Vinculación</div>
    <div class="page-subtitle">Enviar a Talento Humano para su revisión</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/solicitudes-vinculacion" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/solicitudes-vinculacion/guardar">
  <div class="card">
    <div class="card-header"><div class="card-title">Datos del candidato</div></div>
    <div class="form-row">
      <label>Nombres
        <input type="text" name="nombres_candidato" required>
      </label>
      <label>Apellidos
        <input type="text" name="apellidos_candidato" required>
      </label>
    </div>
    <div class="form-row">
      <label>Cargo sugerido
        <input type="text" name="cargo_sugerido" placeholder="Ej. Docente Tiempo Completo">
      </label>
      <label>Fecha requerida de vinculación
        <input type="date" name="fecha_requerida">
      </label>
    </div>
    <div class="form-row">
      <label>Nivel de formación requerido
        <select name="nivel_educativo_requerido">
          <option value="">No aplica</option>
          <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
            <option value="<?= $n ?>"><?= View::nivelEducativoLabel($n) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Dedicación docente
        <select name="tipo_vinculacion_docente">
          <option value="">No aplica</option>
          <option value="tiempo_completo">Tiempo Completo</option>
          <option value="medio_tiempo">Medio Tiempo</option>
          <option value="hora_catedra">Hora Cátedra</option>
        </select>
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Justificación</div></div>
    <label>Explique la necesidad de esta vinculación
      <textarea name="justificacion" rows="5" required placeholder="Carga académica, crecimiento del programa, reemplazo, etc."></textarea>
    </label>
  </div>

  <div class="page-actions">
    <button type="submit" class="btn btn-primary">Enviar solicitud</button>
    <a href="<?= APP_URL ?>/solicitudes-vinculacion" class="btn btn-text">Cancelar</a>
  </div>
</form>
