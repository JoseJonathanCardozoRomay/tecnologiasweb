<?php
/**
 * Asignar materia a un tutor
 */
require_once __DIR__ . '/../models/TutorMateriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';

$modelo = new TutorMateriaModel();
$id_tutor = $_GET['id_tutor'] ?? $_POST['id_tutor'] ?? 0;

if (!$modelo->existeTutor($id_tutor)) {
    header('Location: ../index.php?accion=tutor_materia_listar');
    exit;
}

$tutorModel = new TutorModel();
$tutor = $tutorModel->obtenerPorId($id_tutor);

$asignadas = $modelo->obtenerPorTutor($id_tutor);
$disponibles = $modelo->materiasDisponibles($id_tutor);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id_materia'])) {
        header("Location: ../index.php?accion=tutor_materia_crear&id_tutor=$id_tutor&mensaje=sin_seleccion");
        exit;
    }

    $resultado = $modelo->crear($id_tutor, $_POST['id_materia']);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: ../index.php?accion=tutor_materia_crear&id_tutor=$id_tutor&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header("Location: ../index.php?accion=tutor_materia_crear&id_tutor=$id_tutor&mensaje=creada");
    exit;
}

require_once __DIR__ . '/../views/tutor_materia/crear.php';