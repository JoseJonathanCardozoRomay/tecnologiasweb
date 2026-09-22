<?php
require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/../models/TutorModel.php';

$modelo = new DisponibilidadModel();
$tutorModel = new TutorModel();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=disponibilidades_listar');
    exit;
}

$disp = $modelo->obtenerPorId($id);

if (!$disp) {
    header('Location: index.php?accion=disponibilidades_listar');
    exit;
}

$tutores = $tutorModel->listarTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_tutor' => (int)($_POST['id_tutor'] ?? 0),
        'dia_semana' => trim($_POST['dia_semana'] ?? ''),
        'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
        'hora_fin' => trim($_POST['hora_fin'] ?? '')
    ];

    if ($datos['id_tutor'] > 0 && !empty($datos['dia_semana']) && !empty($datos['hora_inicio']) && !empty($datos['hora_fin'])) {
        if ($modelo->actualizar($id, $datos)) {
            header('Location: index.php?accion=disponibilidades_listar');
            exit;
        }
        $error = 'Error al actualizar';
    } else {
        $error = 'Completa todos los campos';
    }
}

require_once __DIR__ . '/../views/disponibilidad/editar.php';