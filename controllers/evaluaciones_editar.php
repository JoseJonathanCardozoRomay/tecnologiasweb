<?php
require_once __DIR__ . '/../models/EvaluacionTutoriaModel.php';

$modelo = new EvaluacionTutoriaModel();
$id = $_GET['id'] ?? 0;
$evaluacion = $modelo->obtenerPorId($id);

if (!$evaluacion) {
    header('Location: ../index.php?accion=evaluaciones_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->editar($id, $_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=evaluacion_editar&id=$id&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=evaluaciones_listar&mensaje=actualizado");
    exit;
}

require_once __DIR__ . '/../views/evaluaciones/editar.php';