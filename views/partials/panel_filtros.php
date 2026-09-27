<?php
/**
 * Panel de filtros reutilizable (SPRINT 6 - UX/modularización).
 * Configuración esperada desde la vista/controlador (todas opcionales; los
 * datos dinámicos deben venir del controlador/modelo, nunca HTML estático):
 *   $filtroQ            : texto actual de búsqueda por teclado ('' desactiva)
 *   $filtroQNombre      : nombre del campo de búsqueda (default 'q')
 *   $filtroQPlaceholder : texto de ayuda del campo
 *   $filtroQEtiqueta    : etiqueta sobre el campo (default 'Buscar')
 *   $filtroQAuto        : bool -> envío automático al pausar la escritura
 *   $filtroQAutocompleteUrl : URL JSON que devuelve [{id,label},...] para
 *                             autocompletar en tiempo real (datalist)
 *   $filtroSelectores   : [{nombre, etiqueta, opciones:[[valor,texto]...],
 *                           seleccionado, minWidth}, ...] (filtros select)
 *   $filtroFechas       : ['desde'=>['nombre','valor'],'hasta'=>[...]]
 *   $filtroTabs         : ['nombre'=>hidden, 'grupos'=>[[valor,texto]...],
 *                          'activo'=>valor] (pestañas de respuesta inmediata)
 *   $filtroPorPagina    : int actual del selector registros por página
 *   $filtroPorPaginaOpciones : [10,25,50]
 *   $filtroOcultos      : [nombre=>valor,...] campos ocultos (orden, dir, ...)
 *   $filtroAction       : action del form GET ('' = URL actual)
 *   $filtroBotones      : bool -> botón Filtrar + Limpiar
 *   $filtroLimpiarUrl   : URL del botón Limpiar
 */
$filtroQ = $filtroQ ?? '';
$filtroQNombre = $filtroQNombre ?? 'q';
$filtroQPlaceholder = $filtroQPlaceholder ?? 'Buscar...';
$filtroQEtiqueta = $filtroQEtiqueta ?? 'Buscar';
$filtroQAuto = (bool) ($filtroQAuto ?? false);
$filtroQAutocompleteUrl = $filtroQAutocompleteUrl ?? '';
$filtroSelectores = $filtroSelectores ?? [];
$filtroFechas = $filtroFechas ?? [];
$filtroTabs = $filtroTabs ?? [];
$filtroPorPagina = $filtroPorPagina ?? null;
$filtroPorPaginaOpciones = $filtroPorPaginaOpciones ?? [10, 25, 50];
$filtroOcultos = $filtroOcultos ?? [];
$filtroAction = $filtroAction ?? '';
$filtroBotones = (bool) ($filtroBotones ?? true);
$filtroLimpiarUrl = $filtroLimpiarUrl ?? '';

