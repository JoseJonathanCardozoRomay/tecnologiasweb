<?php
/**
 * Editar Rol
 */
require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();
$error = '';
$id = (int)($_GET['id'] ?? 0);
$rol = $modelo->obtenerPorId($id);

if (!$rol) {
    header('Location: index.php?accion=listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_rol'] ?? '');
    if (empty($nombre)) {
        $error = 'Escribe el nombre del rol';
    } else {
        if ($modelo->actualizar($id, $nombre)) {
            header('Location: index.php?accion=listar');
            exit;
        } else {
            $error = 'Error al actualizar';
        }
    }
}

require_once __DIR__ . '/../views/roles/editar.php';