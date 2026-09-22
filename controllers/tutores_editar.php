<?php
/**
 * Controlador para la modificación de tutores
 */
require_once __DIR__ . '/../models/TutorModel.php';

$modelo = new TutorModel();
$id_tutor = $_GET['id'] ?? 0;
$tutor = $modelo->obtenerPorId($id_tutor);

if (!$tutor) {
    header('Location: index.php?accion=tutores_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'especialidad' => trim($_POST['especialidad'] ?? ''),
        'biografia' => trim($_POST['biografia'] ?? '')
    ];

    $resultado = $modelo->actualizar($id_tutor, $datos);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: index.php?accion=tutores_editar&id=$id_tutor&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=tutores_listar&mensaje=registro_actualizado');
    exit;
}

require_once __DIR__ . '/../views/tutores/editar.php';