<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$error = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=accesos_listar');
    exit;
}

$registro = $modelo->obtenerPorId($id);
if (!$registro) {
    header('Location: index.php?accion=accesos_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_usuario' => !empty($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : null,
        'ip_origen' => trim($_POST['ip_origen'] ?? ''),
        'resultado' => $_POST['resultado'] ?? 'fallido'
    ];

    if ($modelo->actualizar($id, $datos)) {
        header('Location: index.php?accion=accesos_listar');
        exit;
    }
    $error = 'Error al actualizar';
}

require_once __DIR__ . '/../views/accesos/editar.php';