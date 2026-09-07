<div class="page-header">
  <div>
    <div class="page-title">Mi perfil</div>
    <div class="page-subtitle">Tu foto y datos de cuenta</div>
  </div>
</div>

<div class="card">
  <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap">
    <div class="avatar-wrap">
      <div class="avatar-circle" style="width:84px;height:84px;font-size:30px">
        <?php if (!empty($usuario['foto_path'])): ?>
          <img src="<?= FOTO_UPLOAD_URL ?>/<?= View::e($usuario['foto_path']) ?>" alt="Tu foto de perfil">
        <?php else: ?>
          <?= mb_strtoupper(mb_substr($usuario['nombre'],0,1)) ?>
        <?php endif; ?>
      </div>
      <form id="form-foto-perfil" method="POST" action="<?= APP_URL ?>/mi-perfil/foto" enctype="multipart/form-data" style="display:none">
        <input type="file" id="input-foto-perfil" name="foto" accept=".jpg,.jpeg,.png,.webp" onchange="this.form.submit()">
      </form>
      <button type="button" class="avatar-edit-btn" title="Cambiar foto"
              onclick="document.getElementById('input-foto-perfil').click()">✎</button>
    </div>
    <div>
      <div style="font-size:17px;font-weight:700;color:var(--txt)"><?= View::e($usuario['nombre']) ?></div>
      <div class="text-muted"><?= View::e($usuario['email']) ?></div>
      <div class="text-muted"><?= View::e(View::rolLabel((int)$usuario['rol_id'])) ?></div>
      <?php if (!empty($usuario['foto_path'])): ?>
        <form method="POST" action="<?= APP_URL ?>/mi-perfil/foto/eliminar" style="margin-top:8px" onsubmit="return confirm('¿Quitar tu foto de perfil?')">
          <button type="submit" style="border:none;background:none;color:var(--muted);font-size:12px;cursor:pointer;padding:0;text-decoration:underline">Quitar foto</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <p class="text-muted" style="margin-top:16px">
    Haz clic en el ✎ sobre tu foto para subir una imagen nueva (JPG, PNG o WEBP, máx. 2 MB). Se verá en la barra superior del sistema y en tu ficha de empleado, si tienes una.
  </p>
</div>
