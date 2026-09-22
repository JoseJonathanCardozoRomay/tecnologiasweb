<?php
/**
 * Controlador para la eliminación de registros de carreras
 */
require_once __DIR__ . '/../models/CarreraModel.php';

$modelo = new CarreraModel();
$id_carrera = $_GET['id'] ?? 0;

$resultado = $modelo->eliminar($id_carrera);

if (is_array($resultado) && isset($resultado['error'])) {
    header('Location: index.php?accion=carreras_listar&mensaje=error&detalle=' . urlencode($resultado['error']));
    exit;
}

header('Location: index.php?accion=carreras_listar&mensaje=registro_eliminado');
exit;