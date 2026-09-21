/**
 * Inicialización global de Select2 para el Sistema de Tutorías UPDS.
 *
 * Uso: agrega la clase "select2-enabled" a cualquier <select> y quedará
 * convertido en un buscador con todas las cadenas en español.
 *
 * Requisitos (cargados en views/layouts/footer.php):
 *   - jQuery
 *   - select2.min.js
 *
 * Notas de integración:
 *   - Select2 dispara el evento "change" sobre el <select> nativo, por lo que
 *     el JavaScript existente (solicitar tutoría, usuarios, etc.) sigue
 *     funcionando sin cambios.
 *   - Para obtener/actualizar el valor se usa la API normal de Select2:
 *       $('#miSelect').val('3').trigger('change');
 */
(function () {
  'use strict';

  // Traducciones al español (Select2 4.x). Se completan todas las claves para
  // evitar que aparezcan textos en inglés en la interfaz.
  var idiomaEspanol = {
    errorLoading: function () {
      return 'No se pudieron cargar los resultados.';
    },
    inputTooLong: function (args) {
      var exceso = args.input.length - args.maximum;
      return 'Por favor elimina ' + exceso + ' carácter' + (exceso === 1 ? '' : 'es') + '.';
    },
    inputTooShort: function (args) {
      var faltan = args.minimum - args.input.length;
      return 'Ingresa ' + faltan + ' carácter' + (faltan === 1 ? '' : 'es') + ' o más.';
    },
    loadingMore: function () {
      return 'Cargando más resultados…';
    },
    maximumSelected: function (args) {
      return 'Solo puedes seleccionar ' + args.maximum + ' elemento' + (args.maximum === 1 ? '' : 's') + '.';
    },
    noResults: function () {
      return 'No se encontraron resultados.';
    },
    searching: function () {
      return 'Buscando…';
    },
    removeAllItems: function () {
      return 'Eliminar todos los elementos';
    }
  };

  function inicializarSelect2(select) {
    var $select = window.jQuery(select);

    // Evita inicializar dos veces el mismo select.
    if ($select.data('select2')) {
      return;
    }

    var opciones = {
      width: '100%',
      language: idiomaEspanol,
      // Los listados del sistema usan placeholders nativos: los respetamos.
      placeholder: select.dataset.placeholder || 'Seleccione una opción',
      allowClear: select.dataset.allowClear !== 'false'
    };

    // Permite sobreescribir opciones desde el propio HTML, por ejemplo:
    //   data-select2-tags="true" para selects de etiquetas libres.
    if (select.dataset.select2Tags === 'true') {
      opciones.tags = true;
      opciones.tokenSeparators = [','];
    }
    if (select.dataset.select2Multiple === 'true') {
      opciones.closeOnSelect = false;
    }

    $select.select2(opciones);
  }

  function inicializarTodos(raiz) {
    var contenedor = raiz || document;
    contenedor.querySelectorAll('select.select2-enabled').forEach(inicializarSelect2);
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) {
      return;
    }
    inicializarTodos(document);
  });

  // Expuesto por si alguna vista agrega selects dinámicamente (por ejemplo vía
  // fetch) y necesita inicializarlos de nuevo tras insertarlos en el DOM.
  window.updsInicializarSelect2 = inicializarTodos;
})();
