<?php
require_once __DIR__ . '/../models/EvaluacionModel.php';

$modelo = new EvaluacionModel();
$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=evaluaciones_listar');
    exit;
}

$evaluacion = $modelo->obtenerPorId($id);
if (!$evaluacion) {
    header('Location: index.php?accion=evaluaciones_listar');
    exit;
}

$tutorias = $modelo->listarTutoriasDisponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_tutoria' => (int)($_POST['id_tutoria'] ?? 0),
        'calificacion' => (int)($_POST['calificacion'] ?? 1),
        'comentario' => trim($_POST['comentario'] ?? '')
    ];

    if ($datos['id_tutoria'] <= 0) {
        $error = 'Selecciona una tutoría';
    } else {
        if ($modelo->actualizar($id, $datos)) {
            header('Location: index.php?accion=evaluaciones_listar');
            exit;
        }
        $error = 'Error al actualizar la evaluación';
    }
}

require_once __DIR__ . '/../views/evaluaciones/editar.php';