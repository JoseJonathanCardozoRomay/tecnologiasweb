<?php
/**
 * Controlador para la eliminación de materias
 */
require_once __DIR__ . '/../models/MateriaModel.php';

$modelo = new MateriaModel();
$id_materia = $_GET['id'] ?? 0;

$resultado = $modelo->eliminar($id_materia);

if (is_array($resultado) && isset($resultado['error'])) {
    header('Location: index.php?accion=materias_listar&mensaje=error&detalle=' . urlencode($resultado['error']));
    exit;
}

header('Location: index.php?accion=materias_listar&mensaje=registro_eliminado');
exit;