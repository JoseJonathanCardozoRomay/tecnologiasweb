<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

if (($_SESSION['rol'] ?? '') !== 'administrador') {
    header('Location: ../index.php');
    exit;
}

$tutoriaModel = new TutoriaModel($pdo);
$periodos = $tutoriaModel->obtenerPeriodosDisponibles();
$periodoSeleccionado = trim($_GET['periodo'] ?? ($periodos[0] ?? 'I-' . date('Y')));
if (!in_array($periodoSeleccionado, $periodos, true) && !empty($periodos)) {
    $periodoSeleccionado = $periodos[0];
}
$reporte = $tutoriaModel->obtenerReportePorPeriodo($periodoSeleccionado);
$metricas = $reporte['metricas'];
$materias = $reporte['materias'];
$tutores = $reporte['tutores'];
$satisfaccion = $reporte['satisfaccion'];

require_once __DIR__ . '/../views/reportes/index.php';
