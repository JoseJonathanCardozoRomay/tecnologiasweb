 <?php
/**
 * Panel de Métricas de Seguimiento
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MetricaSeguimientoModel.php';

$modelo = new MetricaSeguimientoModel();
$metricas = $modelo->obtenerDashboard();

// ✅ RUTA CORRECTA: subir un nivel y entrar a views
require_once __DIR__ . '/../views/seguimiento/dashboard.php';