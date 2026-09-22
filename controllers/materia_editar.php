<?php
/**
 * Controlador para la modificación de materias
 */
require_once __DIR__ . '/../models/MateriaModel.php';

$modelo = new MateriaModel();
$id_materia = $_GET['id'] ?? 0;
$materia = $modelo->obtenerPorId($id_materia);

if (!$materia) {
    header('Location: index.php?accion=materias_listar');
    exit;
}

$carreras = $modelo->listarCarreras();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_materia = trim($_POST['nombre_materia'] ?? '');
    $id_carrera = !empty($_POST['id_carrera']) ? $_POST['id_carrera'] : null;
    
    if (empty($nombre_materia)) {
        header("Location: index.php?accion=materias_editar&id=$id_materia&mensaje=campo_vacio");
        exit;
    }

    $datos = [
        'nombre_materia' => $nombre_materia,
        'id_carrera' => $id_carrera
    ];

    $resultado = $modelo->actualizar($id_materia, $datos);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header("Location: index.php?accion=materias_editar&id=$id_materia&mensaje=error&detalle=" . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=materias_listar&mensaje=registro_actualizado');
    exit;
}

require_once __DIR__ . '/../views/materias/editar.php';