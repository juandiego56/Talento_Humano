<style>
  .hv-form label { display:block; font-size:13px; font-weight:600; color:var(--txt2); margin-bottom:4px; }
  .hv-form input, .hv-form select, .hv-form textarea {
    width:100%; padding:9px 11px; border:1px solid var(--border); border-radius:8px; font-size:14px; margin-bottom:14px;
  }
  .hv-form .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .hv-block { border:1px solid var(--border); border-radius:10px; padding:16px; margin-bottom:16px; background:#fafafa; }
  .hv-errores { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:8px; margin-bottom:18px; }
</style>

<h2 style="margin-bottom:4px">Formato Único de Hoja de Vida</h2>
<p class="text-muted" style="margin-bottom:20px">FO-TH-018 · Diligencia tus datos para postularte. Los campos marcados con * son obligatorios.</p>

<?php if (!empty($errores)): ?>
  <div class="hv-errores">
    <strong>Revisa lo siguiente:</strong>
    <ul style="margin:6px 0 0 18px">
      <?php foreach ($errores as $err): ?><li><?= View::e($err) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form class="hv-form" method="POST" action="<?= $accionGuardar ?? (APP_URL . '/hoja-de-vida/guardar') ?>">

  <h3>I. Información personal</h3>
  <div class="form-row">
    <label>Tipo de documento *
      <select name="tipo_documento" required>
        <?php foreach (['CC'=>'Cédula de ciudadanía','CE'=>'Cédula de extranjería','TI'=>'Tarjeta de identidad','PA'=>'Pasaporte'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($datos['tipo_documento'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Número de documento *
      <input type="text" name="numero_documento" value="<?= View::e($datos['numero_documento'] ?? '') ?>" required>
    </label>
  </div>
  <div class="form-row">
    <label>Nombres *
      <input type="text" name="nombres" value="<?= View::e($datos['nombres'] ?? '') ?>" required>
    </label>
    <label>Apellidos *
      <input type="text" name="apellidos" value="<?= View::e($datos['apellidos'] ?? '') ?>" required>
    </label>
  </div>
  <div class="form-row">
    <label>Fecha de nacimiento
      <input type="date" name="fecha_nacimiento" value="<?= View::e($datos['fecha_nacimiento'] ?? '') ?>">
    </label>
    <label>Género
      <select name="genero">
        <option value="">Selecciona...</option>
        <option value="F" <?= ($datos['genero'] ?? '')==='F'?'selected':'' ?>>Femenino</option>
        <option value="M" <?= ($datos['genero'] ?? '')==='M'?'selected':'' ?>>Masculino</option>
        <option value="Otro" <?= ($datos['genero'] ?? '')==='Otro'?'selected':'' ?>>Otro</option>
      </select>
    </label>
  </div>
  <div class="form-row">
    <label>Estado civil
      <select name="estado_civil">
        <option value="">Selecciona...</option>
        <?php foreach (['Soltero(a)','Casado(a)','Unión libre','Viudo(a)'] as $ec): ?>
          <option value="<?= $ec ?>" <?= ($datos['estado_civil'] ?? '')===$ec?'selected':'' ?>><?= $ec ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Tipo de sangre
      <input type="text" name="tipo_sangre" placeholder="ej. O+" value="<?= View::e($datos['tipo_sangre'] ?? '') ?>">
    </label>
  </div>
  <label>Dirección de residencia
    <input type="text" name="direccion" value="<?= View::e($datos['direccion'] ?? '') ?>">
  </label>
  <div class="form-row">
    <label>Teléfono
      <input type="text" name="telefono" value="<?= View::e($datos['telefono'] ?? '') ?>">
    </label>
    <label>E-mail
      <input type="email" name="email" value="<?= View::e($datos['email'] ?? '') ?>">
    </label>
  </div>
  <div class="form-row">
    <label>Contacto de emergencia — Nombre
      <input type="text" name="contacto_emergencia_nombre" value="<?= View::e($datos['contacto_emergencia_nombre'] ?? '') ?>">
    </label>
    <label>Contacto de emergencia — Teléfono
      <input type="text" name="contacto_emergencia_telefono" value="<?= View::e($datos['contacto_emergencia_telefono'] ?? '') ?>">
    </label>
  </div>
  <div class="form-row">
    <label>EPS
      <input type="text" name="eps" value="<?= View::e($datos['eps'] ?? '') ?>">
    </label>
    <label>Fondo de pensión
      <input type="text" name="fondo_pension" value="<?= View::e($datos['fondo_pension'] ?? '') ?>">
    </label>
  </div>
  <label>ARL
    <input type="text" name="arl" value="<?= View::e($datos['arl'] ?? '') ?>">
  </label>

  <h3>II. Información académica</h3>
  <p class="text-muted" style="margin-bottom:10px">Agrega hasta 3 registros de formación (deja en blanco los que no apliquen).</p>
  <?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="hv-block">
      <strong>Formación <?= $i ?></strong>
      <div class="form-row" style="margin-top:10px">
        <label>Nivel educativo
          <select name="educ<?= $i ?>_nivel">
            <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
              <option value="<?= $n ?>" <?= ($datos["educ{$i}_nivel"] ?? '')===$n?'selected':'' ?>><?= View::nivelEducativoLabel($n) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Institución
          <input type="text" name="educ<?= $i ?>_institucion" value="<?= View::e($datos["educ{$i}_institucion"] ?? '') ?>">
        </label>
      </div>
      <div class="form-row">
        <label>Título obtenido
          <input type="text" name="educ<?= $i ?>_titulo" value="<?= View::e($datos["educ{$i}_titulo"] ?? '') ?>">
        </label>
        <label>Año de grado
          <input type="number" name="educ<?= $i ?>_anio" min="1960" max="2100" value="<?= View::e($datos["educ{$i}_anio"] ?? '') ?>">
        </label>
      </div>
    </div>
  <?php endfor; ?>

  <h3>III. Experiencia laboral</h3>
  <p class="text-muted" style="margin-bottom:10px">Relaciona hasta 3 experiencias, empezando por la más reciente.</p>
  <?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="hv-block">
      <strong>Experiencia <?= $i ?></strong>
      <div class="form-row" style="margin-top:10px">
        <label>Empresa u organización
          <input type="text" name="exp<?= $i ?>_empresa" value="<?= View::e($datos["exp{$i}_empresa"] ?? '') ?>">
        </label>
        <label>Cargo desempeñado
          <input type="text" name="exp<?= $i ?>_cargo" value="<?= View::e($datos["exp{$i}_cargo"] ?? '') ?>">
        </label>
      </div>
      <div class="form-row">
        <label>Fecha de inicio
          <input type="date" name="exp<?= $i ?>_inicio" value="<?= View::e($datos["exp{$i}_inicio"] ?? '') ?>">
        </label>
        <label>Fecha de finalización
          <input type="date" name="exp<?= $i ?>_fin" value="<?= View::e($datos["exp{$i}_fin"] ?? '') ?>">
        </label>
      </div>
      <label>Funciones
        <textarea name="exp<?= $i ?>_funciones" rows="2"><?= View::e($datos["exp{$i}_funciones"] ?? '') ?></textarea>
      </label>
    </div>
  <?php endfor; ?>

  <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:15px">Enviar hoja de vida</button>
</form>