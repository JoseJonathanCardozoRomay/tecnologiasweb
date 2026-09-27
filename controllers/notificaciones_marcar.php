<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/views/estudiante/panel.php'));
    exit;
}

csrf_validar();

$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);
$model = new NotificationModel($pdo);

$id_notificacion = (int) ($_POST['id_notificacion'] ?? 0);
$todas = !empty($_POST['todas']);

try {
    if ($todas) {
        $model->marcarTodasLeidas($id_usuario);
    } elseif ($id_notificacion > 0) {
        $model->marcarLeida($id_notificacion, $id_usuario);
    }
} catch (Throwable $e) {
    // El fallo del marcado no debe romper la navegación.
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$destino = !empty($_POST['destino'])
    ? $_POST['destino']
    : ($referer !== '' ? $referer : '/views/estudiante/panel.php');

header('Location: ' . $destino);
exit;