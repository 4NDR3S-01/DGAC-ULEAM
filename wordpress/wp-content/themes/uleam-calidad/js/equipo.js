/* Carrusel continuo de personal: se detiene al interactuar o al pulsar pausa. */
(function () {
  'use strict';
  document.querySelectorAll('.equipo-recursos').forEach(function (equipo) {
    var pista = equipo.querySelector('.carrusel-pista');
    var prev = equipo.querySelector('.carrusel-prev');
    var next = equipo.querySelector('.carrusel-next');
    var pausa = equipo.querySelector('.carrusel-pausa');
    if (!pista || !pista.children.length) return;

    var originales = Array.prototype.slice.call(pista.children);
    originales.forEach(function (elemento) {
      var clon = elemento.cloneNode(true);
      clon.setAttribute('aria-hidden', 'true');
      clon.querySelectorAll('[tabindex]').forEach(function (foco) { foco.removeAttribute('tabindex'); });
      pista.appendChild(clon);
    });
    var reducido = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var manual = reducido;
    var encima = reducido;
    var anteriorTiempo = 0;
    var acumulado = 0;
    function vuelta() { return pista.children[originales.length].offsetLeft - pista.children[0].offsetLeft; }
    function normalizar() { var distancia = vuelta(); if (distancia > 0 && pista.scrollLeft >= distancia) pista.scrollLeft -= distancia; }
    function paso(direccion) {
      var ancho = originales[0].getBoundingClientRect().width + parseFloat(getComputedStyle(pista).columnGap || 16);
      if (direccion < 0 && pista.scrollLeft < ancho) pista.scrollLeft += vuelta();
      pista.scrollBy({ left: direccion * ancho, behavior: reducido ? 'auto' : 'smooth' });
    }
    function pintarPausa() {
      pausa.setAttribute('aria-label', manual ? 'Reanudar carrusel' : 'Pausar carrusel');
      pausa.setAttribute('aria-pressed', manual ? 'true' : 'false');
      pausa.querySelector('i').className = 'fa-solid ' + (manual ? 'fa-play' : 'fa-pause');
    }
    prev.addEventListener('click', function () { paso(-1); });
    next.addEventListener('click', function () { paso(1); });
    pausa.addEventListener('click', function () { manual = !manual; pintarPausa(); });
    ['mouseenter', 'focusin'].forEach(function (evento) { equipo.addEventListener(evento, function () { encima = true; }); });
    ['mouseleave', 'focusout'].forEach(function (evento) { equipo.addEventListener(evento, function (e) { if (evento === 'mouseleave' || !equipo.contains(e.relatedTarget)) encima = false; }); });
    equipo.addEventListener('touchstart', function () { encima = true; }, { passive: true });
    equipo.addEventListener('touchend', function () { setTimeout(function () { encima = false; }, 2500); }, { passive: true });
    pista.addEventListener('scroll', normalizar, { passive: true });
    if (reducido) pausa.hidden = true;
    pintarPausa();
    requestAnimationFrame(function animar(tiempo) {
      if (!encima && !manual && anteriorTiempo) {
        acumulado += 45 * (tiempo - anteriorTiempo) / 1000;
        var pixeles = Math.floor(acumulado);
        if (pixeles) { acumulado -= pixeles; pista.scrollLeft += pixeles; normalizar(); }
      }
      anteriorTiempo = tiempo;
      requestAnimationFrame(animar);
    });
  });
})();
