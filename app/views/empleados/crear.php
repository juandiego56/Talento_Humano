<div class="page-header">
  <div>
    <div class="page-title">Nuevo Empleado</div>
    <div class="page-subtitle">Registro de personal — módulo de Administración de Personal</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/empleados/guardar">

  <div class="card">
    <div class="card-header"><div class="card-title">Datos de identificación</div></div>
    <div class="form-row">
      <label>Tipo de documento
        <select name="tipo_documento" required>
          <option value="CC">Cédula de ciudadanía</option>
          <option value="CE">Cédula de extranjería</option>
          <option value="TI">Tarjeta de identidad</option>
          <option value="PA">Pasaporte</option>
        </select>
      </label>
      <label>Número de documento
        <input type="text" name="numero_documento" required>
      </label>
      <label>Fecha de nacimiento
        <input type="date" name="fecha_nacimiento">
      </label>
    </div>
    <div class="form-row">
      <label>Nombres
        <input type="text" name="nombres" required>
      </label>
      <label>Apellidos
        <input type="text" name="apellidos" required>
      </label>
      <label>Género
        <select name="genero">
          <option value="">Seleccionar…</option>
          <option value="M">Masculino</option>
          <option value="F">Femenino</option>
          <option value="Otro">Otro</option>
        </select>
      </label>
    </div>
    <div class="form-row">
      <label>Estado civil
        <input type="text" name="estado_civil" placeholder="Soltero(a), Casado(a)…">
      </label>
      <label>Tipo de sangre
        <input type="text" name="tipo_sangre" placeholder="O+, A-, ...">
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Datos de contacto</div></div>
    <div class="form-row">
      <label>Dirección
        <input type="text" name="direccion">
      </label>
      <label>Teléfono
        <input type="text" name="telefono">
      </label>
      <label>Correo electrónico
        <input type="email" name="email">
      </label>
    </div>
    <div class="form-row">
      <label>Contacto de emergencia
        <input type="text" name="contacto_emergencia_nombre">
      </label>
      <label>Teléfono de emergencia
        <input type="text" name="contacto_emergencia_telefono">
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
            <option value="<?= $c['id'] ?>"><?= View::e($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Área
        <select name="area_id">
          <option value="">Sin asignar</option>
          <?php foreach ($areas as $a): ?>
            <option value="<?= $a['id'] ?>"><?= View::e($a['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Fecha de ingreso
        <input type="date" name="fecha_ingreso" required value="<?= date('Y-m-d') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Tipo de contrato
        <select name="tipo_contrato" required>
          <option value="termino_fijo">Término Fijo</option>
          <option value="termino_indefinido">Término Indefinido</option>
          <option value="obra_labor">Obra o Labor</option>
          <option value="prestacion_servicios">Prestación de Servicios</option>
        </select>
      </label>
      <label>Salario básico
        <input type="number" name="salario_base" step="1000" min="0" required>
      </label>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Seguridad social</div></div>
    <div class="form-row">
      <label>EPS
        <input type="text" name="eps">
      </label>
      <label>Fondo de pensión
        <input type="text" name="fondo_pension">
      </label>
      <label>ARL
        <input type="text" name="arl">
      </label>
    </div>
  </div>

  <div class="page-actions">
    <button type="submit" class="btn btn-primary">Guardar empleado</button>
    <a href="<?= APP_URL ?>/empleados" class="btn btn-outline">Cancelar</a>
  </div>
</form>
