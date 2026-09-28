/**
 * JavaScript del tema ULEAM Calidad
 */
(function () {
  'use strict';

  // Tabs de evaluación de desempeño
  var tabs = document.querySelectorAll('.tabs .tab');
  if (tabs.length) {
    tabs.forEach(function (btn) {
      btn.addEventListener('click', function () {
        tabs.forEach(function (b) {
          b.classList.remove('active');
        });
        btn.classList.add('active');
        var textEl = document.getElementById('periodText');
        if (textEl) {
          textEl.textContent =
            btn.getAttribute('data-text') ||
            'Consulta los resultados de la evaluación de desempeño académico y administrativo del período ' +
              btn.textContent +
              '.';
        }
      });
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

  // Título padre con submenú en dispositivos táctiles / pantallas pequeñas
  var parentMenuItems = document.querySelectorAll('.nav-inner .menu-item-has-children');
  parentMenuItems.forEach(function (item) {
    var link = item.querySelector(':scope > a');
    if (link) {
      link.addEventListener('click', function (e) {
        if (window.innerWidth <= 960) {
          var hasSubmenu = item.querySelector('.sub-menu');
          if (hasSubmenu) {
            e.preventDefault();
            e.stopPropagation();
            item.classList.toggle('is-open');
          }
        }
      });
    }
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
    if (!hasCurrent && (window.location.pathname === '/' || window.location.pathname.indexOf('index.php') !== -1)) {
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
})();

