<?php
$kpiIcono = $kpiIcono ?? 'bi-bar-chart';
$kpiValor = $kpiValor ?? 0;
$kpiEtiqueta = $kpiEtiqueta ?? '';
$kpiClase = $kpiClase ?? 'primary';
?>
<div class="kpi-card kpi-<?= htmlspecialchars($kpiClase, ENT_QUOTES, 'UTF-8') ?>">
  <div class="kpi-icon"><i class="bi <?= htmlspecialchars($kpiIcono, ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i></div>
  <div class="kpi-content">
    <div class="kpi-value"><?= htmlspecialchars((string) $kpiValor, ENT_QUOTES, 'UTF-8') ?></div>
    <div class="kpi-label"><?= htmlspecialchars($kpiEtiqueta, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
</div>
