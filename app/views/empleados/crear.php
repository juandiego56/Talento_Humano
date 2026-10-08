<?php $v = fn(string $k) => View::e($old[$k] ?? ''); ?>
<div class="page-header">
  <div>
    <div class="page-title">Nuevo Empleado</div>
    <div class="page-subtitle">Registro de personal — módulo de Administración de Personal</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados" class="btn btn-outline">← Volver</a>
  </div>
</div>

<div class="alert alert-warning" style="margin-bottom:16px">
  <span>Aquí solo se registran los datos que define Talento Humano. Al guardar, el sistema crea el <strong>usuario del empleado</strong> (con su correo)
  y él mismo diligencia su hoja de vida: datos personales, contacto, seguridad social, cuenta bancaria, formación y experiencia.</span>
</div>

<form method="POST" action="<?= APP_URL ?>/empleados/guardar" id="formEmpleado" data-tabs novalidate>

  <div class="form-tabs" role="tablist">
    <button type="button" class="form-tab active" data-tab="identificacion">1. Identificación y acceso</button>
    <button type="button" class="form-tab" data-tab="laboral">2. Laboral</button>
  </div>

  <div class="card tab-panel active" data-tab="identificacion">
    <div class="card-header"><div class="card-title">Datos de identificación</div></div>
    <div class="form-row">
      <label>Tipo de documento *
        <select name="tipo_documento" required>
          <?php foreach (['CC'=>'Cédula de ciudadanía','CE'=>'Cédula de extranjería','TI'=>'Tarjeta de identidad','PA'=>'Pasaporte'] as $k => $lbl): ?>
            <option value="<?= $k ?>" <?= ($old['tipo_documento'] ?? 'CC') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Número de documento *
        <input type="text" name="numero_documento" required pattern="[A-Za-z0-9]{5,15}" maxlength="15" value="<?= $v('numero_documento') ?>"
               data-msg="Escribe entre 5 y 15 caracteres, solo letras y números, sin espacios ni puntos.">
      </label>
    </div>
    <div class="form-row">
      <label>Nombres *
        <input type="text" name="nombres" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ][A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-]*" value="<?= $v('nombres') ?>"
               data-msg="Los nombres solo pueden tener letras y espacios (mínimo 2 caracteres).">
      </label>
      <label>Apellidos *
        <input type="text" name="apellidos" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ][A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-]*" value="<?= $v('apellidos') ?>"
               data-msg="Los apellidos solo pueden tener letras y espacios (mínimo 2 caracteres).">
      </label>
    </div>
    <div class="form-row">
      <label>Correo electrónico * <span class="text-muted" style="font-weight:400">(será su usuario de acceso)</span>
        <input type="email" name="email" required maxlength="120" value="<?= $v('email') ?>" placeholder="nombre@correo.com"
               pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}" data-msg="Escribe un correo válido, por ejemplo nombre@correo.com.">
      </label>
    </div>
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-primary btn-sm btn-next">Siguiente: Laboral →</button>
    </div>
  </div>

  <div class="card tab-panel" data-tab="laboral">
    <div class="card-header"><div class="card-title">Información laboral</div></div>
    <div class="form-row">
      <label>Cargo
        <select name="cargo_id" id="selCargoEsc">
          <option value="">Sin asignar</option>
          <?php foreach ($cargos as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($old['cargo_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= View::e($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Área
        <select name="area_id">
          <option value="">Sin asignar</option>
          <?php foreach ($areas as $a): ?>
            <option value="<?= $a['id'] ?>" <?= ($old['area_id'] ?? '') == $a['id'] ? 'selected' : '' ?>><?= View::e($a['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Programa académico <span class="text-muted" style="font-weight:400">(si aplica — docentes)</span>
        <select name="programa_id">
          <option value="">No aplica</option>
          <?php foreach ($programas as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ($old['programa_id'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= View::e($p['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Fecha de ingreso *
        <input type="date" name="fecha_ingreso" required min="1990-01-01" max="<?= date('Y-m-d', strtotime('+1 year')) ?>"
               value="<?= View::e($old['fecha_ingreso'] ?? date('Y-m-d')) ?>"
               data-msg="Escribe una fecha de ingreso válida (entre 1990 y un año a partir de hoy).">
      </label>
    </div>
    <div class="form-row">
      <label>Nivel de formación <span class="text-muted" style="font-weight:400">(para escalafón)</span>
        <select name="nivel_educativo" id="selNivelEsc">
          <option value="">No aplica</option>
          <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
            <option value="<?= $n ?>" <?= ($old['nivel_educativo'] ?? '') === $n ? 'selected' : '' ?>><?= View::nivelEducativoLabel($n) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Dedicación docente
        <select name="tipo_vinculacion_docente" id="selDedicacionEsc">
          <option value="">No aplica</option>
          <?php foreach (['tiempo_completo'=>'Tiempo Completo','medio_tiempo'=>'Medio Tiempo','hora_catedra'=>'Hora Cátedra'] as $k => $lbl): ?>
            <option value="<?= $k ?>" <?= ($old['tipo_vinculacion_docente'] ?? '') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Nivel de riesgo ARL
        <select name="arl_nivel_riesgo">
          <?php foreach ($nivelesArl as $n): ?>
            <option value="<?= (int)$n['nivel'] ?>" <?= (int)($old['arl_nivel_riesgo'] ?? 1) === (int)$n['nivel'] ? 'selected' : '' ?>>
              <?= View::e($n['descripcion']) ?> (<?= number_format((float)$n['tasa'] * 100, 3) ?>%)
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="form-row">
      <label>Tipo de contrato *
        <select name="tipo_contrato" required>
          <?php foreach (['termino_fijo'=>'Término Fijo','termino_indefinido'=>'Término Indefinido','obra_labor'=>'Obra o Labor','prestacion_servicios'=>'Prestación de Servicios'] as $k => $lbl): ?>
            <option value="<?= $k ?>" <?= ($old['tipo_contrato'] ?? 'termino_fijo') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Salario básico * <span class="text-muted" style="font-weight:400">(admite decimales, ej. 1750905.50)</span>
        <input type="number" name="salario_base" id="inpSalarioBase" step="0.01" min="0.01" max="100000000" required inputmode="decimal"
               value="<?= $v('salario_base') ?>" data-msg="Escribe un salario mayor que cero (máximo 2 decimales).">
      </label>
    </div>
    <div id="hintEscalafon" style="display:none;font-size:12px;color:var(--muted);margin-top:-6px;margin-bottom:10px">
      Salario sugerido por escalafón: <strong class="valor"></strong>
      <button type="button" id="btnUsarSugerido" class="btn btn-sm btn-outline" style="margin-left:6px">Usar este valor</button>
    </div>
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-outline btn-sm btn-prev">← Anterior</button>
      <button type="submit" class="btn btn-primary">Guardar empleado</button>
      <a href="<?= APP_URL ?>/empleados" class="btn btn-text">Cancelar</a>
    </div>
  </div>

  <script>
  (function(){
    var ESCALAFON = <?= json_encode(array_map(function($e){
      return [
        'cargo' => (int)$e['cargo_id'],
        'nivel' => $e['nivel_educativo'],
        'tc'    => (float)$e['salario_tiempo_completo'],
        'mt'    => $e['salario_medio_tiempo'] !== null ? (float)$e['salario_medio_tiempo'] : null,
      ];
    }, $escalafon)) ?>;

    function fmt(n) {
      return '$ ' + Math.round(n).toLocaleString('es-CO');
    }

    function actualizarSugerencia() {
      var cargo = parseInt((document.getElementById('selCargoEsc') || {}).value || '0', 10);
      var nivel = (document.getElementById('selNivelEsc') || {}).value || '';
      var dedicacion = (document.getElementById('selDedicacionEsc') || {}).value || '';
      var hint = document.getElementById('hintEscalafon');
      if (!hint) return;
      if (!cargo || !nivel || (dedicacion !== 'tiempo_completo' && dedicacion !== 'medio_tiempo')) {
        hint.style.display = 'none';
        return;
      }
      var match = null;
      for (var i = 0; i < ESCALAFON.length; i++) {
        if (ESCALAFON[i].cargo === cargo && ESCALAFON[i].nivel === nivel) { match = ESCALAFON[i]; break; }
      }
      if (!match) { hint.style.display = 'none'; return; }
      var valor = dedicacion === 'tiempo_completo' ? match.tc : (match.mt !== null ? match.mt : match.tc / 2);
      hint.style.display = 'block';
      hint.querySelector('.valor').textContent = fmt(valor);
      hint.dataset.valor = Math.round(valor);
    }

    ['selCargoEsc', 'selNivelEsc', 'selDedicacionEsc'].forEach(function(id){
      var el = document.getElementById(id);
      if (el) el.addEventListener('change', actualizarSugerencia);
    });
    var btn = document.getElementById('btnUsarSugerido');
    if (btn) btn.addEventListener('click', function(){
      var hint = document.getElementById('hintEscalafon');
      var inp = document.getElementById('inpSalarioBase');
      if (hint && inp && hint.dataset.valor) inp.value = hint.dataset.valor;
    });
    actualizarSugerencia();
  })();
  </script>
</form>
