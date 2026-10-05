<div class="page-header">
  <div>
    <div class="page-subtitle">Catálogos organizacionales usados en Administración de Personal</div>
  </div>
</div>

<div class="form-row" style="align-items:start">

  <div class="card">
    <div class="card-header"><div class="card-title">Áreas</div></div>
    <table>
      <thead><tr><th>Área</th><th>Empleados</th><th></th></tr></thead>
      <tbody>
        <?php if (!$areas): ?><tr><td colspan="3" class="text-muted">Sin áreas registradas.</td></tr><?php endif; ?>
        <?php foreach ($areas as $a): ?>
        <tr>
          <td><?= View::e($a['nombre']) ?><?php if($a['descripcion']): ?><br><span style="font-size:11px;color:var(--muted)"><?= View::e($a['descripcion']) ?></span><?php endif; ?></td>
          <td><?= (int)$a['num_empleados'] ?></td>
          <td>
            <?php if ((int)$a['num_empleados'] === 0): ?>
            <form method="POST" action="<?= APP_URL ?>/catalogos/area/<?= $a['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar esta área?')">
              <button type="submit" class="btn btn-sm btn-outline">Eliminar</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <details style="margin-top:14px">
      <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Nueva área</summary>
      <form method="POST" action="<?= APP_URL ?>/catalogos/area/guardar" style="margin-top:10px">
        <label>Nombre<input type="text" name="nombre" required></label>
        <label>Descripción<input type="text" name="descripcion"></label>
        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
      </form>
    </details>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Cargos</div></div>
    <table>
      <thead><tr><th>Cargo</th><th>Área</th><th>Salario sugerido</th><th>Empleados</th><th></th></tr></thead>
      <tbody>
        <?php if (!$cargos): ?><tr><td colspan="5" class="text-muted">Sin cargos registrados.</td></tr><?php endif; ?>
        <?php foreach ($cargos as $c): ?>
        <tr>
          <td><?= View::e($c['nombre']) ?></td>
          <td><?= View::e($c['area'] ?? '—') ?></td>
          <td><?= $c['salario_base_sugerido'] ? View::money($c['salario_base_sugerido']) : '—' ?></td>
          <td><?= (int)$c['num_empleados'] ?></td>
          <td>
            <?php if ((int)$c['num_empleados'] === 0): ?>
            <form method="POST" action="<?= APP_URL ?>/catalogos/cargo/<?= $c['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este cargo?')">
              <button type="submit" class="btn btn-sm btn-outline">Eliminar</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <details style="margin-top:14px">
      <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Nuevo cargo</summary>
      <form method="POST" action="<?= APP_URL ?>/catalogos/cargo/guardar" style="margin-top:10px">
        <div class="form-row">
          <label>Nombre<input type="text" name="nombre" required></label>
          <label>Área
            <select name="area_id">
              <option value="">Sin asignar</option>
              <?php foreach ($areas as $a): ?>
                <option value="<?= $a['id'] ?>"><?= View::e($a['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <label>Salario base sugerido<input type="number" name="salario_base_sugerido" step="1000" min="0"></label>
        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
      </form>
    </details>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Programas académicos</div></div>
    <p class="text-muted" style="margin-bottom:10px">Usados para vincular docentes y usuarios con rol Director de Programa.</p>
    <table>
      <thead><tr><th>Programa</th><th>Código</th><th>Facultad</th><th>Empleados</th><th></th></tr></thead>
      <tbody>
        <?php if (!$programas): ?><tr><td colspan="5" class="text-muted">Sin programas registrados.</td></tr><?php endif; ?>
        <?php foreach ($programas as $p): ?>
        <tr>
          <td><?= View::e($p['nombre']) ?></td>
          <td><?= View::e($p['codigo'] ?: '—') ?></td>
          <td><?= View::e($p['facultad'] ?: '—') ?></td>
          <td><?= (int)$p['num_empleados'] ?></td>
          <td>
            <?php if ((int)$p['num_empleados'] === 0): ?>
            <form method="POST" action="<?= APP_URL ?>/catalogos/programa/<?= $p['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este programa académico?')">
              <button type="submit" class="btn btn-sm btn-outline">Eliminar</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <details style="margin-top:14px">
      <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Nuevo programa académico</summary>
      <form method="POST" action="<?= APP_URL ?>/catalogos/programa/guardar" style="margin-top:10px">
        <label>Nombre del programa<input type="text" name="nombre" required placeholder="Ej. Ingeniería de Sistemas"></label>
        <div class="form-row">
          <label>Código<input type="text" name="codigo" placeholder="Ej. ISIS"></label>
          <label>Facultad<input type="text" name="facultad"></label>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
      </form>
    </details>
  </div>

</div>

<div class="card" style="margin-top:16px">
  <div class="card-header"><div class="card-title">Escalafón salarial (cargo × nivel de formación × dedicación)</div></div>
  <p class="text-muted" style="margin-bottom:10px">Define el salario según el cargo, el nivel de formación del empleado y si trabaja tiempo completo o medio tiempo. Se usa para sugerir el salario básico al registrar o editar un empleado.</p>
  <table>
    <thead><tr><th>Cargo</th><th>Nivel de formación</th><th>Tiempo completo</th><th>Medio tiempo</th><th></th></tr></thead>
    <tbody>
      <?php if (!$escalafon): ?><tr><td colspan="5" class="text-muted">Sin escalafón registrado.</td></tr><?php endif; ?>
      <?php foreach ($escalafon as $es): ?>
      <tr>
        <td><?= View::e($es['cargo']) ?></td>
        <td><?= View::nivelEducativoLabel($es['nivel_educativo']) ?></td>
        <td><?= View::money($es['salario_tiempo_completo']) ?></td>
        <td><?= $es['salario_medio_tiempo'] !== null ? View::money($es['salario_medio_tiempo']) : View::money($es['salario_tiempo_completo'] / 2) . ' (auto)' ?></td>
        <td>
          <form method="POST" action="<?= APP_URL ?>/catalogos/escalafon/<?= $es['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este registro del escalafón?')">
            <button type="submit" class="btn btn-sm btn-outline">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <details style="margin-top:14px">
    <summary style="cursor:pointer;color:var(--primary);font-weight:600;font-size:13px">+ Nuevo / actualizar escalafón</summary>
    <form method="POST" action="<?= APP_URL ?>/catalogos/escalafon/guardar" style="margin-top:10px">
      <div class="form-row">
        <label>Cargo
          <select name="cargo_id" required>
            <option value="">Seleccionar…</option>
            <?php foreach ($cargos as $c): ?>
              <option value="<?= $c['id'] ?>"><?= View::e($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Nivel de formación
          <select name="nivel_educativo" required>
            <?php foreach (['bachillerato','tecnico','tecnologo','pregrado','especializacion','maestria','doctorado'] as $n): ?>
              <option value="<?= $n ?>"><?= View::nivelEducativoLabel($n) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </div>
      <div class="form-row">
        <label>Salario tiempo completo<input type="number" name="salario_tiempo_completo" step="1000" min="0" required></label>
        <label>Salario medio tiempo <span class="text-muted" style="font-weight:400">(opcional — si se deja vacío, se usa la mitad)</span>
          <input type="number" name="salario_medio_tiempo" step="1000" min="0">
        </label>
      </div>
      <p class="text-muted" style="margin:4px 0 10px">Si el cargo y nivel ya existen en el escalafón, se actualiza el valor.</p>
      <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
    </form>
  </details>
</div>

<div class="card" style="margin-top:16px">
  <div class="card-header"><div class="card-title">ARL — tasas por nivel de riesgo</div></div>
  <p class="text-muted" style="margin-bottom:10px">Tasas fijas según la clasificación de riesgo (Decreto 1607/2002). Se usan en la Calculadora de Costos Laborales y al registrar/editar empleados.</p>
  <table>
    <thead><tr><th>Nivel</th><th>Descripción</th><th>Tasa actual</th><th>Nueva tasa (%)</th></tr></thead>
    <tbody>
      <?php $numerosRomanos = ['', 'I', 'II', 'III', 'IV', 'V']; ?>
      <?php foreach ($nivelesArl as $n): ?>
      <tr>
        <td>Riesgo <?= View::e($numerosRomanos[(int)$n['nivel']] ?? (string)$n['nivel']) ?></td>
        <td><?= View::e($n['descripcion']) ?></td>
        <td><?= number_format((float)$n['tasa'] * 100, 3) ?>%</td>
        <td>
          <form method="POST" action="<?= APP_URL ?>/catalogos/arl/<?= (int)$n['nivel'] ?>/actualizar" style="display:flex;gap:8px;align-items:center">
            <input type="number" name="tasa_pct" step="0.001" min="0.001" max="100" value="<?= number_format((float)$n['tasa'] * 100, 3) ?>" style="width:100px">
            <button type="submit" class="btn btn-sm btn-outline">Actualizar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>