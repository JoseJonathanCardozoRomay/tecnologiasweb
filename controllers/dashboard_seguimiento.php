<?php
require_once __DIR__ . '/../config/sesion.php';
if (!tieneRol(['administrador','coordinador_mg','tutor'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}
require_once __DIR__ . '/../models/MetricaSeguimientoModel.php';
$modelo = new MetricaSeguimientoModel();
$metricas = $modelo->obtenerDashboard();
require_once __DIR__ . '/../views/seguimiento/dashboard_seguimiento.php';