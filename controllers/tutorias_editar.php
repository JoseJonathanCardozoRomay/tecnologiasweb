<?php
require_once __DIR__ . '/../models/TutoriaModel.php';

$modelo = new TutoriaModel();
$id = $_GET['id'] ?? 0;
$tutoria = $modelo->obtenerPorId($id);

if (!$tutoria) {
    header('Location: ../index.php?accion=tutorias_listar');
    exit;
}

$estudiantes = $modelo->listarEstudiantes();
$tutores = $modelo->listarTutores();
$materias = $modelo->listarMaterias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $modelo->editar($id, $_POST);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=tutoria_editar&id=$id&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=tutorias_listar&mensaje=actualizado");
    exit;
}

require_once __DIR__ . '/../views/tutorias/editar.php';