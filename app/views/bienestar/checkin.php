<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro de asistencia — <?= APP_NAME ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Lato', sans-serif; margin: 0; min-height: 100vh;
      background: linear-gradient(160deg, #0a2540 0%, #071a2e 65%, #050f1e 100%);
      display: flex; align-items: center; justify-content: center; padding: 24px;
    }
    .checkin-card {
      background: #fff; border-radius: 16px; padding: 40px 30px; max-width: 420px; width: 100%;
      text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,.35);
    }
    .checkin-icon {
      width: 68px; height: 68px; border-radius: 50%; margin: 0 auto 16px;
      display: flex; align-items: center; justify-content: center;
    }
    .checkin-icon.ok  { background: #e0f2fe; color: #075985; }
    .checkin-icon.err { background: #fee2e2; color: #991b1b; }
    .checkin-icon svg { width: 34px; height: 34px; }
    .checkin-title { font-size: 21px; font-weight: 700; margin-bottom: 10px; color: #0a2540; }
    .checkin-sub { color: #667085; font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
    .checkin-sub strong { color: #0a2540; }

    .checkin-actividad { font-size: 13px; color: #667085; margin-bottom: 22px; line-height: 1.5; }
    .checkin-actividad strong { display: block; font-size: 16px; color: #0a2540; margin-bottom: 2px; }
    .checkin-form label { display: block; text-align: left; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .checkin-form input {
      width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
      font-size: 16px; font-family: 'Lato', sans-serif; margin-bottom: 6px; text-align: center;
      letter-spacing: .02em;
    }
    .checkin-form input:focus { outline: none; border-color: #1c5fa8; box-shadow: 0 0 0 3px #e8f1fa; }
    .checkin-error {
      background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 13px;
      padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; text-align: left;
    }
    .checkin-form button { width: 100%; padding: 13px; font-size: 15px; margin-top: 8px; }
  </style>
</head>
<body>
  <div class="checkin-card">
    <?php if ($paso === 'formulario'): ?>
      <div class="checkin-icon ok">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      <?php if (!$actividad): ?>
        <div class="checkin-title">Código QR no válido</div>
        <div class="checkin-sub">Este código QR no es válido o la actividad ya no existe.</div>
        <a href="<?= APP_URL ?>/" class="btn btn-primary">Ir al inicio</a>
      <?php else: ?>
        <div class="checkin-title">Registrar mi asistencia</div>
        <div class="checkin-actividad">
          <strong><?= View::e($actividad['nombre']) ?></strong>
          <?= View::fecha($actividad['fecha_inicio']) ?>
        </div>
        <?php if (!empty($error)): ?>
          <div class="checkin-error"><?= View::e($error) ?></div>
        <?php endif; ?>
        <form class="checkin-form" method="POST" action="<?= APP_URL ?>/bienestar/checkin/<?= View::e($token) ?>/registrar">
          <label for="numero_documento">Número de cédula</label>
          <input type="text" inputmode="numeric" name="numero_documento" id="numero_documento" placeholder="Ej. 1002003005" autofocus required>
          <button type="submit" class="btn btn-primary">Registrar asistencia</button>
        </form>
      <?php endif; ?>
    <?php elseif ($ok): ?>
      <div class="checkin-icon ok">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div class="checkin-title">Asistencia registrada</div>
      <div class="checkin-sub">
        <strong><?= View::e($empleadoNombre) ?></strong><br>
        <?= View::e($actividad['nombre']) ?><br>
        <?= View::fecha($actividad['fecha_inicio']) ?>
      </div>
      <a href="<?= APP_URL ?>/" class="btn btn-primary">Ir al inicio</a>
    <?php else: ?>
      <div class="checkin-icon err">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
      </div>
      <div class="checkin-title">No se pudo registrar la asistencia</div>
      <div class="checkin-sub"><?= View::e($mensaje) ?></div>
      <a href="<?= APP_URL ?>/" class="btn btn-primary">Ir al inicio</a>
    <?php endif; ?>
  </div>
</body>
</html>
