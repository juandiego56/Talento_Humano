<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recuperar contraseña — <?= APP_NAME ?></title>
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

  .lg-right { flex:1 1 48%; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#fff; padding: clamp(24px,4vw,48px); }
  .lg-form-wrap { width:100%; max-width:392px; }

  .lg-logo-center { display:flex; justify-content:center; margin-bottom:20px; }
  .lg-logo-center img { width:72px; height:72px; }

  .lg-title { font-weight:800; font-size:25px; color:var(--navy); text-align:center; margin-bottom:6px; letter-spacing:-.01em; }
  .lg-subtitle { font-size:13.5px; color:#64748b; text-align:center; margin-bottom:22px; line-height:1.5; }

  .lg-note {
    margin-bottom:18px; padding:11px 14px; border-radius:9px; background:#e0f2fe; color:#075985;
    font-size:12.5px; line-height:1.55; border-left:3px solid #0284c7;
    display:flex; align-items:flex-start; gap:8px;
  }
  .lg-note svg { width:15px; height:15px; flex-shrink:0; margin-top:1px; }

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

  .lg-submit {
    width:100%; margin-top:6px; background:var(--navy); color:#fff; border:none; border-radius:11px;
    padding:13px; font-family:'Lato',sans-serif; font-size:14.5px; font-weight:700; cursor:pointer;
    box-shadow:0 8px 18px rgba(10,37,64,.22); transition: background .15s ease, box-shadow .15s ease, opacity .15s ease;
  }
  .lg-submit:hover { background: var(--navy-d); }
  .lg-submit:disabled { opacity:.75; cursor:default; }

  .lg-back { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:20px; font-size:12.5px; color:var(--teal-h); text-decoration:none; font-weight:700; }
  .lg-back:hover { color:var(--teal); text-decoration:underline; }
  .lg-back svg { width:14px; height:14px; }

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
      <h1 class="lg-title">Recuperar contraseña</h1>
      <p class="lg-subtitle">Te ayudamos a recuperar el acceso a tu cuenta</p>

      <div class="lg-note">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
        <span>Escribe tu correo institucional. Un administrador revisará tu solicitud y se pondrá en contacto contigo para restablecer tu contraseña.</span>
      </div>

      <form method="POST" action="<?= APP_URL ?>/auth/recuperar/enviar" id="recForm">
        <div class="lg-field">
          <label>Correo electrónico</label>
          <div class="lg-input-wrap">
            <svg class="lg-ic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
            <input type="email" name="email" required autofocus autocomplete="username" placeholder="usuario@empresa.co">
          </div>
        </div>

        <button type="submit" class="lg-submit" id="recSubmit">
          <span id="recSubmitText">Enviar solicitud</span>
        </button>
      </form>

      <a href="<?= APP_URL ?>/auth/login" class="lg-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Volver a iniciar sesión
      </a>

      <p class="lg-footer">&copy; <?= date('Y') ?> Fundación Universitaria De Popayán</p>
    </div>
  </div>

</div>
<script>
  document.getElementById('recForm').addEventListener('submit', function (ev) {
    var correo = this.elements['email'].value.trim();
    if (!confirm('¿Enviar la solicitud de restablecimiento de contraseña para ' + correo + '?\n\nUn administrador la atenderá y se pondrá en contacto contigo.')) {
      ev.preventDefault();
      return;
    }
    var btn = document.getElementById('recSubmit');
    btn.disabled = true;
    document.getElementById('recSubmitText').textContent = 'Enviando…';
  });
</script>
</body>
</html>
