<?php
/**
 * Eliminar Expediente de Modalidad de Grado
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';

$modelo = new MgExpedienteModel();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<script>alert('ID no válido'); window.location='index.php?accion=mg_expedientes_listar';</script>";
    exit;
}

$expediente = $modelo->obtenerPorId($id);
if (!$expediente) {
    echo "<script>alert('Expediente no encontrado'); window.location='index.php?accion=mg_expedientes_listar';</script>";
    exit;
}

if ($modelo->eliminar($id)) {
    header('Location: index.php?accion=mg_expedientes_listar');
    exit;
} else {
    echo "<script>alert('Error al eliminar, intenta nuevamente'); history.back();</script>";
    exit;
}