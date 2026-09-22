<?php
/**
 * Controlador para el registro de nuevas materias
 */
require_once __DIR__ . '/../models/MateriaModel.php';

$modelo = new MateriaModel();
$carreras = $modelo->listarCarreras();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_materia = trim($_POST['nombre_materia'] ?? '');
    $id_carrera = !empty($_POST['id_carrera']) ? $_POST['id_carrera'] : null;
    
    if (empty($nombre_materia)) {
        header('Location: index.php?accion=materias_crear&mensaje=campo_vacio');
        exit;
    }

    $datos = [
        'nombre_materia' => $nombre_materia,
        'id_carrera' => $id_carrera
    ];

    $resultado = $modelo->crear($datos);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header('Location: index.php?accion=materias_crear&mensaje=error&detalle=' . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=materias_listar&mensaje=registro_creado');
    exit;
}

require_once __DIR__ . '/../views/materias/crear.php';