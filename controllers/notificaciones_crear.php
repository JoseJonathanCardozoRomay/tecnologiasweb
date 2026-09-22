<?php
require_once __DIR__ . '/../models/NotificacionModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = new NotificacionModel();
    $modelo->crear(
        $_POST['id_usuario'],
        $_POST['tipo'],
        $_POST['mensaje'],
        $_POST['url'] ?? null
    );
    header('Location: index.php?ruta=notificaciones_listar');
    exit;
}

require_once __DIR__ . '/../views/notificaciones/crear.php';
?>