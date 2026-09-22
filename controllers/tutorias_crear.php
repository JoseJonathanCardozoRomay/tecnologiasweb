<?php
require_once __DIR__ . '/../models/TutoriaModel.php';

$modelo = new TutoriaModel();
$estudiantes = $modelo->listarEstudiantes();
$tutores = $modelo->listarTutores();
$materias = $modelo->listarMaterias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->crear($_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=tutoria_crear&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=tutorias_listar&mensaje=creado");
    exit;
}

require_once __DIR__ . '/../views/tutorias/crear.php';