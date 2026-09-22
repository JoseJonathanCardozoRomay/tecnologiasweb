<?php
require_once __DIR__ . '/../models/NotificacionModel.php';

$modelo = new NotificacionModel();
$notificaciones = $modelo->listarTodos();

require_once __DIR__ . '/../views/notificaciones/listar.php';