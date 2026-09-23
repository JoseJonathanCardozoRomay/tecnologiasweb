<?php
require_once __DIR__ . '/../models/TutorMateriaModel.php';

$modelo = new TutorMateriaModel();
$id_tutor = (int)($_GET['id_tutor'] ?? 0);
$id_materia = (int)($_GET['id_materia'] ?? 0);

if ($id_tutor > 0 && $id_materia > 0) {
    $modelo->quitar($id_tutor, $id_materia);
}

header('Location: index.php?accion=tutor_materia_listar');
exit;