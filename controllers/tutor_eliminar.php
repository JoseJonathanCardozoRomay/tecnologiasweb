<?php
/**
 * Controlador para la eliminación de tutores
 */
require_once __DIR__ . '/../models/TutorModel.php';

$modelo = new TutorModel();
$id_tutor = $_GET['id'] ?? 0;

$resultado = $modelo->eliminar($id_tutor);

if (is_array($resultado) && isset($resultado['error'])) {
    header('Location: index.php?accion=tutores_listar&mensaje=error&detalle=' . urlencode($resultado['error']));
    exit;
}

header('Location: index.php?accion=tutores_listar&mensaje=registro_eliminado');
exit;