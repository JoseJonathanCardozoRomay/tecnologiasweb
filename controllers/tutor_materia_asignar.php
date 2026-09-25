<?php
/**
 * Asignar Materia a Tutor
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorMateriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';

// Solo administrador
if (!tieneRol(['administrador'])) {
    echo "<script>alert('No tienes permiso para acceder a esta página');history.back();</script>";
    exit;
}

$modelo = new TutorMateriaModel();
$tutorModel = new TutorModel();
$materiaModel = new MateriaModel();

$error = '';
$tutores = $tutorModel->listarTodos();
$materias = $materiaModel->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tutor = (int)($_POST['id_tutor'] ?? 0);
    $id_materia = (int)($_POST['id_materia'] ?? 0);

    if ($id_tutor <= 0 || $id_materia <= 0) {
        $error = 'Seleccione tutor y materia';
    } else {
        if ($modelo->asignar($id_tutor, $id_materia)) {
            header('Location: index.php?accion=tutor_materia_listar');
            exit;
        } else {
            $error = 'Esa materia ya está asignada a ese tutor';
        }
    }
}

require_once __DIR__ . '/../views/tutor_materia/asignar.php';