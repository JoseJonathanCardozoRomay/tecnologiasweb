<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SolicitudActualizacionModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$solicitudModel = new SolicitudActualizacionModel($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: solicitudes_tutor_listar.php');
    exit;
}

csrf_validar();

$id_solicitud = (int) ($_POST['id_solicitud'] ?? 0);
$estado = $_POST['estado'] ?? '';
$respuesta = trim($_POST['respuesta'] ?? '');

if ($id_solicitud <= 0) {
    flash_set('error', 'La solicitud indicada no es válida.');
    header('Location: solicitudes_tutor_listar.php');
    exit;
}

$solicitud = $solicitudModel->obtenerPorId($id_solicitud);
if (!$solicitud) {
    flash_set('error', 'La solicitud #' . $id_solicitud . ' no existe.');
    header('Location: solicitudes_tutor_listar.php');
    exit;
}

try {
    $actualizada = $solicitudModel->responder($id_solicitud, $estado, (int) ($_SESSION['id_usuario'] ?? 0), $respuesta);

    if (!$actualizada) {
        flash_set('error', 'La solicitud #' . $id_solicitud . ' ya fue atendida por otro administrador.');
        header('Location: solicitudes_tutor_listar.php?id=' . $id_solicitud);
        exit;
    }

    $notifs = new NotificationModel($pdo);
    $id_usuario_solicitante = (int) ($solicitud['id_usuario_solicitante'] ?? 0);
    if ($id_usuario_solicitante > 0) {
        $mensaje = $estado === 'aprobada'
            ? 'Tu solicitud #' . $id_solicitud . ' de ' . SolicitudActualizacionModel::etiquetaTipo($solicitud['tipo']) . ' fue aprobada. Contacta al administrador para aplicar los cambios.'
            : 'Tu solicitud #' . $id_solicitud . ' de ' . SolicitudActualizacionModel::etiquetaTipo($solicitud['tipo']) . ' fue rechazada. Motivo: ' . $respuesta;
        if ($respuesta !== '') {
            $mensaje .= ($estado === 'aprobada' ? ' Nota: ' : ' ') . $respuesta;
        }
        $notifs->crear(
            $id_usuario_solicitante,
            'TUTOR_SOLICITUD',
            $mensaje,
            'tutores_disponibilidad.php',
            $id_solicitud
        );
    }

    flash_set('success', $estado === 'aprobada'
        ? 'Solicitud #' . $id_solicitud . ' aprobada.'
        : 'Solicitud #' . $id_solicitud . ' rechazada.');
} catch (Throwable $e) {
    flash_set('error', $e->getMessage());
}

header('Location: solicitudes_tutor_listar.php?id=' . $id_solicitud);
exit;
