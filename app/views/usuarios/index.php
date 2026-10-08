<div class="page-header">
  <div>
    <div class="page-subtitle">Cuentas de acceso a SGTH</div>
  </div>
</div>

<?php if (!empty($solicitudes)): ?>
<div class="card" style="border-left:4px solid var(--warning, #f59e0b); margin-bottom:18px">
  <div class="card-header"><div class="card-title">Solicitudes de restablecimiento de contraseña</div></div>
  <table>
    <thead><tr><th>Usuario</th><th>Correo</th><th>Solicitado</th><th>Nueva contraseña</th></tr></thead>
    <tbody>
      <?php foreach ($solicitudes as $s): ?>
      <tr>
        <td><?= View::e($s['nombre']) ?></td>
        <td><?= View::e($s['email']) ?></td>
        <td><?= date('d/m/Y h:i A', strtotime($s['fecha_solicitud'])) ?></td>
        <td>
          <form method="POST" action="<?= APP_URL ?>/usuarios/<?= $s['usuario_id'] ?>/restablecer-password"
                style="display:flex;flex-direction:column;gap:6px;max-width:150px"
                onsubmit="return confirm('¿Restablecer la contraseña de <?= View::e(addslashes($s['nombre'])) ?>?')">
            <input type="password" name="password" placeholder="Nueva contraseña" required minlength="6" style="width:100%">
            <button type="submit" class="btn btn-sm btn-primary" style="width:100%">Restablecer</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<div class="form-row" style="align-items:start">
  <div class="card" style="grid-column: span 2">
    <table>
      <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Empleado vinculado / Programa</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= View::e($u['nombre']) ?></td>
          <td><?= View::e($u['email']) ?></td>
          <td><span class="badge <?= View::rolBadgeClass((int)$u['rol_id']) ?>"><?= View::rolLabel((int)$u['rol_id']) ?></span></td>
          <td><?= View::e((int)$u['rol_id'] === ROL_DIRECTOR_PROGRAMA ? ($u['programa_nombre'] ?: 'Sin programa asignado') : ($u['empleado_nombre'] ?: '—')) ?></td>
          <td>
            <?php if ((int)$u['id'] !== (int)Auth::user()['id']): ?>
            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:flex-start">
              <details>
                <summary class="btn btn-sm btn-outline" style="cursor:pointer;list-style:none">Restablecer clave</summary>
                <form method="POST" action="<?= APP_URL ?>/usuarios/<?= $u['id'] ?>/restablecer-password"
                      style="display:flex;flex-direction:column;gap:6px;margin-top:6px;width:170px"
                      onsubmit="return confirm('¿Restablecer la contraseña de <?= View::e(addslashes($u['nombre'])) ?>? Tendrá que crear una nueva al ingresar.')">
                  <input type="text" name="password" placeholder="Clave temporal" required minlength="6" autocomplete="off">
                  <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                </form>
              </details>
              <form method="POST" action="<?= APP_URL ?>/usuarios/<?= $u['id'] ?>/eliminar" onsubmit="return confirm('¿Eliminar este usuario?')">
                <button type="submit" class="btn btn-sm btn-outline">Eliminar</button>
              </form>
            </div>
            <?php else: ?>
              <span style="font-size:11px;color:var(--muted)">Tú</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php View::partial('layouts._paginacion', ['pagina' => $pagina, 'paginas' => $paginas, 'total' => $total, 'porPagina' => $porPagina]); ?>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Nuevo usuario</div></div>
    <form method="POST" action="<?= APP_URL ?>/usuarios/guardar">
      <label>Nombre completo<input type="text" name="nombre" required></label>
      <label>Correo electrónico<input type="email" name="email" required></label>
      <label>Contraseña<input type="password" name="password" required minlength="6"></label>
      <label>Rol
        <select name="rol_id" id="selRolNuevo" required onchange="document.getElementById('wrapProgramaNuevo').style.display=this.value==='4'?'block':'none'">
          <option value="1">Administrador</option>
          <option value="2">Gestor de Talento Humano</option>
          <option value="3">Empleado</option>
          <option value="4">Director de Programa</option>
        </select>
      </label>
      <label id="wrapProgramaNuevo" style="display:none">Programa académico
        <select name="programa_id">
          <option value="">Seleccionar…</option>
          <?php foreach ($programas as $p): ?>
            <option value="<?= $p['id'] ?>"><?= View::e($p['nombre']) ?></option>
          <?php endforeach; ?>
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