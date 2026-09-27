<?php
$emptyIcono = $emptyIcono ?? 'bi-inbox';
$emptyTitulo = $emptyTitulo ?? 'No hay registros';
$emptyTexto = $emptyTexto ?? '';
$emptyAccion = $emptyAccion ?? null;
?>
<div class="empty-state">
  <div class="empty-state-icon"><i class="bi <?= htmlspecialchars($emptyIcono, ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i></div>
  <h2 class="empty-state-title"><?= htmlspecialchars($emptyTitulo) ?></h2>
  <?php if ($emptyTexto !== ''): ?><p class="empty-state-text"><?= htmlspecialchars($emptyTexto) ?></p><?php endif; ?>
  <?php if ($emptyAccion): ?><div class="mt-3"><?= $emptyAccion ?></div><?php endif; ?>
</div>
