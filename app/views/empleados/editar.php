<div class="page-header">
  <div>
    <div class="page-title">Editar Empleado</div>
    <div class="page-subtitle"><?= View::e($empleado['nombres'].' '.$empleado['apellidos']) ?></div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>/actualizar">

  <div class="card">
    <div class="card-header"><div class="card-title">Datos de identificación</div></div>
    <div class="form-row">
      <label>Tipo de documento
        <select name="tipo_documento" required>
          <?php foreach (['CC'=>'Cédula de ciudadanía','CE'=>'Cédula de extranjería','TI'=>'Tarjeta de identidad','PA'=>'Pasaporte'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $empleado['tipo_documento']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Número de documento
        <input type="text" name="numero_documento" required value="<?= View::e($empleado['numero_documento']) ?>">
      </label>
      <label>Fecha de nacimiento
        <input type="date" name="fecha_nacimiento" value="<?= View::e($empleado['fecha_nacimiento']) ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Nombres
        <input type="text" name="nombres" required value="<?= View::e($empleado['nombres']) ?>">
      </label>
      <label>Apellidos
        <input type="text" name="apellidos" required value="<?= View::e($empleado['apellidos']) ?>">
      </label>
      <label>Género
        <select name="genero">
          <option value="">Seleccionar…</option>
          <?php foreach (['M'=>'Masculino','F'=>'Femenino','Otro'=>'Otro'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $empleado['genero']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="form-row">
      <label>Estado civil
        <input type="text" name="estado_civil" value="<?= View::e($empleado['estado_civil']) ?>">
      </label>
      <label>Tipo de sangre
        <input type="text" name="tipo_sangre" value="<?= View::e($empleado['tipo_sangre']) ?>">
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Datos de contacto</div></div>
    <div class="form-row">
      <label>Dirección
        <input type="text" name="direccion" value="<?= View::e($empleado['direccion']) ?>">
      </label>
      <label>Teléfono
        <input type="text" name="telefono" value="<?= View::e($empleado['telefono']) ?>">
      </label>
      <label>Correo electrónico
        <input type="email" name="email" value="<?= View::e($empleado['email']) ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Contacto de emergencia
        <input type="text" name="contacto_emergencia_nombre" value="<?= View::e($empleado['contacto_emergencia_nombre']) ?>">
      </label>
      <label>Teléfono de emergencia
        <input type="text" name="contacto_emergencia_telefono" value="<?= View::e($empleado['contacto_emergencia_telefono']) ?>">
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Información laboral</div></div>
    <div class="form-row">
      <label>Cargo
        <select name="cargo_id">
          <option value="">Sin asignar</option>
          <?php foreach ($cargos as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (string)$empleado['cargo_id']===(string)$c['id']?'selected':'' ?>><?= View::e($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Área
        <select name="area_id">
          <option value="">Sin asignar</option>
          <?php foreach ($areas as $a): ?>
            <option value="<?= $a['id'] ?>" <?= (string)$empleado['area_id']===(string)$a['id']?'selected':'' ?>><?= View::e($a['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Fecha de ingreso
        <input type="date" name="fecha_ingreso" required value="<?= View::e($empleado['fecha_ingreso']) ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Tipo de contrato
        <select name="tipo_contrato" required>
          <?php foreach (['termino_fijo'=>'Término Fijo','termino_indefinido'=>'Término Indefinido','obra_labor'=>'Obra o Labor','prestacion_servicios'=>'Prestación de Servicios'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $empleado['tipo_contrato']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Salario básico
        <input type="number" name="salario_base" step="1000" min="0" required value="<?= (float)$empleado['salario_base'] ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Estado
        <select name="estado" id="selEstado" onchange="document.getElementById('wrapRetiro').style.display=this.value==='retirado'?'block':'none'">
          <?php foreach (['activo'=>'Activo','inactivo'=>'Inactivo','retirado'=>'Retirado'] as $k=>$v): ?>
            <option value="<?= $k ?>" <?= $empleado['estado']===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label id="wrapRetiro" style="<?= $empleado['estado']==='retirado'?'':'display:none' ?>">Fecha de retiro
        <input type="date" name="fecha_retiro" value="<?= View::e($empleado['fecha_retiro']) ?>">
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Seguridad social</div></div>
    <div class="form-row">
      <label>EPS
        <input type="text" name="eps" value="<?= View::e($empleado['eps']) ?>">
      </label>
      <label>Fondo de pensión
        <input type="text" name="fondo_pension" value="<?= View::e($empleado['fondo_pension']) ?>">
      </label>
      <label>ARL
        <input type="text" name="arl" value="<?= View::e($empleado['arl']) ?>">
      </label>
    </div>
  </div>

  <div class="page-actions">
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="<?= APP_URL ?>/empleados/<?= $empleado['id'] ?>" class="btn btn-outline">Cancelar</a>
  </div>
</form>
