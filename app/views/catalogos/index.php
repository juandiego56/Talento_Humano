<div class="page-header">
  <div>
    <div class="page-title">Áreas y Cargos</div>
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

</div>
