<?php
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';

$modelo = new TutoriaModel();
$estudianteModel = new EstudianteModel();
$tutorModel = new TutorModel();
$materiaModel = new MateriaModel();

$error = '';

$estudiantes = $estudianteModel->listarTodos();
$tutores = $tutorModel->listarTodos();
$materias = $materiaModel->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_estudiante' => (int)($_POST['id_estudiante'] ?? 0),
        'id_tutor' => (int)($_POST['id_tutor'] ?? 0),
        'id_materia' => (int)($_POST['id_materia'] ?? 0),
        'fecha' => trim($_POST['fecha'] ?? ''),
        'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
        'hora_fin' => trim($_POST['hora_fin'] ?? ''),
        'modalidad' => trim($_POST['modalidad'] ?? 'presencial'),
        'lugar_o_enlace' => trim($_POST['lugar_o_enlace'] ?? ''),
        'observaciones' => trim($_POST['observaciones'] ?? ''),
        'estado' => trim($_POST['estado'] ?? 'pendiente')
    ];

    if ($datos['id_estudiante'] <= 0 || $datos['id_tutor'] <= 0 || $datos['id_materia'] <= 0 || empty($datos['fecha']) || empty($datos['hora_inicio']) || empty($datos['hora_fin'])) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=tutorias_listar');
            exit;
        }
        $error = 'Error al guardar la tutoría';
    }
}

require_once __DIR__ . '/../views/tutorias/crear.php';