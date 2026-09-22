<?php
/**
 * Eliminar asignación
 */
require_once __DIR__ . '/../models/TutorMateriaModel.php';

$modelo = new TutorMateriaModel();
$id_tutor = $_GET['id_tutor'] ?? 0;
$id_materia = $_GET['id_materia'] ?? 0;

$resultado = $modelo->eliminar($id_tutor, $id_materia);

if (is_array($resultado) && isset($resultado['error'])) {
    header("Location: ../index.php?accion=tutor_materia_crear&id_tutor=$id_tutor&mensaje=error&detalle=" . urlencode($resultado['error']));
    exit;
}

header("Location: ../index.php?accion=tutor_materia_crear&id_tutor=$id_tutor&mensaje=eliminada");
exit;