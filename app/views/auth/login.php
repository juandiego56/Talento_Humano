<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
<style>
  :root{
    --navy:#0a2540; --navy-d:#071a2e; --blue:#1c5fa8; --blue-l:#3f7fc4; --field-bg:#e8f1fa;
    --teal:#2dd4bf; --teal-h:#14b8a6;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Lato', sans-serif; background:#fff; min-height:100vh; }

  .lg-screen { display:flex; min-height:100vh; }

  /* ===== Panel izquierdo (foto institucional) ===== */
  .lg-left { position:relative; flex:1 1 52%; min-height:100vh; overflow:hidden; display:flex; align-items:center; }
  .lg-photo { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  .lg-overlay {
    position:absolute; inset:0;
    background: linear-gradient(160deg, rgba(10,37,64,.90) 0%, rgba(10,37,64,.62) 42%, rgba(28,95,168,.48) 100%);
  }
  .lg-content { position:relative; z-index:1; width:100%; padding: clamp(32px,5vw,76px); color:#fff; }

  .lg-brand { display:flex; align-items:center; gap:13px; margin-bottom: clamp(46px,10vh,100px); }
  .lg-brand img { width:44px; height:44px; filter:brightness(0) invert(1); }
  .lg-brand-name { font-size:15.5px; font-weight:700; line-height:1.3; }
  .lg-brand-sub  { font-size:11.5px; color:rgba(255,255,255,.7); font-weight:400; }

  .lg-eyebrow { font-size:11px; letter-spacing:.18em; color:var(--teal); font-weight:700; margin-bottom:10px; text-transform:uppercase; }
  .lg-script { font-family:'Lato', sans-serif; font-weight:900; font-size:clamp(36px,4.6vw,54px); line-height:1.08; color:#fff; text-shadow:0 4px 24px rgba(0,0,0,.18); }
  .lg-sub { font-weight:600; font-size:clamp(18px,2vw,23px); color:#cfe6fb; margin:4px 0 18px; }
  .lg-desc { font-size:14px; font-weight:400; color:rgba(255,255,255,.82); line-height:1.65; max-width:360px; }

  /* ===== Panel derecho (formulario) ===== */
  .lg-right { flex:1 1 48%; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#fff; padding: clamp(24px,4vw,48px); }
  .lg-form-wrap { width:100%; max-width:392px; }

  .lg-logo-center { display:flex; justify-content:center; margin-bottom:20px; }
  .lg-logo-center img { width:72px; height:72px; }

  .lg-title { font-weight:800; font-size:25px; color:var(--navy); text-align:center; margin-bottom:6px; letter-spacing:-.01em; }
  .lg-subtitle { font-size:13.5px; color:#64748b; text-align:center; margin-bottom:26px; line-height:1.5; }

  .lg-error, .lg-success {
    margin-bottom:16px; padding:10px 13px; border-radius:9px;
    font-size:12.5px; line-height:1.5; border-left:3px solid;
    display:flex; align-items:flex-start; gap:8px;
  }
  .lg-error   { background:#fee2e2; color:#991b1b; border-left-color:#dc2626; }
  .lg-success { background:#e0f2fe; color:#075985; border-left-color:#0284c7; }
  .lg-error svg, .lg-success svg { width:15px; height:15px; flex-shrink:0; margin-top:1px; }

  .lg-field { margin-bottom:16px; }
  .lg-field label { display:block; font-size:12.5px; font-weight:600; color:#334155; margin-bottom:6px; }
  .lg-input-wrap { position:relative; display:flex; align-items:center; }
  .lg-input-wrap svg.lg-ic {
    position:absolute; left:14px; width:17px; height:17px; color:#7391b5; pointer-events:none;
    transition: color .15s ease;
  }
  .lg-input-wrap:focus-within svg.lg-ic { color:var(--blue); }
  .lg-field input {
    width:100%; border:1.5px solid transparent; border-radius:11px; background:var(--field-bg);
    padding:12px 14px 12px 42px; font-family:'Lato',sans-serif; font-size:13.5px; color:#1e293b; outline:none;
    transition:border-color .15s ease, background .15s ease, box-shadow .15s ease;
  }
  .lg-field input:focus { border-color:var(--blue); background:#fff; box-shadow:0 0 0 3px rgba(28,95,168,.14); }
  .lg-field input::placeholder { color:#8ba3c2; }
  .lg-field.has-toggle input { padding-right:42px; }
  .lg-eye-toggle {
    position:absolute; right:12px; background:none; border:none; cursor:pointer; padding:4px;
    display:flex; align-items:center; color:#7391b5;
  }
  .lg-eye-toggle:hover { color:var(--blue); }
  .lg-eye-toggle svg { width:17px; height:17px; }

  .lg-submit {
    width:100%; margin-top:6px; background:var(--navy); color:#fff; border:none; border-radius:11px;
    padding:13px; font-family:'Lato',sans-serif; font-size:14.5px; font-weight:700; cursor:pointer;
    box-shadow:0 8px 18px rgba(10,37,64,.22); transition: background .15s ease, box-shadow .15s ease, opacity .15s ease;
  }
  .lg-submit:hover { background: var(--navy-d); }
  .lg-submit:disabled { opacity:.75; cursor:default; }

  .lg-help-line { text-align:center; font-size:12.5px; color:#64748b; margin-top:16px; line-height:1.6; }
  .lg-help-line a { color:var(--teal-h); font-weight:700; text-decoration:none; }
  .lg-help-line a:hover { color:var(--teal); text-decoration:underline; }

  .lg-divider { display:flex; align-items:center; gap:12px; margin:22px 0 16px; }
  .lg-divider::before, .lg-divider::after { content:''; flex:1; height:1px; background:#e6ecf3; }
  .lg-divider span { font-size:10px; color:#a3b1c2; font-weight:600; letter-spacing:.04em; text-transform:uppercase; }

  .lg-testusers { font-size:11px; color:#94a3b8; line-height:1.7; text-align:center; }
  .lg-testusers strong { color:#64748b; }

  .lg-footer { text-align:center; font-size:11.5px; color:#a3b1c2; margin-top:26px; }

  @media (max-width: 880px) {
    .lg-left { display:none; }
    .lg-right { flex:1 1 100%; }
  }
</style>
</head>
<body>
<div class="lg-screen">

  <div class="lg-left">
    <img class="lg-photo" src="<?= APP_URL ?>/assets/img/login-banner-photo.jpg" alt="">
    <div class="lg-overlay"></div>
    <div class="lg-content">
      <div class="lg-brand">
        <img src="<?= APP_URL ?>/assets/logo.png" alt="">
        <div>
          <div class="lg-brand-name">Fundación Universitaria</div>
          <div class="lg-brand-sub">De Popayán</div>
        </div>
      </div>

      <div class="lg-eyebrow">Sistema de gestión de</div>
      <div class="lg-script">Talento Humano</div>
      <div class="lg-sub">de tu institución</div>
      <p class="lg-desc">Administración de personal, nómina y bienestar institucional desde un solo lugar.</p>
    </div>
  </div>

  <div class="lg-right">
    <div class="lg-form-wrap">
      <div class="lg-logo-center">
        <img src="<?= APP_URL ?>/assets/logo.png" alt="Fundación Universitaria de Popayán">
      </div>
      <h1 class="lg-title">¡Bienvenido de nuevo!</h1>
      <p class="lg-subtitle">Ingresa tus credenciales para acceder al sistema</p>

      <?php $err = Session::getFlash('error'); if ($err): ?>
        <div class="lg-error">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
          <span><?= View::e($err) ?></span>
        </div>
      <?php endif; ?>
      <?php $ok = Session::getFlash('success'); if ($ok): ?>
        <div class="lg-success">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span><?= View::e($ok) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= APP_URL ?>/auth/procesar" id="loginForm">
        <div class="lg-field">
          <label>Correo electrónico</label>
          <div class="lg-input-wrap">
            <svg class="lg-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
            <input type="email" name="email" required autofocus autocomplete="username" placeholder="usuario@empresa.co">
          </div>
        </div>
        <div class="lg-field has-toggle">
          <label>Contraseña</label>
          <div class="lg-input-wrap">
            <svg class="lg-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            <input type="password" name="password" id="pwInput" required autocomplete="current-password" placeholder="••••••••">
            <button type="button" class="lg-eye-toggle" id="pwToggle" aria-label="Mostrar contraseña">
              <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="lg-submit" id="loginSubmit">
          <span id="loginSubmitText">Iniciar sesión</span>
        </button>
      </form>

      <p class="lg-help-line">¿Olvidaste tu contraseña? <a href="<?= APP_URL ?>/auth/recuperar">Recupérala aquí</a></p>
      <p class="lg-help-line">¿Necesitas ayuda? Contacta a tu administrador del sistema</p>

      <div class="lg-divider"><span>Uso interno</span></div>
      <p class="lg-testusers">
        Usuarios de prueba (clave: <strong>talento2026</strong>)<br>
        admin@empresa.co · maria.gomez@empresa.co · carlos.munoz@empresa.co
      </p>

      <p class="lg-footer">&copy; <?= date('Y') ?> Fundación Universitaria De Popayán</p>
    </div>
  </div>

</div>
<script>
  document.getElementById('loginForm').addEventListener('submit', function () {
    var btn = document.getElementById('loginSubmit');
    btn.disabled = true;
    document.getElementById('loginSubmitText').textContent = 'Ingresando…';
  });

  document.getElementById('pwToggle').addEventListener('click', function () {
    var input = document.getElementById('pwInput');
    var showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    var icon = document.getElementById('eyeIcon');
    icon.innerHTML = showing
      ? '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
      : '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.774 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>';
    this.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
  });
</script>
</body>
</html>
