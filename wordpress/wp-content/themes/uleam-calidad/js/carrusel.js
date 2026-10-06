/**
 * Carrusel de la portada (ver inc/carrusel.php).
 * - Avanza solo si está activado en el Personalizador y la persona no pidió "reducir movimiento".
 * - Se detiene mientras el cursor o el foco del teclado están dentro, y con la pestaña oculta.
 * - El botón de pausa tiene prioridad: si la persona pausa, no vuelve a avanzar solo.
 * - Flechas del teclado, deslizar con el dedo, botones anterior/siguiente y puntos.
 */
(function () {
  'use strict';

  // Título: cada palabra en su propia "máscara" para que suba al aparecer (ver css/carrusel.css).
  // El lector de pantalla lee el título completo (aria-label); las piezas se le ocultan.
  function separarPalabras(titulo) {
    var n = 0;
    function procesar(nodo) {
      Array.prototype.slice.call(nodo.childNodes).forEach(function (hijo) {
        if (hijo.nodeType === 3) {
          var frag = document.createDocumentFragment();
          hijo.textContent.split(/(\s+)/).forEach(function (parte) {
            if (!parte) {
              return;
            }
            if (/^\s+$/.test(parte)) {
              frag.appendChild(document.createTextNode(' '));
              return;
            }
            var caja = document.createElement('span');
            caja.className = 'carrusel__palabra';
            caja.setAttribute('aria-hidden', 'true');
            var interior = document.createElement('span');
            interior.textContent = parte;
            interior.style.setProperty('--i', n++);
            caja.appendChild(interior);
            frag.appendChild(caja);
          });
          nodo.replaceChild(frag, hijo);
        } else if (hijo.nodeType === 1) {
          procesar(hijo); // conserva la palabra destacada (<span>) y su color
        }
      });
    }
    titulo.setAttribute('aria-label', titulo.textContent.replace(/\s+/g, ' ').trim());
    procesar(titulo);
    titulo.classList.add('is-separado');
  }
  document.querySelectorAll('.carrusel__titulo').forEach(separarPalabras);

  document.querySelectorAll('.carrusel--multiple').forEach(function (raiz) {
    var slides = Array.prototype.slice.call(raiz.querySelectorAll('.carrusel__slide'));
    var puntos = Array.prototype.slice.call(raiz.querySelectorAll('.carrusel__punto'));
    var pista = raiz.querySelector('.carrusel__pista');
    var pausa = raiz.querySelector('.carrusel__pausa');
    var reducir = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var intervalo = (parseInt(raiz.getAttribute('data-intervalo'), 10) || 5) * 1000;
    var actual = 0;

    // Estado de la reproducción automática.
    var auto = raiz.getAttribute('data-auto') === '1';
    var pausadoPorPersona = reducir; // con "reducir movimiento" empieza en pausa
    var bloqueos = { cursor: false, foco: false, oculto: false };
    var temporizador = null;
    var inicio = 0;
    var restante = intervalo;

    function reproduciendo() {
      return auto && !pausadoPorPersona && !bloqueos.cursor && !bloqueos.foco && !bloqueos.oculto;
    }

    function reiniciarProgreso() {
      var barra = puntos[actual] && puntos[actual].querySelector('.carrusel__progreso');
      if (barra) {
        barra.style.animation = 'none';
        void barra.offsetWidth;
        barra.style.animation = '';
      }
    }

    function programar() {
      clearTimeout(temporizador);
      raiz.classList.toggle('is-reproduciendo', auto && !pausadoPorPersona);
      raiz.classList.toggle('is-detenido', !reproduciendo());
      if (reproduciendo()) {
        inicio = Date.now();
        temporizador = setTimeout(function () {
          ir(actual + 1, false);
        }, restante);
      }
    }

    function detenerTemporal() {
      if (temporizador && reproduciendo()) {
        restante = Math.max(400, restante - (Date.now() - inicio));
      }
      clearTimeout(temporizador);
      temporizador = null;
    }

    function ir(n, porPersona) {
      var nuevo = (n + slides.length) % slides.length;
      if (nuevo !== actual) {
        slides[actual].classList.remove('is-activa');
        slides[actual].setAttribute('aria-hidden', 'true');
        slides[actual].setAttribute('inert', '');
        slides[nuevo].classList.add('is-activa');
        slides[nuevo].removeAttribute('aria-hidden');
        slides[nuevo].removeAttribute('inert');
        puntos[actual].removeAttribute('aria-current');
        puntos[nuevo].setAttribute('aria-current', 'true');
        // Dirección del movimiento (para la animación del texto).
        raiz.classList.toggle('is-atras', porPersona && n < actual);
        actual = nuevo;
      }
      ladoControles();
      // Cuando la persona navega, el lector de pantalla anuncia la nueva diapositiva.
      if (porPersona) {
        pista.setAttribute('aria-live', 'polite');
      }
      restante = intervalo;
      reiniciarProgreso();
      programar();
    }

    // En el diseño por capas, los controles van al lado contrario de las personas.
    function ladoControles() {
      raiz.classList.toggle('carrusel--controles-der', slides[actual].classList.contains('carrusel__slide--persona-izq'));
    }
    ladoControles();

    raiz.querySelector('.carrusel__anterior').addEventListener('click', function () {
      ir(actual - 1, true);
    });
    raiz.querySelector('.carrusel__siguiente').addEventListener('click', function () {
      ir(actual + 1, true);
    });
    puntos.forEach(function (p, i) {
      p.addEventListener('click', function () {
        ir(i, true);
      });
    });

    if (pausa) {
      function pintarPausa() {
        var detenido = pausadoPorPersona;
        pausa.setAttribute('aria-label', pausa.getAttribute(detenido ? 'data-texto-reanudar' : 'data-texto-pausar'));
        pausa.querySelector('i').className = 'fa-solid ' + (detenido ? 'fa-play' : 'fa-pause');
      }
      pausa.addEventListener('click', function () {
        pausadoPorPersona = !pausadoPorPersona;
        if (pausadoPorPersona) {
          detenerTemporal();
        } else {
          pista.setAttribute('aria-live', 'off');
        }
        pintarPausa();
        programar();
      });
      pintarPausa();
    }

    // Pausa mientras se mira o se usa (WCAG 2.2.2).
    function bloquear(motivo, valor) {
      if (bloqueos[motivo] === valor) {
        return;
      }
      if (valor) {
        detenerTemporal();
      }
      bloqueos[motivo] = valor;
      programar();
    }
    raiz.addEventListener('mouseenter', function () { bloquear('cursor', true); });
    raiz.addEventListener('mouseleave', function () { bloquear('cursor', false); });
    raiz.addEventListener('focusin', function () { bloquear('foco', true); });
    raiz.addEventListener('focusout', function (e) {
      if (!raiz.contains(e.relatedTarget)) {
        bloquear('foco', false);
      }
    });
    document.addEventListener('visibilitychange', function () {
      bloquear('oculto', document.hidden);
    });

    // Teclado: flechas izquierda/derecha cuando el foco está en el carrusel.
    raiz.addEventListener('keydown', function (e) {
      if (e.target.closest('input, textarea')) {
        return;
      }
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        ir(actual - 1, true);
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        ir(actual + 1, true);
      }
    });

    // Deslizar con el dedo (solo gestos horizontales claros; el scroll vertical no se toca).
    var x0 = null;
    var y0 = null;
    pista.addEventListener('pointerdown', function (e) {
      if (e.pointerType !== 'mouse') {
        x0 = e.clientX;
        y0 = e.clientY;
      }
    });
    pista.addEventListener('pointerup', function (e) {
      if (x0 === null) {
        return;
      }
      var dx = e.clientX - x0;
      var dy = e.clientY - y0;
      x0 = null;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) {
        ir(actual + (dx < 0 ? 1 : -1), true);
      }
    });
    pista.addEventListener('pointercancel', function () {
      x0 = null;
    });

    raiz.classList.add('is-listo');
    programar();
  });
})();
