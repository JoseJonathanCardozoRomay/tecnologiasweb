<?php
$titulo = $titulo ?? ($tituloPagina ?? '');
$descripcion = $descripcion ?? '';
$migas = $migas ?? [];
$accion = $accion ?? null;
?>
<header class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
  <div>
    <?php if ($migas): ?>
      <nav aria-label="Migas de pan" class="breadcrumb-nav mb-2">
        <ol class="breadcrumb mb-0">
          <?php foreach ($migas as $miga): ?>
            <li class="breadcrumb-item <?= !empty($miga['actual']) ? 'active' : '' ?>" <?= !empty($miga['actual']) ? 'aria-current="page"' : '' ?>>
              <?php if (!empty($miga['url']) && empty($miga['actual'])): ?>
                <a href="<?= htmlspecialchars($miga['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($miga['texto']) ?></a>
              <?php else: ?>
                <?= htmlspecialchars($miga['texto']) ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </nav>
    <?php endif; ?>
    <h1 class="page-title mb-1"><?= htmlspecialchars($titulo) ?></h1>
    <?php if ($descripcion !== ''): ?><p class="page-description mb-0"><?= htmlspecialchars($descripcion) ?></p><?php endif; ?>
  </div>
  <?php if ($accion): ?>
    <div class="page-action"><?= $accion ?></div>
  <?php endif; ?>
</header>
