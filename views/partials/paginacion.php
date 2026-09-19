<?php
$pag = $pag ?? paginacionCalcular(0, paginacionParametros());
$mostrarSelector = $mostrarSelector ?? true;
$inicio = $pag['total'] > 0 ? $pag['offset'] + 1 : 0;
$fin = min($pag['offset'] + $pag['por_pagina'], $pag['total']);
$inicioVentana = max(1, $pag['pagina'] - 2);
$finVentana = min($pag['total_paginas'], $pag['pagina'] + 2);
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 p-3 border-top">
  <div class="text-muted small">
    Mostrando <?= $inicio ?>–<?= $fin ?> de <?= $pag['total'] ?> registros
  </div>
  <?php if ($mostrarSelector): ?>
    <form method="GET" class="d-flex align-items-center gap-2">
    <?php foreach (['q', 'orden', 'dir', 'estado', 'periodo'] as $parametro): ?>
      <?php if (isset($_GET[$parametro]) && is_scalar($_GET[$parametro]) && $_GET[$parametro] !== ''): ?>
        <input type="hidden" name="<?= htmlspecialchars($parametro, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $_GET[$parametro], ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>
    <?php endforeach; ?>
    <label for="por_pagina" class="small text-muted text-nowrap">Registros por página</label>
    <select id="por_pagina" name="por_pagina" class="form-select form-select-sm" onchange="this.form.submit()">
      <?php foreach ([10, 25, 50] as $opcion): ?>
        <option value="<?= $opcion ?>" <?= $pag['por_pagina'] === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
      <?php endforeach; ?>
    </select>
    <input type="hidden" name="pagina" value="1">
    </form>
  <?php endif; ?>
  <?php if ($pag['total_paginas'] > 1): ?>
    <nav aria-label="Paginación">
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item <?= $pag['pagina'] === 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= urlLista(['pagina' => 1]) ?>" aria-label="Primera">«</a>
        </li>
        <li class="page-item <?= $pag['pagina'] === 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= urlLista(['pagina' => max(1, $pag['pagina'] - 1)]) ?>" aria-label="Anterior">‹</a>
        </li>
        <?php for ($numero = $inicioVentana; $numero <= $finVentana; $numero++): ?>
          <li class="page-item <?= $numero === $pag['pagina'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= urlLista(['pagina' => $numero]) ?>"><?= $numero ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $pag['pagina'] === $pag['total_paginas'] ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= urlLista(['pagina' => min($pag['total_paginas'], $pag['pagina'] + 1)]) ?>" aria-label="Siguiente">›</a>
        </li>
        <li class="page-item <?= $pag['pagina'] === $pag['total_paginas'] ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= urlLista(['pagina' => $pag['total_paginas']]) ?>" aria-label="Última">»</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>
