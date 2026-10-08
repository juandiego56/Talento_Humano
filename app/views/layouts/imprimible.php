<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo ?? '') ?> — <?= APP_NAME ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css?v=<?= filemtime(ROOT.'/public/assets/css/app.css') ?>">
  <style>
    body { background: #dfe4ea; padding: 30px 0; }
    .hoja-doc {
      max-width: 800px; margin: 0 auto; background: #fff; padding: 40px 48px;
      border-radius: var(--r-lg); box-shadow: var(--sh);
    }
    .hoja-toolbar { max-width: 800px; margin: 0 auto 14px; display:flex; justify-content:space-between; align-items:center; }
    body.embed { background: #fff; padding: 0; }
    body.embed .hoja-toolbar { display: none; }
    body.embed .hoja-doc { box-shadow: none; border-radius: 0; max-width: 860px; padding: 24px 28px; }
    @media print {
      body { background: #fff; padding: 0; }
      .hoja-toolbar { display: none; }
      .hoja-doc { box-shadow: none; max-width: 100%; padding: 0; }
    }
  </style>
</head>
<body class="<?= isset($_GET['embed']) ? 'embed' : '' ?>">
  <div class="hoja-toolbar">
    <?php
      $hvVolver = (class_exists('Auth') && Auth::esEmpleado())
          ? APP_URL . '/mi-hoja-de-vida/paso/7'
          : APP_URL . '/empleados' . (isset($empleado['id']) ? '/' . (int)$empleado['id'] : '');
    ?>
    <a href="<?= $hvVolver ?>" target="_top" class="btn btn-outline btn-sm">← Volver</a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">Imprimir / Guardar PDF</button>
  </div>
  <div class="hoja-doc">
    <?= $pageContent ?>
  </div>
</body>
</html>
