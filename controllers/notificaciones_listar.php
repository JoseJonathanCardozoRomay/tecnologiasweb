<?php
/**
 * Listar Notificaciones
 * Admin = TODAS / Tutor = las suyas / Estudiante = las suyas
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/NotificacionModel.php';

$modelo = new NotificacionModel();
$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

// Marcar como leídas al entrar
$modelo->marcarTodasLeidas($id_usuario_actual);

// Cargar según rol
if ($rol_actual === 'administrador') {
    $notificaciones = $modelo->listarTodas();
} else {
    $notificaciones = $modelo->listarPorUsuario($id_usuario_actual);
}

require_once __DIR__ . '/../views/notificaciones/listar.php';