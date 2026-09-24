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
})();
