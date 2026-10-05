<style>
  .hv-form label { display:block; font-size:12.5px; font-weight:600; color:var(--txt2); margin-bottom:5px; letter-spacing:.01em; }
  .hv-form input, .hv-form select, .hv-form textarea {
    width:100%; padding:10px 12px; border:1.5px solid var(--border); border-radius:9px; font-size:14px;
    margin-bottom:16px; background:#fff; color:var(--txt); font-family:inherit;
    transition:border-color .15s ease, box-shadow .15s ease;
  }
  .hv-form input:hover, .hv-form select:hover, .hv-form textarea:hover { border-color:var(--border-h); }
  .hv-form input:focus, .hv-form select:focus, .hv-form textarea:focus {
    outline:none; border-color:var(--blue); box-shadow:0 0 0 3px var(--blue-bg);
  }
  .hv-form input:disabled, .hv-form select:disabled, .hv-form textarea:disabled { background:#f1f5f9; color:var(--faint); border-color:var(--border); }
  .hv-form .form-row { display:grid; grid-template-columns:1fr 1fr; gap:0 16px; }

  /* Tarjetas de sección numeradas (I, II, III) */
  .hv-sec { border:1px solid var(--border); border-radius:12px; margin-bottom:22px; background:#fff; overflow:hidden; box-shadow:0 1px 3px rgba(10,37,64,.06); }
  .hv-sec-head { display:flex; align-items:center; gap:10px; background:var(--primary); color:#fff; padding:13px 18px; }
  .hv-sec-num {
    display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; flex-shrink:0;
    border-radius:50%; background:rgba(255,255,255,.16); font-size:12px; font-weight:700;
  }
  .hv-sec-head h3 { margin:0; font-size:14.5px; font-weight:700; letter-spacing:.02em; color:#fff; }
  .hv-sec-body { padding:20px 20px 4px; }
  .hv-sec-note { font-size:12.5px; color:var(--muted); margin:-6px 0 16px; }

  .hv-block { border:1px solid var(--border); border-radius:10px; padding:16px 18px; margin-bottom:16px; background:var(--surface2); }
  .hv-block-title {
    display:flex; align-items:center; gap:8px; font-size:12.5px; font-weight:700; color:var(--blue-h);
    text-transform:uppercase; letter-spacing:.04em; margin-bottom:2px;
  }
  .hv-block-title .n {
    display:inline-flex; align-items:center; justify-content:center; width:19px; height:19px; border-radius:50%;
    background:var(--blue-bg); color:var(--blue-h); font-size:11px;
  }

  .hv-errores { display:flex; gap:10px; align-items:flex-start; background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:13px 16px; border-radius:10px; margin-bottom:18px; }
  .hv-errores svg { width:18px; height:18px; flex-shrink:0; margin-top:1px; }

  .hv-no-aplica { border-top:1px dashed var(--border-h); margin-top:10px; padding-top:12px; }
  .hv-no-aplica label.hv-check {
    display:flex; align-items:center; gap:8px; font-weight:600; margin-bottom:0; font-size:13px; color:var(--txt2); cursor:pointer;
  }
  .hv-no-aplica label.hv-check input { width:auto; margin:0; accent-color:var(--blue); }
  .hv-no-aplica .form-row { margin-top:12px; margin-bottom:0; }
  .hv-no-aplica select, .hv-no-aplica input { margin-bottom:0; }

  .hv-submit-wrap { text-align:center; padding:6px 0 4px; }
  .hv-submit-wrap button {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    width:100%; padding:13px; font-size:15px; font-weight:600; border-radius:10px;
  }
  .hv-submit-note { font-size:12px; color:var(--faint); margin-top:10px; }
</style>
<script>
  function hvToggleNoAplica(chk, bloqueId) {
    var bloque = document.getElementById(bloqueId);
    var campos = bloque.querySelectorAll('.hv-campo-normal input, .hv-campo-normal select, .hv-campo-normal textarea');
    campos.forEach(function (c) { c.disabled = chk.checked; });
    var motivoWrap = document.getElementById(bloqueId + '_motivo_wrap');
    if (motivoWrap) motivoWrap.style.display = chk.checked ? 'flex' : 'none';
  }
</script>

<div class="hv-head">
  <div class="hv-head-logo">
    <img src="<?= APP_URL ?>/assets/img/logo-fup.png" alt="Fundación Universitaria De Popayán">
  </div>
  <div class="hv-head-title">
    <div class="hv-head-title-row top">Gestión del Talento Humano</div>
    <div class="hv-head-title-row">Formato Único Hoja de Vida</div>
  </div>
  <div class="hv-head-meta">
    <div>Código: FO-TH-018</div>
    <div>Versión: 07</div>
    <div>Fecha: Diciembre 2024</div>
  </div>
</div>
<p class="text-muted" style="margin-bottom:20px">Diligencia tus datos para postularte. Los campos marcados con * son obligatorios.</p>

<?php if (!empty($errores)): ?>
  <div class="hv-errores">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
    <div>
      <strong>Revisa lo siguiente:</strong>
      <ul style="margin:6px 0 0 18px">
        <?php foreach ($errores as $err): ?><li><?= View::e($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
  </div>
<?php endif; ?>

<form class="hv-form" method="POST" action="<?= $accionGuardar ?? (APP_URL . '/hoja-de-vida/guardar') ?>">

  <div class="hv-sec">
    <div class="hv-sec-head"><span class="hv-sec-num">1</span> <h3>Información personal</h3></div>
    <div class="hv-sec-body">
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

  <div class="form-row">
    <label>Banco
      <input type="text" name="banco" value="<?= View::e($datos['banco'] ?? '') ?>">
    </label>
    <label>Tipo de cuenta
      <select name="tipo_cuenta">
        <option value="">Seleccionar…</option>
        <option value="ahorros" <?= ($datos['tipo_cuenta'] ?? '')==='ahorros'?'selected':'' ?>>Ahorros</option>
        <option value="corriente" <?= ($datos['tipo_cuenta'] ?? '')==='corriente'?'selected':'' ?>>Corriente</option>
      </select>
    </label>
    <label>Número de cuenta
      <input type="text" name="numero_cuenta" value="<?= View::e($datos['numero_cuenta'] ?? '') ?>">
    </label>
  </div>
    </div>
  </div>

  <div class="hv-sec">
    <div class="hv-sec-head"><span class="hv-sec-num">2</span> <h3>Información académica</h3></div>
    <div class="hv-sec-body">
  <p class="hv-sec-note">Agrega hasta 3 registros de formación (deja en blanco los que no apliquen).</p>
  <?php for ($i = 1; $i <= 3; $i++): $noAplica = !empty($datos["educ{$i}_no_aplica"]); ?>
    <div class="hv-block" id="hv_educ<?= $i ?>">
      <div class="hv-block-title"><span class="n"><?= $i ?></span> Formación <?= $i ?></div>
      <div class="hv-campo-normal">
        <div class="form-row" style="margin-top:10px">
          <label>Nivel educativo
            <select name="educ<?= $i ?>_nivel" <?= $noAplica ? 'disabled' : '' ?>>
              <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
                <option value="<?= $n ?>" <?= ($datos["educ{$i}_nivel"] ?? '')===$n?'selected':'' ?>><?= View::nivelEducativoLabel($n) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Institución
            <input type="text" name="educ<?= $i ?>_institucion" value="<?= View::e($datos["educ{$i}_institucion"] ?? '') ?>" <?= $noAplica ? 'disabled' : '' ?>>
          </label>
        </div>
        <div class="form-row">
          <label>Título obtenido
            <input type="text" name="educ<?= $i ?>_titulo" value="<?= View::e($datos["educ{$i}_titulo"] ?? '') ?>" <?= $noAplica ? 'disabled' : '' ?>>
          </label>
          <label>Año de grado
            <input type="number" name="educ<?= $i ?>_anio" min="1960" max="2100" value="<?= View::e($datos["educ{$i}_anio"] ?? '') ?>" <?= $noAplica ? 'disabled' : '' ?>>
          </label>
        </div>
        <div class="form-row">
          <label>Fecha de expedición del título
            <input type="date" name="educ<?= $i ?>_fecha_expedicion" value="<?= View::e($datos["educ{$i}_fecha_expedicion"] ?? '') ?>" <?= $noAplica ? 'disabled' : '' ?>>
          </label>
          <label style="align-self:end;display:flex;align-items:center;gap:6px;margin-bottom:14px">
            <input type="checkbox" name="educ<?= $i ?>_en_curso" value="1" <?= !empty($datos["educ{$i}_en_curso"]) ? 'checked' : '' ?> <?= $noAplica ? 'disabled' : '' ?>>
            Actualmente en curso / no culminado
          </label>
        </div>
      </div>
      <div class="hv-no-aplica">
        <label class="hv-check">
          <input type="checkbox" name="educ<?= $i ?>_no_aplica" value="1" <?= $noAplica ? 'checked' : '' ?> onchange="hvToggleNoAplica(this, 'hv_educ<?= $i ?>')">
          No tengo este nivel de formación / no aplica
        </label>
        <div id="hv_educ<?= $i ?>_motivo_wrap" class="form-row" style="display:<?= $noAplica ? 'flex' : 'none' ?>">
          <label>Motivo
            <select name="educ<?= $i ?>_motivo">
              <?php foreach (['Aún no cuento con este nivel','En trámite','No aplica a mi caso','Otro'] as $m): ?>
                <option value="<?= $m ?>"><?= $m ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Especifica (si elegiste "Otro")
            <input type="text" name="educ<?= $i ?>_motivo_otro" value="<?= View::e($datos["educ{$i}_motivo_otro"] ?? '') ?>">
          </label>
        </div>
      </div>
    </div>
  <?php endfor; ?>
    </div>
  </div>

  <div class="hv-sec">
    <div class="hv-sec-head"><span class="hv-sec-num">3</span> <h3>Experiencia laboral</h3></div>
    <div class="hv-sec-body">
  <p class="hv-sec-note">Relaciona hasta 3 experiencias, empezando por la más reciente.</p>
  <?php for ($i = 1; $i <= 3; $i++): $expNoAplica = !empty($datos["exp{$i}_no_aplica"]); ?>
    <div class="hv-block" id="hv_exp<?= $i ?>">
      <div class="hv-block-title"><span class="n"><?= $i ?></span> Experiencia <?= $i ?></div>
      <div class="hv-campo-normal">
      <div class="form-row" style="margin-top:10px">
        <label>Empresa u organización
          <input type="text" name="exp<?= $i ?>_empresa" value="<?= View::e($datos["exp{$i}_empresa"] ?? '') ?>" <?= $expNoAplica ? 'disabled' : '' ?>>
        </label>
        <label>Cargo desempeñado
          <input type="text" name="exp<?= $i ?>_cargo" value="<?= View::e($datos["exp{$i}_cargo"] ?? '') ?>" <?= $expNoAplica ? 'disabled' : '' ?>>
        </label>
      </div>
      <div class="form-row">
        <label>Fecha de inicio
          <input type="date" name="exp<?= $i ?>_inicio" value="<?= View::e($datos["exp{$i}_inicio"] ?? '') ?>" <?= $expNoAplica ? 'disabled' : '' ?>>
        </label>
        <label>Fecha de finalización
          <input type="date" name="exp<?= $i ?>_fin" value="<?= View::e($datos["exp{$i}_fin"] ?? '') ?>" <?= $expNoAplica ? 'disabled' : '' ?>>
        </label>
      </div>
      <label>Funciones
        <textarea name="exp<?= $i ?>_funciones" rows="2" <?= $expNoAplica ? 'disabled' : '' ?>><?= View::e($datos["exp{$i}_funciones"] ?? '') ?></textarea>
      </label>
      </div>
      <div class="hv-no-aplica">
        <label class="hv-check">
          <input type="checkbox" name="exp<?= $i ?>_no_aplica" value="1" <?= $expNoAplica ? 'checked' : '' ?> onchange="hvToggleNoAplica(this, 'hv_exp<?= $i ?>')">
          No tengo experiencia laboral que relacionar aquí / no aplica
        </label>
        <div id="hv_exp<?= $i ?>_motivo_wrap" class="form-row" style="display:<?= $expNoAplica ? 'flex' : 'none' ?>">
          <label>Motivo
            <select name="exp<?= $i ?>_motivo">
              <?php foreach (['Sin experiencia laboral previa','En trámite de certificación','No aplica a mi caso','Otro'] as $m): ?>
                <option value="<?= $m ?>"><?= $m ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Especifica (si elegiste "Otro")
            <input type="text" name="exp<?= $i ?>_motivo_otro" value="<?= View::e($datos["exp{$i}_motivo_otro"] ?? '') ?>">
          </label>
        </div>
      </div>
    </div>
  <?php endfor; ?>
    </div>
  </div>

  <div class="hv-submit-wrap">
    <button type="submit" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
      Enviar hoja de vida
    </button>
    <p class="hv-submit-note">Tus datos quedarán asociados a tu registro en Talento Humano.</p>
  </div>
</form>