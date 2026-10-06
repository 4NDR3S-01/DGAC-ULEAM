/**
 * Interfaz: navegación fija con sombra al hacer scroll y aparición escalonada de bloques.
 * Mejora progresiva: sin este archivo (o con "reducir movimiento") todo se ve igual, sin animar.
 */
(function () {
  'use strict';

  var raiz = document.documentElement;

  // Sombra en la navegación fija cuando la página ya se desplazó.
  var pendiente = false;
  function marcarScroll() {
    pendiente = false;
    raiz.classList.toggle('is-scrolled', window.scrollY > 8);
  }
  window.addEventListener('scroll', function () {
    if (!pendiente) {
      pendiente = true;
      window.requestAnimationFrame(marcarScroll);
    }
  }, { passive: true });
  marcarScroll();

  // El texto del período de evaluación cambia con un fundido corto.
  document.querySelectorAll('.tabs .tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      var texto = document.getElementById('periodText');
      if (texto) {
        texto.classList.remove('is-cambiando');
        void texto.offsetWidth; // reinicia la animación
        texto.classList.add('is-cambiando');
      }
    });
  });

  var reducir = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reducir || !('IntersectionObserver' in window)) {
    return;
  }

  // Bloques que aparecen al entrar en pantalla (tarjetas, paneles y títulos de sección).
  var SELECTORES = [
    '.section > h2', '.quick-intro', '.quick-grid > .card', '.two-col > .panel',
    '.dgac-section-header', '.dgac-mv-header', '.dgac-timeline-header',
    '.news-card', '.dgac-result', '.dgac-repo-tabs', '.dgac-repo-search',
    '.dgac-mv-card', '.dgac-pilar-card', '.dgac-area-card', '.dgac-audit-card',
    '.dgac-catalog-card', '.dgac-macro-card', '.dgac-contact-box', '.dgac-overview-card',
    '.dgac-obligacion-card', '.dgac-intro-box', '.dgac-note-box', '.dgac-main-email-card',
    '.dgac-normativa-box', '.dgac-timeline-item', '.dgac-file-card', '.dgac-proc-row',
    '.footer-grid > *'
  ].join(',');

  var candidatos = Array.prototype.slice.call(document.querySelectorAll(SELECTORES));
  var elementos = candidatos.filter(function (el) {
    // Si un contenedor ya aparece animado, su interior no se anima otra vez.
    var padre = el.parentElement && el.parentElement.closest(SELECTORES);
    return !padre && !el.closest('.asistente, .nav, .header');
  });
  if (!elementos.length) {
    return;
  }

  // Escalonado entre hermanos: cada uno entra 70 ms después del anterior (máximo 6 pasos).
  var orden = new Map();
  elementos.forEach(function (el) {
    var n = orden.get(el.parentElement) || 0;
    el.style.setProperty('--ui-retraso', Math.min(n, 6) * 0.07 + 's');
    orden.set(el.parentElement, n + 1);
    el.classList.add('ui-oculto');
  });

  // Dirección (como en los sitios de carreras ULEAM): si dos bloques comparten fila, el de la
  // izquierda entra desde la izquierda y el de la derecha desde la derecha; el resto, desde abajo.
  orden.forEach(function (total, padre) {
    var hijos = elementos.filter(function (el) { return el.parentElement === padre; });
    if (hijos.length === 2 && Math.abs(hijos[0].getBoundingClientRect().top - hijos[1].getBoundingClientRect().top) < 4) {
      hijos[0].classList.add('ui-desde-izq');
      hijos[1].classList.add('ui-desde-der');
    }
  });
  raiz.classList.add('ui-revelar');

  function terminar(el) {
    el.classList.remove('ui-oculto', 'ui-visible', 'ui-desde-izq', 'ui-desde-der');
    el.style.removeProperty('--ui-retraso');
  }

  var observador = new IntersectionObserver(function (entradas) {
    entradas.forEach(function (entrada) {
      if (!entrada.isIntersecting) {
        return;
      }
      var el = entrada.target;
      observador.unobserve(el);
      el.classList.add('ui-visible');
      // Al terminar se quitan las clases para que el hover de las tarjetas funcione normal.
      var hecho = false;
      function fin() {
        if (!hecho) {
          hecho = true;
          terminar(el);
        }
      }
      el.addEventListener('transitionend', function (e) {
        if (e.target === el && e.propertyName === 'transform') {
          fin();
        }
      });
      setTimeout(fin, 1400);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

  elementos.forEach(function (el) {
    observador.observe(el);
  });

  // Al imprimir, todo visible.
  window.addEventListener('beforeprint', function () {
    elementos.forEach(terminar);
  });
})();