$idUnico = 'pf-' . substr(md5((string) mt_rand()), 0, 6);
$nombreTab = (string) ($filtroTabs['nombre'] ?? 'tipo_evento');
$tabActivo = (string) ($filtroTabs['activo'] ?? '');
?>
<form method="GET" action="<?= htmlspecialchars($filtroAction, ENT_QUOTES, 'UTF-8') ?>" class="panel-filtros" id="<?= htmlspecialchars($idUnico, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
  <?php foreach ($filtroOcultos as $k => $v): ?>
    <input type="hidden" name="<?= htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
  <input type="hidden" name="pagina" value="1">

  <?php if (!empty($filtroTabs['grupos'])): ?>
    <div class="historial-tabs d-flex flex-wrap gap-2 mb-3">
      <input type="hidden" name="<?= htmlspecialchars($nombreTab, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($tabActivo, ENT_QUOTES, 'UTF-8') ?>">
      <?php foreach ($filtroTabs['grupos'] as $grupo): ?>
        <button type="button"
                class="historial-tab<?= ($tabActivo === (string) $grupo['valor']) ? ' active' : '' ?>"
                data-filtro-hidden="<?= htmlspecialchars($nombreTab, ENT_QUOTES, 'UTF-8') ?>"
                data-filtro-val="<?= htmlspecialchars((string) $grupo['valor'], ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars((string) $grupo['texto'], ENT_QUOTES, 'UTF-8') ?>
        </button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="d-flex flex-wrap gap-2 align-items-end filtro-campos">
    <div class="d-flex flex-column">
      <label class="text-muted text-uppercase fw-semibold mb-1 label-filtro"><?= htmlspecialchars($filtroQEtiqueta, ENT_QUOTES, 'UTF-8') ?></label>
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0 text-muted rounded-3 rounded-end-0"><i class="bi bi-search"></i></span>
        <input type="search"
               name="<?= htmlspecialchars($filtroQNombre, ENT_QUOTES, 'UTF-8') ?>"
               value="<?= htmlspecialchars((string) $filtroQ, ENT_QUOTES, 'UTF-8') ?>"
               class="form-control form-control-sm bg-light border-start-0 rounded-start-0"
               placeholder="<?= htmlspecialchars($filtroQPlaceholder, ENT_QUOTES, 'UTF-8') ?>"
               <?= $filtroQAuto ? 'data-filtro-auto="500"' : '' ?>
               <?= $filtroQAutocompleteUrl !== '' ? 'data-filtro-autocomplete="' . htmlspecialchars($filtroQAutocompleteUrl, ENT_QUOTES, 'UTF-8') . '" list="ds-' . $idUnico . '"' : '' ?>>
        <?php if ($filtroQAutocompleteUrl !== ''): ?>
          <datalist id="ds-<?= htmlspecialchars($idUnico, ENT_QUOTES, 'UTF-8') ?>"></datalist>
        <?php endif; ?>
      </div>
    </div>

    <?php foreach ($filtroSelectores as $select): ?>
      <div class="d-flex flex-column">
        <label class="text-muted text-uppercase fw-semibold mb-1 label-filtro"><?= htmlspecialchars((string) ($select['etiqueta'] ?? ''), ENT_QUOTES, 'UTF-8') ?></label>
        <select name="<?= htmlspecialchars((string) ($select['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                class="form-select form-select-sm rounded-3 filtro-select-auto"
                <?= isset($select['minWidth']) ? 'style="min-width:' . (int) $select['minWidth'] . 'px;"' : '' ?>>
          <?php foreach (($select['opciones'] ?? []) as $opcion): ?>
            <option value="<?= htmlspecialchars((string) $opcion['valor'], ENT_QUOTES, 'UTF-8') ?>"
                    <?= ((string) ($select['seleccionado'] ?? '') === (string) $opcion['valor']) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $opcion['texto'], ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endforeach; ?>

    <?php if (!empty($filtroFechas['desde']) || !empty($filtroFechas['hasta'])): ?>
      <div class="d-flex flex-column">
        <label class="text-muted text-uppercase fw-semibold mb-1 label-filtro">Desde</label>
        <input type="date" name="<?= htmlspecialchars((string) ($filtroFechas['desde']['nombre'] ?? 'desde'), ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) ($filtroFechas['desde']['valor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm rounded-3">
      </div>
      <div class="d-flex flex-column">
        <label class="text-muted text-uppercase fw-semibold mb-1 label-filtro">Hasta</label>
        <input type="date" name="<?= htmlspecialchars((string) ($filtroFechas['hasta']['nombre'] ?? 'hasta'), ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) ($filtroFechas['hasta']['valor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm rounded-3">
      </div>
    <?php endif; ?>

    <?php if ($filtroPorPagina !== null): ?>
      <div class="d-flex flex-column">
        <label class="text-muted text-uppercase fw-semibold mb-1 label-filtro">Registros por página</label>
        <select name="por_pagina" class="form-select form-select-sm rounded-3 filtro-select-auto" style="min-width: 110px;">
          <?php foreach ($filtroPorPaginaOpciones as $opcion): ?>
            <option value="<?= (int) $opcion ?>" <?= ((int) $filtroPorPagina === (int) $opcion) ? 'selected' : '' ?>><?= (int) $opcion ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>

    <?php if ($filtroBotones): ?>
      <button class="btn btn-primary btn-sm rounded-3 px-3" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button>
      <?php if ($filtroLimpiarUrl !== ''): ?>
        <a href="<?= htmlspecialchars($filtroLimpiarUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Limpiar</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</form>
<?php if (empty($GLOBALS['__panel_filtros_js_v2'])) { $GLOBALS['__panel_filtros_js_v2'] = true; ?>
<script>
(function () {
  function configurarPanel(form) {
    form.querySelectorAll('button[data-filtro-hidden]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var h = form.querySelector('input[name="' + btn.getAttribute('data-filtro-hidden') + '"]');
        if (h) { h.value = btn.getAttribute('data-filtro-val'); }
        form.submit();
      });
    });
    form.querySelectorAll('select.filtro-select-auto').forEach(function (sel) {
      sel.addEventListener('change', function () { form.submit(); });
    });
    var auto = form.querySelector('input[data-filtro-auto]');
    if (auto) {
      var temporizador = null;
      var retraso = parseInt(auto.getAttribute('data-filtro-auto'), 10) || 0;
      auto.addEventListener('input', function () {
        if (temporizador) { clearTimeout(temporizador); }
        if (retraso > 0) { temporizador = setTimeout(function () { form.submit(); }, retraso); }
      });
    }
    var ac = form.querySelector('input[data-filtro-autocomplete]');
    if (ac) {
      var dl = document.getElementById(ac.getAttribute('list'));
      if (dl) {
        ac.addEventListener('input', function () {
          var valor = ac.value.trim();
          dl.innerHTML = '';
          if (valor.length < 1) { return; }
          var base = ac.getAttribute('data-filtro-autocomplete');
          fetch(base + (base.indexOf('?') >= 0 ? '&' : '?') + 'q=' + encodeURIComponent(valor), { headers: { 'Accept': 'application/json' } })
            .then(function (respuesta) { return respuesta.json(); })
            .then(function (items) {
              if (!Array.isArray(items)) { return; }
              items.forEach(function (item) {
                var opcion = document.createElement('option');
                opcion.value = item.label || item;
                dl.appendChild(opcion);
              });
            })
            .catch(function () {});
        });
      }
    }
  }
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form.panel-filtros').forEach(configurarPanel);
  });
})();
</script>
<?php } ?>