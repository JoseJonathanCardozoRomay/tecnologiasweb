<?php
require_once __DIR__ . '/../models/DisponibilidadTutorModel.php';

$modelo = new DisponibilidadTutorModel();
$tutores = $modelo->listarTutores();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->crear($_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=disponibilidad_crear&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=disponibilidad_listar&mensaje=creado");
    exit;
}

require_once __DIR__ . '/../views/disponibilidad/crear.php';