<?php
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /controllers/notificaciones_listar.php');
    exit;
}

csrf_validar();

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$notificacionModel = new NotificacionModel($pdo);
$destino = '/controllers/notificaciones_listar.php';

if (isset($_POST['todas'])) {
    $notificacionModel->marcarTodas($idUsuario);
    flash_set('success', 'Todas las notificaciones fueron marcadas como leídas.');
} else {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id) {
        $notificacion = $notificacionModel->obtenerPorId($id, $idUsuario);
        if ($notificacion) {
            $notificacionModel->marcarLeida($id, $idUsuario);
            $url = (string) ($notificacion['url'] ?? '');
            if ($url !== '' && strpos($url, '/') === 0) {
                $destino = $url;
            }
        }
    }
}

header('Location: ' . $destino);
exit;
