<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';

$modelo = new TutoriaModel();
$tutorModel = new TutorModel();
$materiaModel = new MateriaModel();
$estudianteModel = new EstudianteModel();

$error = '';
$tutores = $tutorModel->listarTodos();
$materias = $materiaModel->listarTodas();

// Obtener el id_estudiante automáticamente
$id_estudiante_actual = $estudianteModel->obtenerPorUsuario($_SESSION['id_usuario']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
    'id_estudiante' => $id_estudiante_actual,
    'id_tutor' => (int)($_POST['id_tutor'] ?? 0),
    'id_materia' => (int)($_POST['id_materia'] ?? 0),
    'fecha' => $_POST['fecha'] ?? '',
    'hora_inicio' => $_POST['hora_inicio'] ?? '',
    'hora_fin' => $_POST['hora_fin'] ?? '',
    'modalidad' => $_POST['modalidad'] ?? 'presencial',
    'lugar_o_enlace' => $_POST['lugar_o_enlace'] ?? '',
    'estado' => 'pendiente', // ✅ Siempre pendiente
    'observaciones' => $_POST['observaciones'] ?? ''
];

    if ($datos['id_estudiante'] && $datos['id_tutor'] && $datos['id_materia'] && $datos['fecha']) {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=tutorias_listar');
            exit;
        } else {
            $error = 'Error al guardar la tutoría';
        }
    } else {
        $error = 'Completa todos los campos obligatorios';
    }
}

// ✅ RUTA CORRECTA — con "s" en tutorias
require_once __DIR__ . '/../views/tutorias/crear.php';