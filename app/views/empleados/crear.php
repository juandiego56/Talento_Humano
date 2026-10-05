<div class="page-header">
  <div>
    <div class="page-title">Nuevo Empleado</div>
    <div class="page-subtitle">Registro de personal — módulo de Administración de Personal</div>
  </div>
  <div class="page-actions">
    <a href="<?= APP_URL ?>/empleados" class="btn btn-outline">← Volver</a>
  </div>
</div>

<form method="POST" action="<?= APP_URL ?>/empleados/guardar" id="formEmpleado">

  <div class="form-tabs" role="tablist">
    <button type="button" class="form-tab active" data-tab="identificacion">1. Identificación</button>
    <button type="button" class="form-tab" data-tab="contacto">2. Contacto</button>
    <button type="button" class="form-tab" data-tab="laboral">3. Laboral</button>
    <button type="button" class="form-tab" data-tab="seguridad">4. Seguridad Social</button>
    <button type="button" class="form-tab" data-tab="bancaria">5. Cuenta Bancaria</button>
  </div>

  <div class="card tab-panel active" data-tab="identificacion">
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
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-primary btn-sm btn-next" data-next="contacto">Siguiente: Contacto →</button>
    </div>
  </div>

  <div class="card tab-panel" data-tab="contacto">
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
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-outline btn-sm btn-prev" data-prev="identificacion">← Anterior</button>
      <button type="button" class="btn btn-primary btn-sm btn-next" data-next="laboral">Siguiente: Laboral →</button>
    </div>
  </div>

  <div class="card tab-panel" data-tab="laboral">
    <div class="card-header"><div class="card-title">Información laboral</div></div>
    <div class="form-row">
      <label>Cargo
        <select name="cargo_id" id="selCargoEsc">
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
      <label>Programa académico <span class="text-muted" style="font-weight:400">(si aplica — docentes)</span>
        <select name="programa_id">
          <option value="">No aplica</option>
          <?php foreach ($programas as $p): ?>
            <option value="<?= $p['id'] ?>"><?= View::e($p['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Fecha de ingreso
        <input type="date" name="fecha_ingreso" required value="<?= date('Y-m-d') ?>">
      </label>
    </div>
    <div class="form-row">
      <label>Nivel de formación <span class="text-muted" style="font-weight:400">(para escalafón)</span>
        <select name="nivel_educativo" id="selNivelEsc">
          <option value="">No aplica</option>
          <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
            <option value="<?= $n ?>"><?= View::nivelEducativoLabel($n) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Dedicación docente
        <select name="tipo_vinculacion_docente" id="selDedicacionEsc">
          <option value="">No aplica</option>
          <option value="tiempo_completo">Tiempo Completo</option>
          <option value="medio_tiempo">Medio Tiempo</option>
          <option value="hora_catedra">Hora Cátedra</option>
        </select>
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
        <input type="number" name="salario_base" id="inpSalarioBase" step="1000" min="0" required>
      </label>
    </div>
    <div id="hintEscalafon" style="display:none;font-size:12px;color:var(--muted);margin-top:-6px;margin-bottom:10px">
      Salario sugerido por escalafón: <strong class="valor"></strong>
      <button type="button" id="btnUsarSugerido" class="btn btn-sm btn-outline" style="margin-left:6px">Usar este valor</button>
    </div>
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-outline btn-sm btn-prev" data-prev="contacto">← Anterior</button>
      <button type="button" class="btn btn-primary btn-sm btn-next" data-next="seguridad">Siguiente: Seguridad Social →</button>
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

  <div class="card tab-panel" data-tab="seguridad">
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
      <label>Nivel de riesgo ARL
        <select name="arl_nivel_riesgo">
          <?php foreach ($nivelesArl as $n): ?>
            <option value="<?= (int)$n['nivel'] ?>" <?= (int)$n['nivel'] === 1 ? 'selected' : '' ?>>
              <?= View::e($n['descripcion']) ?> (<?= number_format((float)$n['tasa'] * 100, 3) ?>%)
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-outline btn-sm btn-prev" data-prev="laboral">← Anterior</button>
      <button type="button" class="btn btn-primary btn-sm btn-next" data-next="bancaria">Siguiente: Cuenta Bancaria →</button>
    </div>
  </div>

  <div class="card tab-panel" data-tab="bancaria">
    <div class="card-header"><div class="card-title">Cuenta bancaria</div></div>
    <p class="text-muted" style="margin-bottom:10px">El soporte de certificación bancaria se adjunta luego desde la Lista de Chequeo.</p>
    <div class="form-row">
      <label>Banco
        <input type="text" name="banco">
      </label>
      <label>Tipo de cuenta
        <select name="tipo_cuenta">
          <option value="">Seleccionar…</option>
          <option value="ahorros">Ahorros</option>
          <option value="corriente">Corriente</option>
        </select>
      </label>
      <label>Número de cuenta
        <input type="text" name="numero_cuenta">
      </label>
    </div>
    <div class="page-actions" style="margin-top:16px">
      <button type="button" class="btn btn-outline btn-sm btn-prev" data-prev="seguridad">← Anterior</button>
      <button type="submit" class="btn btn-primary">Guardar empleado</button>
      <a href="<?= APP_URL ?>/empleados" class="btn btn-text">Cancelar</a>
    </div>
  </div>

  <script>
  (function(){
    var tabs   = Array.prototype.slice.call(document.querySelectorAll('#formEmpleado .form-tab'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('#formEmpleado .tab-panel'));
    var form   = document.getElementById('formEmpleado');

    function showTab(name) {
      tabs.forEach(function(t){ t.classList.toggle('active', t.dataset.tab === name); });
      panels.forEach(function(p){ p.classList.toggle('active', p.dataset.tab === name); });
      window.scrollTo({top: 0, behavior: 'smooth'});
    }

    tabs.forEach(function(t){
      t.addEventListener('click', function(){ showTab(t.dataset.tab); });
    });
    Array.prototype.slice.call(document.querySelectorAll('#formEmpleado .btn-next')).forEach(function(b){
      b.addEventListener('click', function(){ showTab(b.dataset.next); });
    });
    Array.prototype.slice.call(document.querySelectorAll('#formEmpleado .btn-prev')).forEach(function(b){
      b.addEventListener('click', function(){ showTab(b.dataset.prev); });
    });

    // Si al enviar hay un campo obligatorio sin llenar en una pestaña oculta,
    // mostrarla primero para que el usuario vea el error nativo del navegador.
    if (form) form.addEventListener('submit', function(e){
      for (var i = 0; i < panels.length; i++) {
        var invalid = panels[i].querySelector(':invalid');
        if (invalid) {
          e.preventDefault();
          showTab(panels[i].dataset.tab);
          invalid.reportValidity();
          return;
        }
      }
    });
  })();
  </script>
</form>
