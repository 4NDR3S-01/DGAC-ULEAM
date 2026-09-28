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

  // Título padre con submenú en dispositivos táctiles / pantallas pequeñas
  var parentMenuItems = document.querySelectorAll('.nav-inner .menu-item-has-children');
  parentMenuItems.forEach(function (item) {
    var link = item.querySelector(':scope > a');
    if (link) {
      link.addEventListener('click', function (e) {
        if (window.innerWidth <= 860) {
          var hasSubmenu = item.querySelector('.sub-menu');
          if (hasSubmenu) {
            var href = link.getAttribute('href') || '';
            if (href === '#' || href === '' || !item.classList.contains('is-open')) {
              e.preventDefault();
              item.classList.toggle('is-open');
            }
          }
        }
      });
    }
  });

  // Control de elemento activo en el menú (resaltado único)
  function setActiveMenuItem(targetHref) {
    var allItems = document.querySelectorAll('.nav-inner li');
    var allLinks = document.querySelectorAll('.nav-inner a');

    allItems.forEach(function (li) {
      li.classList.remove('current-menu-item', 'active', 'current_page_item');
    });
    allLinks.forEach(function (a) {
      a.classList.remove('active');
    });

    var matchedLink = null;

    if (targetHref && targetHref !== '#') {
      for (var i = 0; i < allLinks.length; i++) {
        var link = allLinks[i];
        var href = link.getAttribute('href') || '';
        if (targetHref.charAt(0) === '#' && href.indexOf(targetHref) !== -1) {
          matchedLink = link;
          break;
        } else if (href === targetHref) {
          matchedLink = link;
          break;
        }
      }
    }

    if (!matchedLink) {
      matchedLink = document.querySelector('.nav-inner > li:first-child > a') ||
                    document.querySelector('.nav-inner a[href*="#inicio"]') ||
                    document.querySelector('.nav-inner a');
    }

    if (matchedLink) {
      matchedLink.classList.add('active');
      var topLi = matchedLink.closest('.nav-inner > li');
      if (topLi) {
        topLi.classList.add('current-menu-item', 'active');
      }
    }
  }

  // Marcar al hacer clic en cualquier enlace del menú
  var menuLinks = document.querySelectorAll('.nav-inner a');
  menuLinks.forEach(function (a) {
    a.addEventListener('click', function () {
      var href = a.getAttribute('href') || '';
      if (href && href !== '#') {
        var hash = href.indexOf('#') !== -1 ? href.substring(href.indexOf('#')) : '';
        setActiveMenuItem(hash || href);

        // Si es móvil, cerrar el menú al hacer clic en un enlace de destino
        if (window.innerWidth <= 860) {
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
      }
    });
  });

  // Inicializar estado activo al cargar
  var initialTarget = window.location.hash || '#inicio';
  setActiveMenuItem(initialTarget);

  // Scrollspy para actualizar el resaltado según la sección visible
  var sections = document.querySelectorAll('section[id], footer[id]');
  if (sections.length && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting && entry.intersectionRatio >= 0.25) {
            var id = entry.target.getAttribute('id');
            if (id) {
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

