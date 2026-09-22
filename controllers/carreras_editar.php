<?php
/**
 * Controlador para la modificación de carreras
 */
require_once __DIR__ . '/../models/CarreraModel.php';

$modelo = new CarreraModel();
$id_carrera = $_GET['id'] ?? 0;
$carrera = $modelo->obtenerPorId($id_carrera);

if (!$carrera) {
    header('Location: index.php?accion=carreras_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_carrera = trim($_POST['nombre_carrera']);
    
    if (empty($nombre_carrera)) {
        header("Location: index.php?accion=carreras_editar&id=$id_carrera&mensaje=campo_vacio");
        exit;
    }

    $resultado = $modelo->actualizar($id_carrera, $nombre_carrera);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: index.php?accion=carreras_editar&id=$id_carrera&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=carreras_listar&mensaje=registro_actualizado');
    exit;
}

require_once __DIR__ . '/../views/carreras/editar.php';