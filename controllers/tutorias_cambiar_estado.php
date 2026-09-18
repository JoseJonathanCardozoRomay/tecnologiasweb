<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$idTutoria = $_REQUEST['id'] ?? null;
$nuevoEstado = $_REQUEST['estado'] ?? null;
$observaciones = $_REQUEST['observaciones'] ?? null;

$estadosValidos = ['pendiente', 'confirmada', 'realizada', 'cancelada'];

if ($idTutoria && in_array($nuevoEstado, $estadosValidos)) {
    $tutoriaModel = new TutoriaModel($pdo);
    try {
        $tutoriaModel->actualizarEstado($idTutoria, $nuevoEstado, $observaciones);
    } catch (PDOException $e) {
        // Log o manejo de error
    }
}

// Redirección inteligente según de dónde vino la petición
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (!empty($referer)) {
    header("Location: " . $referer);
} else {
    header("Location: tutorias_listar.php");
}
exit;
