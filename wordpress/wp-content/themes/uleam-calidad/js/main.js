/**
 * JavaScript del tema ULEAM Calidad
 */
(function () {
  'use strict';

  // Carrusel de fotos de la portada: cambia sola cada 6 s con un fundido (se detiene al pasar el ratón o enfocar)
  document.querySelectorAll('.photo-carousel').forEach(function (carousel) {
    var track = carousel.querySelector('.photo-carousel__track');
    var slides = carousel.querySelectorAll('.photo-carousel__slide');
    var dots = carousel.querySelectorAll('.photo-carousel__dot');
    if (!track || slides.length < 2) {
      return;
    }
    var current = 0;
    var timer = null;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function goTo(index) {
      current = (index + slides.length) % slides.length;
      slides.forEach(function (slide, i) {
        slide.classList.toggle('is-active', i === current);
        if (i === current) {
          slide.removeAttribute('aria-hidden');
        } else {
          slide.setAttribute('aria-hidden', 'true');
        }
      });
      dots.forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === current);
        if (i === current) {
          dot.setAttribute('aria-current', 'true');
        } else {
          dot.removeAttribute('aria-current');
        }
      });
    }

    function play() {
      if (reduceMotion || timer) {
        return;
      }
      timer = setInterval(function () {
        goTo(current + 1);
      }, 6000);
    }

    function pause() {
      clearInterval(timer);
      timer = null;
    }

    // Cambiar de foto a mano reinicia la cuenta, para que no salte enseguida a la siguiente.
    function goToManual(index) {
      goTo(index);
      if (timer) {
        pause();
        play();
      }
    }

    carousel.querySelector('.photo-carousel__nav--prev').addEventListener('click', function () {
      goToManual(current - 1);
    });
    carousel.querySelector('.photo-carousel__nav--next').addEventListener('click', function () {
      goToManual(current + 1);
    });
    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () {
        goToManual(i);
      });
    });
    track.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
        e.preventDefault();
        goToManual(current + (e.key === 'ArrowRight' ? 1 : -1));
      }
    });

    // Deslizar con el dedo en el celular
    var startX = null;
    track.addEventListener('touchstart', function (e) {
      startX = e.touches[0].clientX;
      pause();
    }, { passive: true });
    track.addEventListener('touchend', function (e) {
      if (startX === null) {
        return;
      }
      var dx = e.changedTouches[0].clientX - startX;
      startX = null;
      if (Math.abs(dx) > 40) {
        goTo(current + (dx < 0 ? 1 : -1));
      }
      play();
    });

    carousel.addEventListener('mouseenter', pause);
    carousel.addEventListener('mouseleave', play);
    carousel.addEventListener('focusin', pause);
    carousel.addEventListener('focusout', play);
    play();
  });

  // Pestañas Misión / Objetivo / Productos (DGAC Pill Tabs)
  var pillTabs = document.querySelectorAll('.dgac-pill-tab');
  if (pillTabs.length) {
    pillTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var container = tab.closest('.dgac-proc-intro') || tab.closest('.entry-content') || document;
        var target = tab.getAttribute('data-tab');
        var groupTabs = container.querySelectorAll('.dgac-pill-tab');
        var groupPanels = container.querySelectorAll('.dgac-overview-panel');

        groupTabs.forEach(function (t) {
          t.classList.remove('is-active');
          t.setAttribute('aria-selected', 'false');
        });
        groupPanels.forEach(function (p) {
          p.classList.remove('is-active');
        });

        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');
        var activePanel = container.querySelector('#panel-' + target);
        if (activePanel) {
          activePanel.classList.add('is-active');
        }
      });
    });
  }

  // Paneles colapsables interactivos (Catálogos e Información General)
  var allCollapseCards = document.querySelectorAll('.dgac-proc-collapse-card');
  if (allCollapseCards.length) {
    allCollapseCards.forEach(function (card) {
      var toggleLabel = card.querySelector('.dgac-proc-collapse-toggle-label');
      var openText = card.getAttribute('data-open-text') || 'Recoger';
      var closeText = card.getAttribute('data-close-text') || 'Mostrar';

      card.addEventListener('toggle', function () {
        if (toggleLabel) {
          toggleLabel.textContent = card.open ? openText : closeText;
        }
      });

      var closeBtn = card.querySelector('.dgac-proc-collapse-close-btn');
      if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
          e.preventDefault();
          card.open = false;
          card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
      }
    });
  }

  // Menú móvil y submenús interactivos
  var navToggle = document.getElementById('navToggle');
  var primaryMenu = document.getElementById('primaryMenu');

  if (navToggle && primaryMenu) {
    navToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = primaryMenu.classList.toggle('is-active');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      var icon = navToggle.querySelector('i');
      if (icon) {
        icon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
      }
    });

    document.addEventListener('click', function (e) {
      if (!primaryMenu.contains(e.target) && !navToggle.contains(e.target)) {
        primaryMenu.classList.remove('is-active');
        navToggle.setAttribute('aria-expanded', 'false');
        var icon = navToggle.querySelector('i');
        if (icon) {
          icon.className = 'fa-solid fa-bars';
        }
      }
    });
  }

  // Abrir submenú padre en móvil si contiene la página activa
  var currentAncestor = document.querySelector('.nav-inner .menu-item-has-children.current-menu-ancestor');
  if (currentAncestor) {
    currentAncestor.classList.add('is-open');
  }

  // Título padre con submenú en dispositivos táctiles y escritorio
  var parentMenuItems = document.querySelectorAll('.nav-inner .menu-item-has-children');
  parentMenuItems.forEach(function (item) {
    var link = item.querySelector(':scope > a');
    if (link) {
      link.addEventListener('click', function (e) {
        var href = link.getAttribute('href') || '';
        if (href === '#' || href === '' || href.charAt(0) === '#') {
          e.preventDefault();
          link.blur();
        }
        if (window.innerWidth <= 960) {
          var hasSubmenu = item.querySelector('.sub-menu');
          if (hasSubmenu) {
            e.stopPropagation();
            item.classList.toggle('is-open');
          }
        }
      });
    }

    // Al salir el cursor del elemento padre en escritorio, asegurar que no queden focos colgados
    item.addEventListener('mouseleave', function () {
      if (window.innerWidth > 960 && link) {
        link.blur();
      }
    });
  });

  // Control de elemento activo en el menú
  function setActiveMenuItem(targetHref) {
    if (!targetHref) return;

    var allItems = document.querySelectorAll('.nav-inner li');
    var allLinks = document.querySelectorAll('.nav-inner a');
    var matchedLink = null;

    if (targetHref.charAt(0) === '#') {
      for (var i = 0; i < allLinks.length; i++) {
        var link = allLinks[i];
        var href = link.getAttribute('href') || '';
        if (href.indexOf(targetHref) !== -1) {
          matchedLink = link;
          break;
        }
      }
    } else {
      var targetPath = targetHref.replace(/^(?:https?:\/\/[^\/]+)?/, '').replace(/\/$/, '') || '/';
      for (var j = 0; j < allLinks.length; j++) {
        var l = allLinks[j];
        var linkHref = l.getAttribute('href') || '';
        var linkPath = linkHref.replace(/^(?:https?:\/\/[^\/]+)?/, '').replace(/\/$/, '') || '/';
        if (linkPath === targetPath) {
          matchedLink = l;
          break;
        }
      }
    }

    if (matchedLink) {
      allItems.forEach(function (li) {
        li.classList.remove('current-menu-item', 'active', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent');
      });
      allLinks.forEach(function (a) {
        a.classList.remove('active');
      });

      matchedLink.classList.add('active');
      var matchedLi = matchedLink.closest('li');
      if (matchedLi) {
        matchedLi.classList.add('current-menu-item', 'active');
        var parentLi = matchedLi.parentElement ? matchedLi.parentElement.closest('.nav-inner > li') : null;
        if (parentLi && parentLi !== matchedLi) {
          parentLi.classList.add('current-menu-ancestor', 'active');
          var parentLink = parentLi.querySelector(':scope > a');
          if (parentLink) {
            parentLink.classList.add('active');
          }
        }
      }
    }
  }

  // Marcar al hacer clic en cualquier enlace del menú
  var menuLinks = document.querySelectorAll('.nav-inner a');
  menuLinks.forEach(function (a) {
    a.addEventListener('click', function () {
      var href = a.getAttribute('href') || '';
      if (!href || href === '#') return;

      var isHashOnly = href.charAt(0) === '#';
      var isSamePageHash = href.indexOf('#') !== -1 && (href.split('#')[0] === '' || href.split('#')[0] === window.location.href.split('#')[0] || href.split('#')[0] === window.location.pathname);

      if (isHashOnly || isSamePageHash) {
        var hash = href.substring(href.indexOf('#'));
        setActiveMenuItem(hash);
      }

      // Si es móvil, cerrar el menú al hacer clic en un enlace de navegación
      if (window.innerWidth <= 960) {
        var isParent = a.parentElement && a.parentElement.classList.contains('menu-item-has-children');
        if (!isParent || a.closest('.sub-menu')) {
          if (primaryMenu) {
            primaryMenu.classList.remove('is-active');
          }
          if (navToggle) {
            navToggle.setAttribute('aria-expanded', 'false');
            var icon = navToggle.querySelector('i');
            if (icon) {
              icon.className = 'fa-solid fa-bars';
            }
          }
        }
      }
    });
  });

  // Inicializar estado activo al cargar sólo si hay un ancla o falta la clase nativa
  if (window.location.hash) {
    setActiveMenuItem(window.location.hash);
  } else {
    var hasCurrent = document.querySelector('.nav-inner .current-menu-item, .nav-inner .current_page_item');
    // La página de resultados (/?s=…) también usa "/", pero no es Inicio.
    var isSearch = /[?&]s=/.test(window.location.search);
    if (!hasCurrent && !isSearch && (window.location.pathname === '/' || window.location.pathname.indexOf('index.php') !== -1)) {
      setActiveMenuItem('/');
    }
  }

  // Scrollspy solo cuando existan secciones internas y enlaces con ancla en el menú
  var sections = document.querySelectorAll('section[id], footer[id]');
  var hasInternalHashLinks = document.querySelector('.nav-inner a[href*="#"]');
  if (sections.length && hasInternalHashLinks && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting && entry.intersectionRatio >= 0.25) {
            var id = entry.target.getAttribute('id');
            if (id && document.querySelector('.nav-inner a[href*="#' + id + '"]')) {
              setActiveMenuItem('#' + id);
            }
          }
        });
      },
      {
        threshold: [0.25],
        rootMargin: '-80px 0px -40% 0px',
      }
    );

    sections.forEach(function (sec) {
      observer.observe(sec);
    });
  }

  // Repositorio documental: tarjetas de área, buscador con resaltado y "Expandir todos"
  function normalizar(s) {
    return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
  }
  function escaparHtml(s) {
    return s.replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  // Resalta q dentro de texto sin importar tildes ni mayúsculas.
  function resaltar(texto, q) {
    if (!q) {
      return escaparHtml(texto);
    }
    var base = '';
    var mapa = [];
    for (var i = 0; i < texto.length; i++) {
      var n = normalizar(texto[i]);
      for (var k = 0; k < n.length; k++) {
        base += n[k];
        mapa.push(i);
      }
    }
    var html = '';
    var desde = 0;
    var pos = base.indexOf(q);
    while (pos !== -1) {
      var ini = mapa[pos];
      var fin = mapa[pos + q.length - 1] + 1;
      html += escaparHtml(texto.slice(desde, ini)) + '<mark>' + escaparHtml(texto.slice(ini, fin)) + '</mark>';
      desde = fin;
      pos = base.indexOf(q, pos + q.length);
    }
    return html + escaparHtml(texto.slice(desde));
  }
  function plural(n, uno, varios) {
    return n + ' ' + (n === 1 ? uno : varios);
  }

  document.querySelectorAll('.dgac-repo').forEach(function (repo) {
    var repoTabs = repo.querySelectorAll('.dgac-repo-tab');
    var panels = repo.querySelectorAll('.dgac-repo-panel');
    var input = repo.querySelector('.dgac-repo-search__input');
    var noResults = repo.querySelector('.dgac-repo-noresults');
    var counter = repo.querySelector('.dgac-repo-count');
    var toggle = repo.querySelector('.dgac-repo-toggle');

    function activate(id, updateHash) {
      var found = false;
      panels.forEach(function (p) {
        var on = p.id === id;
        p.classList.toggle('is-active', on);
        found = found || on;
      });
      if (!found) {
        return false;
      }
      repoTabs.forEach(function (t) {
        var on = t.getAttribute('aria-controls') === id;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      if (updateHash && history.replaceState) {
        history.replaceState(null, '', '#' + id);
      }
      syncToggle();
      return true;
    }

    repoTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var id = tab.getAttribute('aria-controls');
        if (input && input.value) {
          // Durante una búsqueda, la tarjeta lleva a los resultados de esa área.
          var panel = repo.querySelector('#' + CSS.escape(id));
          if (panel && !panel.classList.contains('is-filtered-out')) {
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
          return;
        }
        activate(id, true);
      });
    });

    // Permite enlazar directo a un área: /aseguramiento-de-la-calidad/#planes-de-mejora
    if (location.hash) {
      activate(decodeURIComponent(location.hash.slice(1)), false);
    }
    window.addEventListener('hashchange', function () {
      if (activate(decodeURIComponent(location.hash.slice(1)), false)) {
        repo.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });

    // "Expandir todos" / "Contraer todos" sobre los acordeones visibles.
    function visibleAccordions() {
      var scope = repo.classList.contains('is-searching') ? repo : repo.querySelector('.dgac-repo-panel.is-active') || repo;
      return Array.prototype.filter.call(scope.querySelectorAll('.dgac-year-accordion'), function (d) {
        return !d.classList.contains('is-filtered-out');
      });
    }
    function syncToggle() {
      if (!toggle) {
        return;
      }
      var list = visibleAccordions();
      var allOpen = list.length > 0 && list.every(function (d) { return d.open; });
      toggle.setAttribute('aria-expanded', allOpen ? 'true' : 'false');
      toggle.querySelector('span').textContent = allOpen ? 'Contraer todos' : 'Expandir todos';
      toggle.hidden = list.length === 0;
    }
    if (toggle) {
      toggle.addEventListener('click', function () {
        var open = toggle.getAttribute('aria-expanded') !== 'true';
        visibleAccordions().forEach(function (d) { d.open = open; });
        syncToggle();
      });
      repo.querySelectorAll('.dgac-year-accordion').forEach(function (d) {
        d.addEventListener('toggle', syncToggle);
      });
      syncToggle();
    }

    // Búsqueda en todas las áreas a la vez.
    var titles = repo.querySelectorAll('.dgac-doc-row-title');
    titles.forEach(function (t) { t.setAttribute('data-original', t.textContent); });

    function filter(raw) {
      var q = normalizar(raw.trim());
      repo.classList.toggle('is-searching', q !== '');
      var total = 0;
      repo.querySelectorAll('.dgac-doc-row').forEach(function (row) {
        var title = row.querySelector('.dgac-doc-row-title');
        var match = !q || normalizar(row.getAttribute('data-buscar')).indexOf(q) !== -1;
        row.hidden = !match;
        if (title) {
          title.innerHTML = resaltar(title.getAttribute('data-original'), match ? q : '');
        }
        if (q && match) {
          total++;
        }
      });
      // Oculta grupos, años, subsecciones y áreas sin resultados; abre los que sí tienen.
      repo.querySelectorAll('.dgac-doc-group, .dgac-year-accordion, .dgac-repo-sub, .dgac-repo-panel').forEach(function (box) {
        var hits = box.querySelectorAll('.dgac-doc-row:not([hidden])').length;
        var visible = !q || hits > 0;
        box.classList.toggle('is-filtered-out', !visible);
        if (box.tagName === 'DETAILS') {
          var count = box.querySelector('.dgac-year-count');
          if (count) {
            count.textContent = q ? plural(hits, 'coincidencia', 'coincidencias') : count.getAttribute('data-total');
          }
          if (q && visible) {
            box.open = true;
          }
        }
      });
      // Coincidencias por área en sus tarjetas.
      repoTabs.forEach(function (tab) {
        var panel = repo.querySelector('#' + CSS.escape(tab.getAttribute('aria-controls')));
        var meta = tab.querySelector('.dgac-subtab-meta');
        var hits = panel ? panel.querySelectorAll('.dgac-doc-row:not([hidden])').length : 0;
        tab.classList.toggle('is-empty', q !== '' && hits === 0);
        if (meta) {
          meta.textContent = q ? plural(hits, 'coincidencia', 'coincidencias') : meta.getAttribute('data-total');
        }
      });
      if (counter) {
        counter.textContent = q ? plural(total, 'resultado', 'resultados') : repo.getAttribute('data-total');
      }
      if (noResults) {
        noResults.hidden = !q || total > 0;
      }
      syncToggle();
    }
    if (input) {
      var timer;
      input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { filter(input.value); }, 120);
      });
    }
  });

  // Gestión de Procesos: pestañas de subsistemas, buscador de procedimientos y "Expandir todos".
  // El contenido se edita en Páginas → Gestión de Procesos; aquí solo va el comportamiento.
  (function () {
    var subsysTabs = document.querySelectorAll('.dgac-subsistema-tab[data-subsys]');
    var subsysPanels = document.querySelectorAll('.dgac-subsistema-content');
    var searchInput = document.getElementById('dgacProcessSearch');
    var counter = document.getElementById('dgacProcCounter');
    var toggleBtn = document.getElementById('dgacToggleAllBtn');
    var toggleText = document.getElementById('dgacToggleAllText');
    if (!subsysTabs.length) {
      return;
    }
    // Las cifras se calculan de los procedimientos reales: al agregar o quitar uno en el editor, se actualizan solas.
    document.querySelectorAll('.dgac-macro-card').forEach(function (card) {
      var count = card.querySelector('.dgac-macro-count');
      if (count) {
        count.textContent = plural(card.querySelectorAll('.dgac-proc-row').length, 'procedimiento', 'procedimientos');
      }
    });
    subsysTabs.forEach(function (tab) {
      var panel = document.getElementById('subsys-' + tab.getAttribute('data-subsys'));
      var meta = tab.querySelector('.dgac-subtab-meta');
      if (panel && meta) {
        meta.textContent = plural(panel.querySelectorAll('.dgac-macro-card').length, 'macroproceso', 'macroprocesos') + ' · ' +
          plural(panel.querySelectorAll('.dgac-proc-row').length, 'procedimiento', 'procedimientos');
      }
    });
    function activePanel() {
      return document.querySelector('.dgac-subsistema-content.is-active');
    }
    function updateCounter() {
      var panel = activePanel();
      if (!panel || !counter) {
        return;
      }
      var total = panel.querySelectorAll('.dgac-proc-row').length;
      var visibles = panel.querySelectorAll('.dgac-proc-row:not([hidden])').length;
      counter.textContent = searchInput && searchInput.value.trim()
        ? plural(visibles, 'procedimiento encontrado', 'procedimientos encontrados')
        : plural(total, 'procedimiento en este subsistema', 'procedimientos en este subsistema');
    }
    function updateToggle() {
      var panel = activePanel();
      if (!panel || !toggleBtn || !toggleText) {
        return;
      }
      var cards = Array.prototype.slice.call(panel.querySelectorAll('.dgac-macro-card:not([hidden])'));
      var allOpen = cards.length > 0 && cards.every(function (c) { return c.open; });
      toggleText.textContent = allOpen ? 'Contraer todos' : 'Expandir todos';
      toggleBtn.setAttribute('aria-expanded', allOpen ? 'true' : 'false');
    }
    function filtrar() {
      var panel = activePanel();
      if (!panel) {
        return;
      }
      var q = normalizar(searchInput ? searchInput.value.trim() : '');
      panel.querySelectorAll('.dgac-proc-row').forEach(function (row) {
        // Busca en lo que la persona ve: nombre del procedimiento y de su macroproceso.
        var nombre = row.querySelector('.dgac-proc-name');
        var macro = row.closest('.dgac-macro-card');
        var titulo = macro ? macro.querySelector('.dgac-macro-title') : null;
        var texto = normalizar((nombre ? nombre.textContent : row.textContent) + ' ' + (titulo ? titulo.textContent : ''));
        row.hidden = q !== '' && texto.indexOf(q) === -1;
      });
      panel.querySelectorAll('.dgac-macro-card').forEach(function (card) {
        var visible = card.querySelector('.dgac-proc-row:not([hidden])');
        card.hidden = !visible;
        if (q && visible) {
          card.open = true;
        }
      });
      updateCounter();
      updateToggle();
    }

    subsysTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var target = tab.getAttribute('data-subsys');
        subsysTabs.forEach(function (t) {
          t.classList.toggle('is-active', t === tab);
          t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
        });
        subsysPanels.forEach(function (p) {
          p.classList.toggle('is-active', p.id === 'subsys-' + target);
        });
        filtrar();
      });
    });
    if (searchInput) {
      searchInput.addEventListener('input', filtrar);
    }
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        var panel = activePanel();
        if (!panel) {
          return;
        }
        var cards = Array.prototype.slice.call(panel.querySelectorAll('.dgac-macro-card:not([hidden])'));
        var expandir = !cards.every(function (c) { return c.open; });
        cards.forEach(function (c) { c.open = expandir; });
        if (!expandir) {
          panel.querySelectorAll('.dgac-proc-row').forEach(function (r) { r.open = false; });
        }
        updateToggle();
      });
    }
    document.querySelectorAll('.dgac-macro-card').forEach(function (card) {
      card.addEventListener('toggle', updateToggle);
    });
    updateCounter();
    updateToggle();
  })();

  // Sugerencias mientras se escribe en los buscadores del sitio (cabecera y página de resultados)
  if (window.uleamBusqueda && window.fetch) {
    document.querySelectorAll('form.search, form.dgac-search-hero').forEach(function (form, n) {
      var input = form.querySelector('input[type="search"]');
      if (!input) {
        return;
      }
      var box = document.createElement('div');
      var listId = 'dgac-suggest-' + n;
      box.className = 'dgac-suggest';
      box.hidden = true;
      form.appendChild(box);
      input.setAttribute('autocomplete', 'off');
      input.setAttribute('role', 'combobox');
      input.setAttribute('aria-autocomplete', 'list');
      input.setAttribute('aria-expanded', 'false');
      input.setAttribute('aria-controls', listId);

      var timer;
      var controller;
      var active = -1;
      var lastQuery = '';

      function options() {
        return box.querySelectorAll('.dgac-suggest__item, .dgac-suggest__footer');
      }
      function close() {
        box.hidden = true;
        active = -1;
        input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant');
      }
      function setActive(i) {
        var opts = options();
        if (!opts.length) {
          return;
        }
        active = (i + opts.length) % opts.length;
        opts.forEach(function (o, k) {
          o.classList.toggle('is-active', k === active);
          o.setAttribute('aria-selected', k === active ? 'true' : 'false');
        });
        input.setAttribute('aria-activedescendant', opts[active].id);
        opts[active].scrollIntoView({ block: 'nearest' });
      }
      function render(data, q) {
        var html = '';
        if (!data.items.length) {
          html = '<p class="dgac-suggest__empty">Sin coincidencias para «' + escaparHtml(q) + '». Presiona Enter para buscar en todo el sitio.</p>';
        } else {
          html = '<ul class="dgac-suggest__list" id="' + listId + '" role="listbox" aria-label="Sugerencias">';
          data.items.forEach(function (it, k) {
            var meta = [it.etiqueta + (it.ext ? ' · ' + it.ext : '')].concat(it.datos || []).join(' · ');
            var nueva = it.archivo ? ' target="_blank" rel="noopener"' : '';
            html += '<li class="dgac-suggest__item dgac-suggest__item--' + it.tipo + '" id="' + listId + '-' + k + '" role="option" aria-selected="false">' +
              '<a href="' + escaparHtml(it.url) + '"' + nueva + ' tabindex="-1">' +
              '<span class="dgac-suggest__icon"><i class="' + escaparHtml(it.icono) + '" aria-hidden="true"></i></span>' +
              '<span class="dgac-suggest__text"><span class="dgac-suggest__title">' + it.titulo_html + '</span>' +
              '<span class="dgac-suggest__meta">' + escaparHtml(meta) + '</span></span></a></li>';
          });
          html += '</ul>';
          html += '<a class="dgac-suggest__footer" id="' + listId + '-todos" role="option" aria-selected="false" href="' + escaparHtml(data.todos) + '">' +
            'Ver ' + (data.total === 1 ? 'el resultado' : 'los ' + data.total + ' resultados') + ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>';
        }
        box.innerHTML = html;
        box.hidden = false;
        active = -1;
        input.setAttribute('aria-expanded', 'true');
      }
      function search() {
        var q = input.value.trim();
        if (q.length < 2) {
          close();
          return;
        }
        if (q === lastQuery && !box.hidden) {
          return;
        }
        lastQuery = q;
        if (controller) {
          controller.abort();
        }
        controller = 'AbortController' in window ? new AbortController() : null;
        var url = window.uleamBusqueda.endpoint + (window.uleamBusqueda.endpoint.indexOf('?') === -1 ? '?' : '&') + 'q=' + encodeURIComponent(q);
        fetch(url, controller ? { signal: controller.signal } : {})
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (input.value.trim() === q) {
              render(data, q);
            }
          })
          .catch(function () {});
      }

      input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(search, 220);
      });
      input.addEventListener('focus', function () {
        if (input.value.trim().length >= 2 && box.innerHTML) {
          box.hidden = false;
          input.setAttribute('aria-expanded', 'true');
        }
      });
      input.addEventListener('keydown', function (e) {
        if (box.hidden) {
          return;
        }
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          setActive(active + 1);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          setActive(active - 1);
        } else if (e.key === 'Escape') {
          close();
        } else if (e.key === 'Enter' && active > -1) {
          e.preventDefault();
          var opt = options()[active];
          var link = opt.tagName === 'A' ? opt : opt.querySelector('a');
          if (link) {
            link.click();
          }
        }
      });
      document.addEventListener('click', function (e) {
        if (!form.contains(e.target)) {
          close();
        }
      });
    });
  }
})();

