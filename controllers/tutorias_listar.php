<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$tutoriaModel = new TutoriaModel($pdo);

$rolSesion = $_SESSION['rol'] ?? '';
$idUsuario = $_SESSION['id_usuario'] ?? 0;

// Filtro por estado desde GET
$filtroEstado = $_GET['estado'] ?? null;
if (!in_array($filtroEstado, ['pendiente', 'confirmada', 'realizada', 'cancelada'])) {
    $filtroEstado = null;
}

$tutorias = $tutoriaModel->obtenerTodas($filtroEstado);
$metricas = $tutoriaModel->obtenerMetricasGlobales();

require_once __DIR__ . '/../views/tutorias/listar.php';
