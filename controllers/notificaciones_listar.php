<?php
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor','estudiante']);
require_once __DIR__ . '/../models/NotificacionModel.php';

$modelo = new NotificacionModel();
$id_usuario = $_SESSION['id_usuario'] ?? 0;

// ✅ Nombre exacto del método
$modelo->marcarTodasLeidas($id_usuario);

$notificaciones = $modelo->listarPorUsuario($id_usuario);

require_once __DIR__ . '/../views/notificaciones/listar.php';