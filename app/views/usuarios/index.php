<div class="page-header">
  <div>
    <div class="page-title">Usuarios del Sistema</div>
    <div class="page-subtitle">Cuentas de acceso a SGTH</div>
  </div>
</div>

<div class="form-row" style="align-items:start">
  <div class="card" style="grid-column: span 2">
    <table>
      <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Empleado vinculado</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= View::e($u['nombre']) ?></td>
          <td><?= View::e($u['email']) ?></td>
          <td><span class="badge <?= View::rolBadgeClass((int)$u['rol_id']) ?>"><?= View::rolLabel((int)$u['rol_id']) ?></span></td>
          <td><?= View::e($u['empleado_nombre'] ?: '—') ?></td>
          <td>
            <?php if ((int)$u['id'] !== (int)Auth::user()['id']): ?>
            <form method="POST" action="<?= APP_URL ?>/usuarios/<?= $u['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este usuario?')">
              <button type="submit" class="btn btn-sm btn-outline">Eliminar</button>
            </form>
            <?php else: ?>
              <span style="font-size:11px;color:var(--muted)">Tú</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Nuevo usuario</div></div>
    <form method="POST" action="<?= APP_URL ?>/usuarios/guardar">
      <label>Nombre completo<input type="text" name="nombre" required></label>
      <label>Correo electrónico<input type="email" name="email" required></label>
      <label>Contraseña<input type="password" name="password" required minlength="6"></label>
      <label>Rol
        <select name="rol_id" required>
          <option value="1">Administrador</option>
          <option value="2">Gestor de Talento Humano</option>
          <option value="3">Empleado</option>
        </select>
      </label>
      <label>Empleado vinculado (opcional)
        <select name="empleado_id">
          <option value="">Ninguno</option>
          <?php foreach ($empleados as $e): ?>
            <option value="<?= $e['id'] ?>"><?= View::e($e['nombres'].' '.$e['apellidos']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit" class="btn btn-primary" style="margin-top:10px;width:100%">Crear usuario</button>
    </form>
  </div>
</div>
