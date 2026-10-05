<div class="page-header">
  <div>
    <div class="page-subtitle">Documentos base usados en la lista de chequeo de cada empleado</div>
  </div>
</div>

<div class="form-row" style="align-items:start">
  <div class="card" style="grid-column: span 2">
    <table>
      <thead><tr><th>Documento</th><th>Descripción</th><th>Obligatorio</th><th></th></tr></thead>
      <tbody>
        <?php if (!$documentos): ?><tr><td colspan="4" class="text-muted">Sin documentos registrados.</td></tr><?php endif; ?>
        <?php foreach ($documentos as $d): ?>
        <tr>
          <td style="<?= $d['activo'] ? '' : 'color:var(--muted)' ?>"><?= View::e($d['nombre']) ?></td>
          <td style="<?= $d['activo'] ? '' : 'color:var(--muted)' ?>"><?= View::e($d['descripcion'] ?: '—') ?></td>
          <td style="<?= $d['activo'] ? '' : 'opacity:.6' ?>"><?= $d['obligatorio'] ? '<span class="badge badge-en-revision">Sí</span>' : '<span class="badge badge-borrador">No</span>' ?></td>
          <td>
            <?php if ($d['activo']): ?>
            <form method="POST" action="<?= APP_URL ?>/documentos/<?= $d['id'] ?>/eliminar" onsubmit="return confirm('¿Desactivar este documento del catálogo?')">
              <button type="submit" class="btn btn-sm btn-outline">Desactivar</button>
            </form>
            <?php else: ?>
            <div style="display:flex;align-items:center;gap:8px">
              <span class="badge badge-borrador">Inactivo</span>
              <form method="POST" action="<?= APP_URL ?>/documentos/<?= $d['id'] ?>/reactivar" onsubmit="return confirm('¿Reactivar este documento en el catálogo?')">
                <button type="submit" class="btn btn-sm btn-primary" style="font-weight:700">Reactivar</button>
              </form>
            </div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title">Nuevo documento</div></div>
    <form method="POST" action="<?= APP_URL ?>/documentos/guardar">
      <label>Nombre<input type="text" name="nombre" required></label>
      <label>Descripción<input type="text" name="descripcion"></label>
      <label class="check-group" style="display:flex;align-items:center;gap:6px">
        <input type="checkbox" name="obligatorio" checked> Obligatorio
      </label>
      <button type="submit" class="btn btn-primary" style="margin-top:10px;width:100%">Agregar al catálogo</button>
    </form>
  </div>
</div>