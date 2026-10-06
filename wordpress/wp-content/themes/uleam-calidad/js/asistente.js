/**
 * Asistente virtual: ventana de chat flotante (ver inc/asistente.php).
 * La conversación se guarda solo en esta pestaña (sessionStorage) para no perderla al cambiar de página.
 */
(function () {
  'use strict';

  var cfg = window.uleamAsistente;
  var raiz = document.getElementById('asistente');
  if (!cfg || !raiz) {
    return;
  }

  var fab = raiz.querySelector('.asistente__fab');
  var panel = raiz.querySelector('.asistente__panel');
  var log = raiz.querySelector('.asistente__log');
  var form = raiz.querySelector('.asistente__form');
  var entrada = raiz.querySelector('.asistente__entrada');
  var enviar = raiz.querySelector('.asistente__enviar');
  var CLAVE = 'uleamAsistente';
  var conversacion = [];
  var ocupado = false;

  function guardar() {
    try {
      sessionStorage.setItem(CLAVE, JSON.stringify({ abierto: !panel.hidden, mensajes: conversacion.slice(-40) }));
    } catch (e) { /* sin almacenamiento: la conversación solo dura en esta página */ }
  }

  function leer() {
    try {
      return JSON.parse(sessionStorage.getItem(CLAVE)) || null;
    } catch (e) {
      return null;
    }
  }

  function el(tag, clase, texto) {
    var n = document.createElement(tag);
    if (clase) {
      n.className = clase;
    }
    if (texto) {
      n.textContent = texto;
    }
    return n;
  }

  function bajar() {
    log.scrollTop = log.scrollHeight;
  }

  // Texto plano (bienvenida, mensajes del usuario) → párrafos seguros.
  function parrafos(texto) {
    var frag = document.createDocumentFragment();
    texto.split(/\n+/).forEach(function (linea) {
      if (linea.trim()) {
        frag.appendChild(el('p', '', linea));
      }
    });
    return frag;
  }

  function tarjeta(f) {
    var a = el('a', 'asistente__doc');
    a.href = f.url;
    if (f.archivo) {
      a.target = '_blank';
      a.rel = 'noopener';
    }
    var icono = el('i', f.icono);
    icono.setAttribute('aria-hidden', 'true');
    a.appendChild(el('span', 'asistente__doc-icono')).appendChild(icono);
    var cuerpo = a.appendChild(el('span', 'asistente__doc-cuerpo'));
    cuerpo.appendChild(el('span', 'asistente__doc-titulo', f.titulo));
    var meta = [f.etiqueta, f.datos].filter(Boolean).join(' · ');
    if (meta) {
      cuerpo.appendChild(el('span', 'asistente__doc-meta', meta));
    }
    if (f.archivo) {
      a.appendChild(el('span', 'screen-reader-text', ' (se abre en otra pestaña)'));
    }
    var fila = el('li', 'asistente__doc-fila');
    fila.appendChild(a);
    if (f.ubicacion && f.ubicacion.url) {
      var ub = el('a', 'asistente__doc-ubicacion', 'Ver en ' + f.ubicacion.area);
      ub.href = f.ubicacion.url;
      fila.appendChild(ub);
    }
    return fila;
  }

  function contacto(ultima) {
    var caja = el('div', 'asistente__contacto');
    caja.appendChild(el('p', 'asistente__contacto-titulo', '¿Prefieres hablar con una persona?'));
    var acciones = caja.appendChild(el('div', 'asistente__contacto-acciones'));
    if (cfg.email) {
      var mail = el('a', 'asistente__boton');
      mail.href = 'mailto:' + cfg.email +
        '?subject=' + encodeURIComponent('Consulta desde el sitio web') +
        (ultima ? '&body=' + encodeURIComponent('Hola, mi consulta es: ' + ultima + '\n\n') : '');
      mail.innerHTML = '<i class="fa-solid fa-envelope" aria-hidden="true"></i> ';
      mail.appendChild(document.createTextNode('Escribir un correo'));
      acciones.appendChild(mail);
    }
    if (cfg.telefono) {
      var tel = el('a', 'asistente__boton asistente__boton--sec');
      // Solo el número principal (sin extensiones: "05 2623 740 ext. 181").
      tel.href = 'tel:' + cfg.telefono.split(/ext|\/|,/i)[0].replace(/[^\d+]/g, '');
      tel.innerHTML = '<i class="fa-solid fa-phone" aria-hidden="true"></i> ';
      tel.appendChild(document.createTextNode(cfg.telefono));
      acciones.appendChild(tel);
    }
    return caja;
  }

  // m = { rol: 'usuario'|'bot', texto, html?, fuentes?, ver_todos?, contacto?, consulta? }
  function pintar(m) {
    var burbuja = el('div', 'asistente__msg asistente__msg--' + m.rol);
    if (m.rol === 'bot') {
      burbuja.appendChild(el('span', 'screen-reader-text', cfg.nombre + ': '));
    } else {
      burbuja.appendChild(el('span', 'screen-reader-text', 'Tú: '));
    }
    var cuerpo = burbuja.appendChild(el('div', 'asistente__msg-texto'));
    if (m.html) {
      cuerpo.insertAdjacentHTML('beforeend', m.html); // HTML ya saneado en el servidor.
    } else {
      cuerpo.appendChild(parrafos(m.texto || ''));
    }
    if (m.fuentes && m.fuentes.length) {
      var lista = burbuja.appendChild(el('ul', 'asistente__docs'));
      m.fuentes.forEach(function (f) {
        lista.appendChild(tarjeta(f));
      });
    }
    if (m.ver_todos) {
      var todos = el('a', 'asistente__ver-todos', 'Ver los ' + m.ver_todos.total + ' resultados en el buscador');
      todos.href = m.ver_todos.url;
      burbuja.appendChild(todos);
    }
    if (m.contacto) {
      burbuja.appendChild(contacto(m.consulta));
    }
    log.appendChild(burbuja);
    bajar();
  }

  function quitarChips() {
    log.querySelectorAll('.asistente__chips').forEach(function (c) {
      c.remove();
    });
  }

  function sugerencias(lista) {
    lista = lista || cfg.sugerencias;
    if (!lista || !lista.length) {
      return;
    }
    var caja = el('div', 'asistente__chips');
    caja.setAttribute('role', 'group');
    caja.setAttribute('aria-label', 'Sugerencias');
    lista.forEach(function (s) {
      var b = el('button', 'asistente__chip', s);
      b.type = 'button';
      b.addEventListener('click', function () {
        preguntar(s);
      });
      caja.appendChild(b);
    });
    log.appendChild(caja);
  }

  function iniciar() {
    log.textContent = '';
    conversacion = [];
    pintar({ rol: 'bot', texto: cfg.bienvenida });
    sugerencias();
  }

  function escribiendo(si) {
    var previo = log.querySelector('.asistente__escribiendo');
    if (previo) {
      previo.remove();
    }
    if (si) {
      var p = el('div', 'asistente__msg asistente__msg--bot asistente__escribiendo');
      // Texto oculto (no aria-label: no está permitido en un div sin rol); los puntos son decorativos.
      p.innerHTML = '<span class="screen-reader-text">Buscando…</span><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>';
      log.appendChild(p);
      bajar();
    }
  }

  function preguntar(texto) {
    texto = (texto || '').trim();
    if (!texto || ocupado) {
      return;
    }
    quitarChips();
    var historial = conversacion.slice(-6).map(function (m) {
      return { rol: m.rol, texto: m.texto || m.plano || '' };
    });
    var mio = { rol: 'usuario', texto: texto };
    conversacion.push(mio);
    pintar(mio);
    guardar();
    entrada.value = '';
    ajustarAltura();
    ocupado = true;
    enviar.disabled = true;
    escribiendo(true);

    var inicio = Date.now();
    function conPausa(fn) {
      return function (valor) {
        return new Promise(function (ok) {
          setTimeout(function () { ok(fn(valor)); }, Math.max(0, 550 - (Date.now() - inicio)));
        });
      };
    }

    fetch(cfg.endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mensaje: texto, historial: historial })
    })
      .then(function (r) {
        return r.json().then(function (datos) {
          if (!r.ok) {
            throw new Error(datos && datos.message ? datos.message : 'error');
          }
          return datos;
        });
      })
      .then(conPausa(function (d) {
        var tmp = document.createElement('div');
        tmp.innerHTML = d.html || '';
        var resp = {
          rol: 'bot',
          html: d.html,
          plano: tmp.textContent.trim(),
          fuentes: d.fuentes,
          ver_todos: d.ver_todos,
          contacto: d.contacto,
          sugerencias: d.sugerencias || [],
          consulta: texto
        };
        escribiendo(false);
        conversacion.push(resp);
        pintar(resp);
        sugerencias(resp.sugerencias);
        bajar();
      }))
      .catch(conPausa(function (e) {
        escribiendo(false);
        var msg = e && e.message && e.message !== 'error' && e.message.indexOf('JSON') === -1
          ? e.message
          : 'No pude conectarme en este momento. Inténtalo de nuevo o comunícate con la Dirección.';
        var err = { rol: 'bot', texto: msg, contacto: true, consulta: texto };
        conversacion.push(err);
        pintar(err);
      }))
      .then(function () {
        ocupado = false;
        enviar.disabled = false;
        guardar();
        if (!panel.hidden && (!pantallaCompleta.matches || document.activeElement === entrada)) {
          entrada.focus();
        }
      });
  }

  // En celulares y ventanas bajas el chat ocupa toda la pantalla (igual que en css/asistente.css):
  // se comporta como un diálogo modal (fondo bloqueado y foco dentro del chat).
  var pantallaCompleta = window.matchMedia('(max-width: 600px), (max-height: 500px)');
  var vv = window.visualViewport;

  // Con el teclado virtual abierto, el chat se ajusta al área visible (iOS no lo hace solo).
  function ajustarAlTeclado() {
    if (!vv || panel.hidden || !pantallaCompleta.matches) {
      panel.style.removeProperty('--asistente-alto');
      panel.style.removeProperty('--asistente-arriba');
      return;
    }
    panel.style.setProperty('--asistente-alto', Math.round(vv.height) + 'px');
    panel.style.setProperty('--asistente-arriba', Math.round(vv.offsetTop) + 'px');
    bajar();
  }
  if (vv) {
    vv.addEventListener('resize', ajustarAlTeclado);
    vv.addEventListener('scroll', ajustarAlTeclado);
  }

  function modoPantalla() {
    var modal = !panel.hidden && pantallaCompleta.matches;
    document.documentElement.classList.toggle('asistente-pantalla-completa', modal);
    if (modal) {
      panel.setAttribute('aria-modal', 'true');
    } else {
      panel.removeAttribute('aria-modal');
    }
    ajustarAlTeclado();
  }
  if (pantallaCompleta.addEventListener) {
    pantallaCompleta.addEventListener('change', modoPantalla);
  }

  // Foco atrapado dentro del chat mientras ocupa toda la pantalla.
  panel.addEventListener('keydown', function (e) {
    if (e.key !== 'Tab' || !pantallaCompleta.matches) {
      return;
    }
    var focos = Array.prototype.filter.call(
      panel.querySelectorAll('button, a[href], textarea, input'),
      function (el) { return !el.disabled && el.offsetParent !== null; }
    );
    if (!focos.length) {
      return;
    }
    var primero = focos[0];
    var ultimo = focos[focos.length - 1];
    if (e.shiftKey && document.activeElement === primero) {
      e.preventDefault();
      ultimo.focus();
    } else if (!e.shiftKey && document.activeElement === ultimo) {
      e.preventDefault();
      primero.focus();
    }
  });

  function abrir(enfocar) {
    panel.hidden = false;
    raiz.classList.add('is-abierto');
    fab.setAttribute('aria-expanded', 'true');
    if (!log.children.length) {
      iniciar();
    }
    modoPantalla();
    bajar();
    if (enfocar) {
      // En celulares no se abre el teclado de inmediato: tapaba la bienvenida y las sugerencias.
      (pantallaCompleta.matches ? panel.querySelector('[data-asistente-cerrar]') : entrada).focus();
    }
    guardar();
  }

  function cerrar() {
    panel.hidden = true;
    raiz.classList.remove('is-abierto');
    fab.setAttribute('aria-expanded', 'false');
    modoPantalla();
    fab.focus();
    guardar();
  }

  function ajustarAltura() {
    entrada.style.height = 'auto';
    entrada.style.height = Math.min(entrada.scrollHeight, 120) + 'px';
  }

  log.addEventListener('click', function (e) {
    var a = e.target.closest('a[href]');
    if (!a || a.target === '_blank' || /^(mailto|tel):/.test(a.getAttribute('href'))) {
      return;
    }
    var mismaPagina = a.pathname === window.location.pathname && a.hash;
    if (pantallaCompleta.matches || mismaPagina) {
      // Se guarda "cerrado" antes de navegar: la página nueva no lo vuelve a abrir.
      panel.hidden = true;
      raiz.classList.remove('is-abierto');
      fab.setAttribute('aria-expanded', 'false');
      modoPantalla();
      guardar();
    }
  });

  fab.addEventListener('click', function () {
    if (panel.hidden) {
      abrir(true);
    } else {
      cerrar();
    }
  });
  raiz.querySelector('[data-asistente-cerrar]').addEventListener('click', cerrar);
  raiz.querySelector('[data-asistente-reiniciar]').addEventListener('click', function () {
    iniciar();
    guardar();
    entrada.focus();
  });
  panel.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      cerrar();
    }
  });
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    preguntar(entrada.value);
  });
  entrada.addEventListener('keydown', function (e) {
    // Enter envía; Shift+Enter hace un salto de línea.
    if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
      e.preventDefault();
      preguntar(entrada.value);
    }
  });
  entrada.addEventListener('input', ajustarAltura);

  // Recupera la conversación al cambiar de página.
  var previo = leer();
  if (previo && previo.mensajes && previo.mensajes.length) {
    conversacion = previo.mensajes;
    pintar({ rol: 'bot', texto: cfg.bienvenida });
    conversacion.forEach(pintar);
    var ultima = conversacion[conversacion.length - 1];
    if (ultima && ultima.rol === 'bot' && ultima.sugerencias) {
      sugerencias(ultima.sugerencias);
    }
  }
  if (previo && previo.abierto) {
    abrir(false);
  }
})();
