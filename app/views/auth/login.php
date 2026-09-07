<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión — <?= APP_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Inter', sans-serif;
    background: #ffffff;
    min-height: 100vh;
  }

  .lg-screen { display: flex; flex-direction: column; min-height: 100vh; }

  /* Barra superior */
  .lg-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 22px clamp(20px, 3.5vw, 48px);
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
  }
  .lg-logo { font-size: 13px; font-weight: 700; letter-spacing: .04em; color: #1e3a8a; }
  .lg-nav { display: flex; gap: 26px; font-size: 12.5px; color: #64748b; }
  .lg-nav span:hover { color: #1e3a8a; }
  .lg-topbar-note { font-size: 12px; color: #94a3b8; }

  /* Hero claro */
  .lg-hero {
    position: relative; flex: 1; overflow: hidden;
    display: flex; align-items: flex-end;
    background:
      radial-gradient(45% 45% at 18% 20%, #dbeafe 0%, transparent 65%),
      radial-gradient(40% 35% at 82% 12%, #e0e7ff 0%, transparent 65%),
      radial-gradient(55% 50% at 55% 100%, #eff6ff 0%, transparent 65%),
      #ffffff;
  }

  .lg-wordmark {
    position: absolute; top: clamp(24px, 5vh, 52px); right: clamp(20px, 4vw, 56px);
    font-family: 'Fraunces', serif; font-weight: 500; font-size: clamp(28px, 3.6vw, 42px);
    color: #1e3a8a; line-height: 1; text-align: right;
  }

  .lg-widget {
    position: absolute; top: clamp(70px, 12vh, 120px); left: clamp(20px, 4vw, 56px);
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px;
    padding: 12px 16px; display: flex; align-items: center; gap: 10px;
    box-shadow: 0 10px 26px rgba(30,58,138,.08);
  }
  .lg-widget-bars { display: flex; align-items: flex-end; gap: 3px; height: 16px; }
  .lg-widget-bars i { display: block; width: 3px; background: #3b82f6; border-radius: 2px; }
  .lg-widget-bars i:nth-child(1) { height: 40%; }
  .lg-widget-bars i:nth-child(2) { height: 100%; }
  .lg-widget-bars i:nth-child(3) { height: 65%; }
  .lg-widget-bars i:nth-child(4) { height: 80%; }

  .lg-content {
    position: relative; width: 100%;
    padding: 0 clamp(20px, 4vw, 56px) clamp(56px, 9vh, 90px);
  }
  .lg-eyebrow { font-size: 11px; letter-spacing: .16em; color: #3b82f6; margin-bottom: 16px; font-weight: 600; }
  .lg-headline {
    font-family: 'Fraunces', serif; font-weight: 400; color: #1e3a8a;
    font-size: clamp(26px, 4vw, 44px); line-height: 1.16; letter-spacing: -.01em;
    max-width: 16ch; margin-bottom: 32px;
  }

  .lg-error {
    max-width: 460px; margin-bottom: 16px; padding: 10px 14px; border-radius: 8px;
    background: #fee2e2; color: #991b1b; font-size: 13px; border-left: 3px solid #dc2626;
  }

  .lg-form { max-width: 460px; }
  .lg-form-row { display: flex; flex-wrap: wrap; gap: 22px 28px; margin-bottom: 6px; }
  .lg-field { flex: 1 1 180px; min-width: 0; }
  .lg-field label { display: block; font-size: 10.5px; letter-spacing: .06em; color: #64748b; margin-bottom: 6px; font-weight: 600; }
  .lg-field input {
    width: 100%; border: none; border-bottom: 1.5px solid #cbd5e1; background: transparent;
    padding: 4px 2px 8px; font-family: 'Inter', sans-serif; font-size: 14.5px; color: #1e293b;
    outline: none;
  }
  .lg-field input:focus { border-bottom-color: #3b82f6; }
  .lg-field input::placeholder { color: #94a3b8; }

  .lg-submit {
    display: inline-flex; align-items: center; gap: 12px; margin-top: 28px;
    background: none; border: none; cursor: pointer; padding: 0;
  }
  .lg-submit-circle {
    width: 46px; height: 46px; border-radius: 50%; flex-shrink: 0;
    background: #1e3a8a;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 8px 18px rgba(30,58,138,.3);
  }
  .lg-submit-label { font-size: 13.5px; font-weight: 600; color: #1e3a8a; }

  .lg-testusers { margin-top: 24px; font-size: 11px; color: #94a3b8; line-height: 1.7; }
  .lg-testusers strong { color: #64748b; }

  @media (max-width: 720px) {
    .lg-nav, .lg-widget { display: none; }
    .lg-wordmark { position: static; text-align: left; margin: 20px clamp(20px, 4vw, 56px) 0; }
    .lg-hero { align-items: flex-start; }
    .lg-content { padding-top: 4px; }
  }
</style>
</head>
<body>
<div class="lg-screen">

  <div class="lg-topbar">
    <div class="lg-logo">SGTH</div>
    <nav class="lg-nav">
      <span>Personal</span>
      <span>Nómina</span>
      <span>Bienestar</span>
    </nav>
    <div class="lg-topbar-note">Plataforma institucional</div>
  </div>

  <div class="lg-hero">
    <div class="lg-wordmark">Talento<br>Humano</div>

    <div class="lg-widget">
      <span>📁</span>
      <div class="lg-widget-bars"><i></i><i></i><i></i><i></i></div>
    </div>

    <div class="lg-content">
      <div class="lg-eyebrow">· ACCESO AL SISTEMA ·</div>
      <h1 class="lg-headline">Bienvenido de nuevo. Gestiona el talento humano de tu institución.</h1>

      <?php $err = Session::getFlash('error'); if ($err): ?>
        <div class="lg-error"><?= View::e($err) ?></div>
      <?php endif; ?>

      <form class="lg-form" method="POST" action="<?= APP_URL ?>/auth/procesar">
        <div class="lg-form-row">
          <div class="lg-field">
            <label>Correo electrónico</label>
            <input type="email" name="email" required autofocus autocomplete="username" placeholder="usuario@empresa.co">
          </div>
          <div class="lg-field">
            <label>Contraseña</label>
            <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
          </div>
        </div>
        <button type="submit" class="lg-submit">
          <span class="lg-submit-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14M13 6l6 6-6 6"/>
            </svg>
          </span>
          <span class="lg-submit-label">Iniciar sesión</span>
        </button>
      </form>

      <p class="lg-testusers">
        Usuarios de prueba (clave: <strong>talento2026</strong>) — admin@empresa.co · maria.gomez@empresa.co · carlos.munoz@empresa.co
      </p>
    </div>
  </div>

</div>
</body>
</html>
