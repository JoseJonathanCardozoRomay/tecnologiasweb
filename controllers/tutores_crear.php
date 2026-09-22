<?php
/**
 * Controlador para el registro de tutores
 */
require_once __DIR__ . '/../models/TutorModel.php';

$modelo = new TutorModel();
$usuarios = $modelo->listarUsuariosDisponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id_usuario'])) {
        header('Location: index.php?accion=tutores_crear&mensaje=campos_vacios');
        exit;
    }

    $datos = [
        'id_usuario' => $_POST['id_usuario'],
        'especialidad' => trim($_POST['especialidad'] ?? ''),
        'biografia' => trim($_POST['biografia'] ?? '')
    ];

    $resultado = $modelo->crear($datos);
    
    if (is_array($resultado) && isset($resultado['error'])) {
        header('Location: index.php?accion=tutores_crear&mensaje=error&detalle=' . urlencode($resultado['error']));
        exit;
    }

    header('Location: index.php?accion=tutores_listar&mensaje=registro_creado');
    exit;
}

require_once __DIR__ . '/../views/tutores/crear.php';