/* Comportamiento compartido de los formularios de empleados y hoja de vida. */
(function () {
  'use strict';
  function $all(root, sel) { return Array.prototype.slice.call(root.querySelectorAll(sel)); }

  // Mensajes claros en vez de los genéricos del navegador.
  function mensajesPersonalizados(root) {
    $all(root, '[data-msg]').forEach(function (el) {
      el.addEventListener('invalid', function () { el.setCustomValidity(el.getAttribute('data-msg')); });
      el.addEventListener('input', function () { el.setCustomValidity(''); });
      el.addEventListener('change', function () { el.setCustomValidity(''); });
    });
  }

  // Teléfonos / números: solo dígitos.
  function soloDigitos(root) {
    $all(root, '[data-digitos]').forEach(function (el) {
      el.addEventListener('input', function () {
        var max = parseInt(el.getAttribute('data-digitos'), 10) || 20;
        el.value = el.value.replace(/\D/g, '').slice(0, max);
      });
    });
  }

  // Fondo de pensión: la opción "Otro" muestra el cajón de texto.
  function fondoOtro(root) {
    $all(root, 'select[data-otro]').forEach(function (sel) {
      var caja = document.getElementById(sel.getAttribute('data-otro'));
      if (!caja) return;
      var inp = caja.querySelector('input');
      function aplicar() {
        var otro = sel.value === 'Otro';
        caja.style.display = otro ? '' : 'none';
        if (inp) { inp.required = otro; if (!otro) inp.value = ''; }
      }
      sel.addEventListener('change', aplicar);
      aplicar();
    });
  }

  // Casilla que desactiva otros campos (p. ej. "En curso" desactiva la fecha de expedición).
  function casillasQueDesactivan(root) {
    $all(root, 'input[type=checkbox][data-desactiva]').forEach(function (chk) {
      var objetivos = $all(document, chk.getAttribute('data-desactiva'));
      function aplicar() {
        objetivos.forEach(function (o) {
          o.disabled = chk.checked;
          o.required = !chk.checked && o.hasAttribute('data-req-si-activo');
          if (chk.checked) o.value = '';
        });
      }
      chk.addEventListener('change', aplicar);
      aplicar();
    });
  }

  // Formularios por pestañas: no deja avanzar si falta un campo de la pestaña actual.
  function formulariosPorPestanas(root) {
    $all(root, 'form[data-tabs]').forEach(function (form) {
      var tabs = $all(form, '.form-tab');
      var panels = $all(form, '.tab-panel');
      var actual = 0;

      function mostrar(i) {
        actual = i;
        tabs.forEach(function (t, k) { t.classList.toggle('active', k === i); });
        panels.forEach(function (p, k) { p.classList.toggle('active', k === i); });
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
      function panelValido(i) {
        var campos = $all(panels[i], 'input, select, textarea');
        for (var k = 0; k < campos.length; k++) {
          if (!campos[k].checkValidity()) { mostrar(i); campos[k].reportValidity(); return false; }
        }
        return true;
      }
      function irA(destino) {
        if (destino <= actual) { mostrar(destino); return; }
        for (var i = actual; i < destino; i++) { if (!panelValido(i)) return; }
        mostrar(destino);
      }

      tabs.forEach(function (t, i) { t.addEventListener('click', function () { irA(i); }); });
      $all(form, '.btn-next').forEach(function (b) {
        b.addEventListener('click', function () { irA(Math.min(actual + 1, panels.length - 1)); });
      });
      $all(form, '.btn-prev').forEach(function (b) {
        b.addEventListener('click', function () { mostrar(Math.max(actual - 1, 0)); });
      });
      form.addEventListener('submit', function (e) {
        for (var i = 0; i < panels.length; i++) {
          if (!panelValido(i)) { e.preventDefault(); return; }
        }
      });
    });
  }

  // Listas con búsqueda y filtros: se actualizan solas al escribir o cambiar un filtro.
  function filtrosAutomaticos(root) {
    $all(root, 'form[data-autofiltro]').forEach(function (form) {
      var t = null;
      $all(form, 'input[type=text], input[type=search]').forEach(function (inp) {
        inp.addEventListener('input', function () {
          clearTimeout(t);
          t = setTimeout(function () { form.submit(); }, 450);
        });
      });
      $all(form, 'select').forEach(function (s) {
        s.addEventListener('change', function () { form.submit(); });
      });
      // Deja el cursor al final del cuadro de búsqueda tras recargar.
      var q = form.querySelector('input[name=q]');
      if (q && q.value && document.activeElement === document.body) {
        q.focus(); var v = q.value; q.value = ''; q.value = v;
      }
    });
  }

  // Al volver con el botón "atrás" el navegador puede mostrar una copia guardada: se recarga.
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) window.location.reload();
  });

  document.addEventListener('DOMContentLoaded', function () {
    mensajesPersonalizados(document);
    soloDigitos(document);
    fondoOtro(document);
    casillasQueDesactivan(document);
    formulariosPorPestanas(document);
    filtrosAutomaticos(document);
  });
})();
