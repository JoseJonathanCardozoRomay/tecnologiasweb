<?php
require_once __DIR__ . '/../models/NotificacionModel.php';

$modelo = new NotificacionModel();
$modelo->eliminar($_GET['id']);

header('Location: index.php?ruta=notificaciones_listar');
exit;
?>