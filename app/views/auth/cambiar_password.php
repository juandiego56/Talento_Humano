<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($titulo ?? 'Crea tu contraseña') ?> — <?= APP_NAME ?></title>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
<style>
  :root { --navy:#0a2540; --blue:#1c5fa8; --field-bg:#e8f1fa; --teal:#2dd4bf; }
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Lato',sans-serif; background:#eef2f7; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; color:#1e293b; }
  .cp-card { background:#fff; width:100%; max-width:440px; border-radius:16px; padding:34px 32px; box-shadow:0 10px 40px rgba(10,37,64,.12); }
  .cp-card h1 { font-size:22px; font-weight:900; color:var(--navy); margin-bottom:6px; }
  .cp-card p.sub { font-size:14px; color:#64748b; margin-bottom:20px; line-height:1.5; }
  .cp-hola { font-size:13px; font-weight:700; color:#0f9f8f; text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
  label { display:block; font-size:13px; font-weight:700; color:#334155; margin:14px 0 6px; }
  input[type=password], input[type=text] { width:100%; padding:12px 14px; border:1.5px solid #cbd5e1; border-radius:10px; font-size:15px; font-family:inherit; background:var(--field-bg); }
  input:focus { outline:none; border-color:var(--blue); background:#fff; }
  .cp-reglas { font-size:12.5px; color:#64748b; margin-top:10px; line-height:1.6; }
  .cp-btn { width:100%; margin-top:22px; padding:13px; border:0; border-radius:10px; background:var(--navy); color:#fff; font-size:15px; font-weight:700; cursor:pointer; font-family:inherit; }
  .cp-btn:hover { background:#123a63; }
  .cp-salir { display:block; text-align:center; margin-top:14px; font-size:13px; color:#64748b; text-decoration:none; }
  .cp-salir:hover { color:var(--navy); }
  .cp-error { background:#fee2e2; color:#991b1b; border-left:4px solid #dc2626; padding:11px 14px; border-radius:8px; font-size:14px; margin-bottom:6px; }
  .cp-ver { font-size:12.5px; color:#475569; margin-top:10px; display:flex; align-items:center; gap:6px; font-weight:400; }
  .cp-ver input { width:auto; }
</style>
</head>
<body>
<div class="cp-card">
  <div class="cp-hola">Primer ingreso</div>
  <h1>Crea tu contraseña</h1>
  <p class="sub">Hola, <?= htmlspecialchars(Auth::user()['nombre'] ?? '') ?>. Por seguridad debes reemplazar la contraseña inicial por una propia antes de continuar.</p>

  <?php $err = Session::getFlash('error'); if ($err): ?>
    <div class="cp-error"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/cambiar-password/guardar" autocomplete="off">
    <label for="password">Nueva contraseña</label>
    <input type="password" id="password" name="password" required minlength="8" autofocus>
    <label for="password_confirm">Repite la contraseña</label>
    <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
    <label class="cp-ver"><input type="checkbox" id="verPass"> Mostrar contraseñas</label>
    <div class="cp-reglas">Mínimo 8 caracteres, con letras y números. No uses tu número de documento.</div>
    <button type="submit" class="cp-btn">Guardar y continuar</button>
  </form>
  <a href="<?= APP_URL ?>/auth/logout" class="cp-salir">Cancelar y cerrar sesión</a>
</div>
<script>
  document.getElementById('verPass').addEventListener('change', function () {
    ['password', 'password_confirm'].forEach(function (id) {
      document.getElementById(id).type = document.getElementById('verPass').checked ? 'text' : 'password';
    });
  });
</script>
</body>
</html>
