<?php
require_once __DIR__ . '/../models/EvaluacionTutoriaModel.php';

$modelo = new EvaluacionTutoriaModel();
$tutorias = $modelo->listarTutoriasSinEvaluar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->crear($_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=evaluacion_crear&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=evaluaciones_listar&mensaje=creado");
    exit;
}

require_once __DIR__ . '/../views/evaluaciones/crear.php';