<?php
/**
 * Controlador para el registro de nuevas carreras
 */
require_once __DIR__ . '/../models/CarreraModel.php';

$modelo = new CarreraModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_carrera = trim($_POST['nombre_carrera']);
    
    if (empty($nombre_carrera)) {
        header('Location: index.php?accion=carreras_crear&mensaje=campo_vacio');
        exit;
    }

    $resultado = $modelo->crear($nombre_carrera);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header('Location: index.php?accion=carreras_crear&mensaje=error&detalle=' . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=carreras_listar&mensaje=registro_creado');
    exit;
}

require_once __DIR__ . '/../views/carreras/crear.php';