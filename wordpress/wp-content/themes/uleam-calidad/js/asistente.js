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

  function sugerencias() {
    if (!cfg.sugerencias || !cfg.sugerencias.length) {
      return;
    }
    var caja = el('div', 'asistente__chips');
    caja.setAttribute('role', 'group');
    caja.setAttribute('aria-label', 'Sugerencias');
    cfg.sugerencias.forEach(function (s) {
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
      p.setAttribute('aria-label', 'Buscando…');
      p.innerHTML = '<span></span><span></span><span></span>';
      log.appendChild(p);
      bajar();
    }
  }

  function preguntar(texto) {
    texto = (texto || '').trim();
    if (!texto || ocupado) {
      return;
    }
    var chips = log.querySelector('.asistente__chips');
    if (chips) {
      chips.remove();
    }
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
      .then(function (d) {
        var tmp = document.createElement('div');
        tmp.innerHTML = d.html || '';
        var resp = {
          rol: 'bot',
          html: d.html,
          plano: tmp.textContent.trim(),
          fuentes: d.fuentes,
          ver_todos: d.ver_todos,
          contacto: d.contacto,
          consulta: texto
        };
        escribiendo(false);
        conversacion.push(resp);
        pintar(resp);
      })
      .catch(function (e) {
        escribiendo(false);
        var msg = e && e.message && e.message !== 'error' && e.message.indexOf('JSON') === -1
          ? e.message
          : 'No pude conectarme en este momento. Inténtalo de nuevo o comunícate con la Dirección.';
        var err = { rol: 'bot', texto: msg, contacto: true, consulta: texto };
        conversacion.push(err);
        pintar(err);
      })
      .then(function () {
        ocupado = false;
        enviar.disabled = false;
        guardar();
        if (!panel.hidden) {
          entrada.focus();
        }
      });
  }

  function abrir(enfocar) {
    panel.hidden = false;
    raiz.classList.add('is-abierto');
    fab.setAttribute('aria-expanded', 'true');
    if (!log.children.length) {
      iniciar();
    }
    bajar();
    if (enfocar) {
      entrada.focus();
    }
    guardar();
  }

  function cerrar() {
    panel.hidden = true;
    raiz.classList.remove('is-abierto');
    fab.setAttribute('aria-expanded', 'false');
    fab.focus();
    guardar();
  }

  function ajustarAltura() {
    entrada.style.height = 'auto';
    entrada.style.height = Math.min(entrada.scrollHeight, 120) + 'px';
  }

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
  }
  if (previo && previo.abierto) {
    abrir(false);
  }
})();
