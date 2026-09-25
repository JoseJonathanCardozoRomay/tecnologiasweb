<?php
/**
 * Eliminar Evaluación — SOLO administrador
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EvaluacionModel.php';

if (!tieneRol(['administrador'])) {
    echo "<script>alert('Solo el administrador puede eliminar');history.back();</script>";
    exit;
}

$modelo = new EvaluacionModel();
$id_evaluacion = (int)($_GET['id'] ?? 0);

if ($id_evaluacion > 0) {
    $modelo->eliminar($id_evaluacion);
}

header('Location: index.php?accion=evaluaciones_listar');
exit;