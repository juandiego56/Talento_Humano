"use strict";

/* ── Sidebar responsivo (toggle en móvil) ─────────── */
(function () {
  'use strict';

  const toggle  = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebar-overlay');

  if (!toggle || !sidebar || !overlay) return;

  var _open = false;

  function abrirSidebar() {
    _open = true;
    sidebar.classList.add('sidebar-open');
    overlay.classList.add('active');
    document.body.classList.add('sidebar-is-open');
    document.body.style.overflow = 'hidden';
    toggle.setAttribute('aria-expanded', 'true');
  }

  function cerrarSidebar() {
    _open = false;
    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('active');
    document.body.classList.remove('sidebar-is-open');
    document.body.style.overflow = '';
    toggle.setAttribute('aria-expanded', 'false');
  }

  toggle.addEventListener('click', function () {
    _open ? cerrarSidebar() : abrirSidebar();
  });

  overlay.addEventListener('click', cerrarSidebar);

  // Cerrar con Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && _open) cerrarSidebar();
  });

  // Cerrar al navegar en móvil
  sidebar.querySelectorAll('nav a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 768) cerrarSidebar();
    });
  });

  // Cerrar si se redimensiona a desktop
  var _resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(_resizeTimer);
    _resizeTimer = setTimeout(function () {
      if (window.innerWidth > 768 && _open) cerrarSidebar();
    }, 100);
  });

  // Estado inicial de aria
  toggle.setAttribute('aria-expanded', 'false');
  toggle.setAttribute('aria-controls', 'sidebar');
  if (sidebar.id !== 'sidebar') sidebar.id = 'main-sidebar';
})();

// Confirmación antes de enviar formularios destructivos
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', e => {
    if (!confirm(el.dataset.confirm)) e.preventDefault();
  });
});

// Totales en tiempo real — Condición 4
document.querySelectorAll('table input[type=number]').forEach(inp => {
  inp.addEventListener('input', () => {
    const row = inp.closest('tr');
    if (!row) return;
    const nums = name => {
      const el = row.querySelector(`input[name^="${name}"]`);
      return el ? parseInt(el.value) || 0 : 0;
    };
    // Presencial: AD = teor + teo-prac + prac
    const ad = nums('horas_teoricas') + nums('horas_teo_prac') + nums('horas_practicas');
    const total = ad + nums('horas_independ');
    const cells = row.querySelectorAll('td');
    if (cells[9])  cells[9].textContent  = total; // total presencial
    // Virtual: TI = TID + TIA
    const ti    = nums('horas_tid') + nums('horas_tia');
    const totalV = nums('horas_ad') + ti;
    if (cells[13]) cells[13].textContent = ti;      // TI
    if (cells[14]) cells[14].textContent = totalV;  // Total virtual
  });
});

// Auto-guardar formularios largos cada 90 s
document.querySelectorAll('[data-autosave]').forEach(form => {
  setInterval(() => {
    const fd = new FormData(form);
    fetch(form.action, { method:'POST', body:fd, headers:{'X-AutoSave':'1'} })
      .then(r => r.json())
      .then(d => {
        if (d.ok) {
          const msg = document.getElementById('autosave-msg');
          if (msg) { msg.textContent = '✓ Guardado automáticamente'; setTimeout(() => msg.textContent = '', 3000); }
        }
      }).catch(() => {});
  }, 90000);
});
