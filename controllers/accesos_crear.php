<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/RegistroAccesosModel.php';

$modelo = new RegistroAccesosModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_usuario' => !empty($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : null,
        'ip_origen' => trim($_POST['ip_origen'] ?? ''),
        'resultado' => $_POST['resultado'] ?? 'fallido'
    ];

    if ($modelo->crear($datos)) {
        header('Location: index.php?accion=accesos_listar');
        exit;
    }
    $error = 'Error al registrar el acceso';
}

require_once __DIR__ . '/../views/accesos/crear.php';