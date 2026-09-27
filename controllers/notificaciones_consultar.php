<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/NotificationModel.php';

header('Content-Type: application/json; charset=utf-8');

$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);
if ($id_usuario <= 0) {
    echo json_encode(['ok' => false, 'no_leidas' => 0, 'items' => []]);
    exit;
}

$notificaciones = (new NotificationModel($pdo))->recientes($id_usuario);

echo json_encode([
    'ok' => true,
    'no_leidas' => (new NotificationModel($pdo))->noLeidas($id_usuario),
    'items' => array_map(function ($n) {
        return [
            'id'     => (int) $n['id_notificacion'],
            'tipo'   => (string) ($n['tipo'] ?? ''),
            'mensaje' => (string) ($n['mensaje'] ?? ''),
            'enlace' => $n['enlace'] ?? null,
            'leida'  => (bool) $n['leida'],
            'fecha'  => (string) ($n['fecha'] ?? ''),
        ];
    }, $notificaciones),
], JSON_UNESCAPED_UNICODE);
exit;