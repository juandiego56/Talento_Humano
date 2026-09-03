<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo ?? '') ?> — <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css?v=<?= filemtime(ROOT.'/public/assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/responsive.css?v=<?= filemtime(ROOT.'/public/assets/css/responsive.css') ?>">
</head>
<body>
<div class="app-shell">

  <div id="sidebar-overlay" class="sidebar-overlay"></div>

  <?php require ROOT . '/app/views/layouts/navbar.php'; ?>

  <div class="app-content">

    <div class="app-topbar">
      <div class="topbar-left">
        <button id="sidebar-toggle" class="sidebar-toggle-btn" aria-label="Abrir menú" aria-expanded="false" type="button">
          <svg class="icon-menu" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
          </svg>
          <svg class="icon-close" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <div class="topbar-title">
          <h2><?= htmlspecialchars($titulo ?? 'Inicio') ?></h2>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-user">
          <div class="topbar-avatar">
            <?= mb_strtoupper(mb_substr(Auth::user()['nombre'] ?? 'U', 0, 1)) ?>
          </div>
          <div class="topbar-user-info">
            <div class="topbar-user-name"><?= htmlspecialchars(Auth::user()['nombre'] ?? '') ?></div>
            <div class="topbar-user-role"><?= htmlspecialchars(Auth::user()['rol_nombre'] ?? '') ?></div>
          </div>
        </div>
        <a href="<?= APP_URL ?>/auth/logout" class="btn btn-outline btn-sm" title="Cerrar sesión">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
          </svg>
          Salir
        </a>
      </div>
    </div>

    <main class="container">

      <?php $f = Session::getFlash('success'); if ($f): ?>
      <div class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span><?= htmlspecialchars($f) ?></span>
      </div>
      <?php endif; ?>

      <?php $f = Session::getFlash('error'); if ($f): ?>
      <div class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        <span><?= htmlspecialchars($f) ?></span>
      </div>
      <?php endif; ?>

      <?php $f = Session::getFlash('warning'); if ($f): ?>
      <div class="alert alert-warning">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <span><?= htmlspecialchars($f) ?></span>
      </div>
      <?php endif; ?>

      <?= $pageContent ?>

    </main>
  </div>

</div>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
