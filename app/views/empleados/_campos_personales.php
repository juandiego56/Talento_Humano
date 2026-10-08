<?php
/**
 * Campos de datos personales del empleado, por grupo. Los usa la hoja de vida (el empleado los diligencia)
 * y la edición de Talento Humano (donde los ya diligenciados quedan bloqueados).
 *
 * Variables: $datos (array), $grupo ('personal'|'contacto'|'seguridad'|'banco'),
 *            $exigir (bool: campos obligatorios), $bloq (callable: fn(string $campo): bool)
 */
$bloq   = $bloq   ?? fn(string $c): bool => false;
$exigir = $exigir ?? true;
$val    = fn(string $c): string => View::e($datos[$c] ?? '');
$req    = $exigir ? 'required' : '';
$ro     = fn(string $c): string => $bloq($c) ? 'readonly tabindex="-1" class="campo-bloqueado" title="Ya fue diligenciado por el empleado"' : '';
$dis    = fn(string $c): string => $bloq($c) ? 'disabled class="campo-bloqueado" title="Ya fue diligenciado por el empleado"' : '';
$ast    = $exigir ? ' *' : '';
$hoy    = new DateTime('today');
$minNac = (clone $hoy)->modify('-80 years')->format('Y-m-d');
$maxNac = (clone $hoy)->modify('-18 years')->format('Y-m-d');
?>
<?php if ($grupo === 'personal'): ?>
<div class="form-row">
  <label>Fecha de nacimiento<?= $ast ?>
    <input type="date" name="fecha_nacimiento" <?= $req ?> min="<?= $minNac ?>" max="<?= $maxNac ?>" value="<?= $val('fecha_nacimiento') ?>" <?= $ro('fecha_nacimiento') ?>
           data-msg="Debes ser mayor de 18 años (fecha entre <?= date('d/m/Y', strtotime($minNac)) ?> y <?= date('d/m/Y', strtotime($maxNac)) ?>).">
  </label>
  <label>Género<?= $ast ?>
    <select name="genero" <?= $req ?> <?= $dis('genero') ?>>
      <option value="">Seleccionar…</option>
      <?php foreach (['M'=>'Masculino','F'=>'Femenino','Otro'=>'Otro'] as $k => $lbl): ?>
        <option value="<?= $k ?>" <?= ($datos['genero'] ?? '') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Estado civil<?= $ast ?>
    <select name="estado_civil" <?= $req ?> <?= $dis('estado_civil') ?>>
      <option value="">Seleccionar…</option>
      <?php foreach (Validador::ESTADOS_CIVILES as $ec): ?>
        <option value="<?= View::e($ec) ?>" <?= ($datos['estado_civil'] ?? '') === $ec ? 'selected' : '' ?>><?= View::e($ec) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Tipo de sangre<?= $ast ?>
    <select name="tipo_sangre" <?= $req ?> <?= $dis('tipo_sangre') ?>>
      <option value="">Seleccionar…</option>
      <?php foreach (Validador::TIPOS_SANGRE as $ts): ?>
        <option value="<?= $ts ?>" <?= ($datos['tipo_sangre'] ?? '') === $ts ? 'selected' : '' ?>><?= $ts ?></option>
      <?php endforeach; ?>
    </select>
  </label>
</div>

<?php elseif ($grupo === 'contacto'): ?>
<div class="form-row">
  <label>Dirección de residencia<?= $ast ?>
    <input type="text" name="direccion" <?= $req ?> minlength="6" maxlength="150" value="<?= $val('direccion') ?>" <?= $ro('direccion') ?> placeholder="Calle 5 # 3-20, Barrio Centro"
           data-msg="Escribe una dirección completa con letras y números (ej. Calle 5 # 3-20).">
  </label>
  <label>Teléfono / celular<?= $ast ?>
    <input type="text" name="telefono" <?= $req ?> inputmode="numeric" data-digitos="10" maxlength="10" pattern="3\d{9}|\d{7}" value="<?= $val('telefono') ?>" <?= $ro('telefono') ?> placeholder="3101234567"
           data-msg="El celular debe tener 10 dígitos y empezar por 3 (ej. 3101234567), o un fijo de 7 dígitos.">
  </label>
