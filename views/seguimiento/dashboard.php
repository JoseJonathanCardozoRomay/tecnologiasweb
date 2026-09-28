<?php
$titulo_pagina = 'Métricas de Seguimiento';
ob_start();
?>
<h1>Métricas de Seguimiento de Sesiones</h1>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin:30px 0;">
    <div style="background:#d4edda; padding:20px; border-radius:8px; text-align:center;">
        <h3 style="color:#155724; margin:0;">Logrados</h3>
        <p style="font-size:32px; font-weight:bold; color:#155724; margin:10px 0 0;"><?= $metricas['logrados'] ?? 0 ?></p>
    </div>
    <div style="background:#fff3cd; padding:20px; border-radius:8px; text-align:center;">
        <h3 style="color:#856404; margin:0;">Parciales</h3>
        <p style="font-size:32px; font-weight:bold; color:#856404; margin:10px 0 0;"><?= $metricas['parciales'] ?? 0 ?></p>
    </div>
    <div style="background:#f8d7da; padding:20px; border-radius:8px; text-align:center;">
        <h3 style="color:#721c24; margin:0;">Sin Avance</h3>
        <p style="font-size:32px; font-weight:bold; color:#721c24; margin:10px 0 0;"><?= $metricas['sin_avance'] ?? 0 ?></p>
    </div>
    <div style="background:#cce5ff; padding:20px; border-radius:8px; text-align:center;">
        <h3 style="color:#004085; margin:0;">Asistieron</h3>
        <p style="font-size:32px; font-weight:bold; color:#004085; margin:10px 0 0;"><?= $metricas['asistieron'] ?? 0 ?></p>
    </div>
    <div style="background:#e2e3e5; padding:20px; border-radius:8px; text-align:center;">
        <h3 style="color:#383d41; margin:0;">No Asistieron</h3>
        <p style="font-size:32px; font-weight:bold; color:#383d41; margin:10px 0 0;"><?= $metricas['no_asistieron'] ?? 0 ?></p>
    </div>
</div>

<a href="index.php" style="display:inline-block; padding:12px 25px; background:#6c757d; color:white; text-decoration:none; border-radius:6px; font-weight:bold;">Volver al Inicio</a>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';