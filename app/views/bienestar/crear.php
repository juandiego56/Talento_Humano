<div class="page-header">
  <div>
    <div class="page-title">Nueva Actividad de Bienestar</div>
    <div class="page-subtitle">Módulo de Bienestar Laboral</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/bienestar/guardar">
  <div class="card">
    <div class="form-row">
      <label>Nombre de la actividad
        <input type="text" name="nombre" required>
      </label>
      <label>Tipo
        <select name="tipo" required>
          <?php foreach (['capacitacion','recreacion','salud','integracion','deportivo'] as $t): ?>
            <option value="<?= $t ?>"><?= View::tipoActividadLabel($t) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <label>Descripción
      <textarea name="descripcion" rows="3"></textarea>
    </label>
    <div class="form-row">
      <label>Fecha de inicio
        <input type="date" name="fecha_inicio" required>
      </label>
      <label>Fecha de fin (opcional)
        <input type="date" name="fecha_fin">
      </label>
      <label>Hora de inicio
        <input type="time" name="hora_inicio">
      </label>
    </div>
    <div class="form-row">
      <label>Lugar
        <input type="text" name="lugar">
      </label>
      <label>Responsable
        <input type="text" name="responsable">
      </label>
      <label>Cupo máximo (opcional)
        <input type="number" name="cupo_maximo" min="1">
      </label>
    </div>
  </div>

  <div class="page-actions">
    <button type="submit" class="btn btn-primary">Crear actividad</button>
    <a href="<?= APP_URL ?>/bienestar" class="btn btn-outline">Cancelar</a>
  </div>
</form>
