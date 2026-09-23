<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/NotificacionModel.php';

$modelo = new NotificacionModel($pdo);
$idUsuario = (int)$_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $id = validarId($_POST['id_notificacion'] ?? null);
    if ($id && $modelo->marcarLeida($id, $idUsuario)) {
        flash('success', 'Notificación marcada como leída.');
    }
    redirect('notificaciones_listar.php');
}

$registros = $modelo->obtenerPorUsuario($idUsuario);
$tituloPagina = 'Notificaciones - Sistema de Tutorías';
require __DIR__.'/../views/notificaciones/listar.php';
