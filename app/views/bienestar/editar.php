<div class="page-header">
  <div>
    <div class="page-title">Editar Actividad</div>
    <div class="page-subtitle"><?= View::e($actividad['nombre']) ?></div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>/actualizar">
  <div class="card">
    <div class="form-row">
      <label>Nombre de la actividad
        <input type="text" name="nombre" required value="<?= View::e($actividad['nombre']) ?>">
      </label>
      <label>Tipo
        <select name="tipo" required>
          <?php foreach (['capacitacion','recreacion','salud','integracion','deportivo'] as $t): ?>
            <option value="<?= $t ?>" <?= $actividad['tipo']===$t?'selected':'' ?>><?= View::tipoActividadLabel($t) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <label>Descripción
      <textarea name="descripcion" rows="3"><?= View::e($actividad['descripcion']) ?></textarea>
    </label>
    <div class="form-row">
      <label>Fecha de inicio
        <input type="date" name="fecha_inicio" required value="<?= View::e($actividad['fecha_inicio']) ?>">
      </label>
      <label>Fecha de fin (opcional)
        <input type="date" name="fecha_fin" value="<?= View::e($actividad['fecha_fin']) ?>">
      </label>
      <label>Hora de inicio
        <input type="time" name="hora_inicio" value="<?= View::e($actividad['hora_inicio']) ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Lugar
        <input type="text" name="lugar" value="<?= View::e($actividad['lugar']) ?>">
      </label>
      <label>Responsable
        <input type="text" name="responsable" value="<?= View::e($actividad['responsable']) ?>">
      </label>
      <label>Cupo máximo (opcional)
        <input type="number" name="cupo_maximo" min="1" value="<?= View::e($actividad['cupo_maximo']) ?>">
      </label>
    </div>
    <label>Estado
      <select name="estado">
        <?php foreach (['programada','en_curso','finalizada','cancelada'] as $e): ?>
          <option value="<?= $e ?>" <?= $actividad['estado']===$e?'selected':'' ?>><?= View::estadoActividadLabel($e) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </div>

  <div class="page-actions">
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="<?= APP_URL ?>/bienestar/<?= $actividad['id'] ?>" class="btn btn-outline">Cancelar</a>
  </div>
</form>