</div>
<div class="form-row">
  <label>Nombre del contacto de emergencia<?= $ast ?>
    <input type="text" name="contacto_emergencia_nombre" <?= $req ?> minlength="2" maxlength="100" value="<?= $val('contacto_emergencia_nombre') ?>" <?= $ro('contacto_emergencia_nombre') ?>
           pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ][A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s.'\-]*" data-msg="Escribe el nombre completo del contacto (solo letras y espacios).">
  </label>
  <label>Teléfono del contacto de emergencia<?= $ast ?>
    <input type="text" name="contacto_emergencia_telefono" <?= $req ?> inputmode="numeric" data-digitos="10" maxlength="10" pattern="3\d{9}|\d{7}" value="<?= $val('contacto_emergencia_telefono') ?>" <?= $ro('contacto_emergencia_telefono') ?> placeholder="3101234567"
           data-msg="El celular debe tener 10 dígitos y empezar por 3 (ej. 3101234567), o un fijo de 7 dígitos.">
  </label>
</div>

<?php elseif ($grupo === 'seguridad'): ?>
<?php
  $fondoActual = trim((string)($datos['fondo_pension'] ?? ''));
  $esOtroFondo = $fondoActual !== '' && !in_array($fondoActual, Validador::FONDOS_PENSION, true);
  $selFondo    = $esOtroFondo ? 'Otro' : $fondoActual;
?>
<div class="form-row">
  <label>EPS<?= $ast ?>
    <input type="text" name="eps" <?= $req ?> minlength="2" maxlength="100" value="<?= $val('eps') ?>" <?= $ro('eps') ?> placeholder="Ej. Sanitas, Nueva EPS, Coosalud…">
  </label>
  <label>Fondo de pensión<?= $ast ?>
    <select name="fondo_pension" <?= $req ?> data-otro="wrapFondoOtro" <?= $dis('fondo_pension') ?>>
      <option value="">Seleccionar…</option>
      <?php foreach (Validador::FONDOS_PENSION as $fp): ?>
        <option value="<?= View::e($fp) ?>" <?= $selFondo === $fp ? 'selected' : '' ?>><?= View::e($fp) ?></option>
      <?php endforeach; ?>
      <option value="Otro" <?= $selFondo === 'Otro' ? 'selected' : '' ?>>Otro (no está en la lista)</option>
    </select>
  </label>
  <label id="wrapFondoOtro" style="display:none">¿Cuál fondo?
    <input type="text" name="fondo_pension_otro" maxlength="100" value="<?= $esOtroFondo ? View::e($fondoActual) : '' ?>" <?= $ro('fondo_pension') ?> placeholder="Nombre del fondo">
  </label>
  <label>ARL<?= $ast ?>
    <input type="text" name="arl" <?= $req ?> minlength="2" maxlength="100" value="<?= $val('arl') ?>" <?= $ro('arl') ?> placeholder="Ej. Positiva, Sura, Colmena…">
  </label>
</div>

<?php elseif ($grupo === 'banco'): ?>
<div class="form-row">
  <label>Banco<?= $ast ?>
    <input type="text" name="banco" <?= $req ?> minlength="2" maxlength="100" value="<?= $val('banco') ?>" <?= $ro('banco') ?> placeholder="Ej. Bancolombia">
  </label>
  <label>Tipo de cuenta<?= $ast ?>
    <select name="tipo_cuenta" <?= $req ?> <?= $dis('tipo_cuenta') ?>>
      <option value="">Seleccionar…</option>
      <option value="ahorros" <?= ($datos['tipo_cuenta'] ?? '') === 'ahorros' ? 'selected' : '' ?>>Ahorros</option>
      <option value="corriente" <?= ($datos['tipo_cuenta'] ?? '') === 'corriente' ? 'selected' : '' ?>>Corriente</option>
    </select>
  </label>
  <label>Número de cuenta<?= $ast ?>
    <input type="text" name="numero_cuenta" <?= $req ?> inputmode="numeric" data-digitos="20" maxlength="20" pattern="\d{6,20}" value="<?= $val('numero_cuenta') ?>" <?= $ro('numero_cuenta') ?>
           data-msg="El número de cuenta debe tener entre 6 y 20 dígitos, sin espacios ni guiones.">
  </label>
</div>
<?php endif; ?>
