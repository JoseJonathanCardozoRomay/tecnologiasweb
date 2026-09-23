<?php
require_once __DIR__ . '/../models/TutorMateriaModel.php';

$modelo = new TutorMateriaModel();
$tutores = $modelo->listarTutores();
$materias = $modelo->listarMaterias();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tutor = (int)($_POST['id_tutor'] ?? 0);
    $id_materia = (int)($_POST['id_materia'] ?? 0);

    if ($id_tutor <= 0 || $id_materia <= 0) {
        $error = 'Selecciona un tutor y una materia';
    } else {
        if ($modelo->asignar($id_tutor, $id_materia)) {
            header('Location: index.php?accion=tutor_materia_listar');
            exit;
        }
        $error = 'Esa materia ya está asignada a este tutor';
    }
}

require_once __DIR__ . '/../views/tutor_materia/asignar.php';