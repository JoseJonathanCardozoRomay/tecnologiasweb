<?php
/**
 * Crear Rol
 */
require_once __DIR__ . '/../models/RolModel.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_rol'] ?? '');
    if (empty($nombre)) {
        $error = 'Escribe el nombre del rol';
    } else {
        $modelo = new RolModel();
        if ($modelo->crear($nombre)) {
            header('Location: index.php?accion=listar');
            exit;
        } else {
            $error = 'Error: El rol ya existe o hubo un problema';
        }
    }
}

require_once __DIR__ . '/../views/roles/crear.php';