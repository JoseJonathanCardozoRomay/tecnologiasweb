<?php
require_once __DIR__ . '/../models/NotificacionModel.php';

$modelo = new NotificacionModel();
$notificacion = $modelo->obtenerPorId($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo->marcarLeida($_GET['id']);
    header('Location: index.php?ruta=notificaciones_listar');
    exit;
}

require_once __DIR__ . '/../views/notificaciones/editar.php';
?>