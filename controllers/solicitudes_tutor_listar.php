<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SolicitudActualizacionModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../includes/flash.php';

$solicitudModel = new SolicitudActualizacionModel($pdo);

$estado = $_GET['estado'] ?? '';
$estado = in_array($estado, ['pendiente', 'aprobada', 'rechazada'], true) ? $estado : null;
$solicitudes = $solicitudModel->listar(null, $estado);
$pendientes = $solicitudModel->contarPendientes();

$solicitudSeleccionada = null;
if (isset($_GET['id']) && ctype_digit((string) $_GET['id'])) {
    $solicitudSeleccionada = $solicitudModel->obtenerPorId((int) $_GET['id']);
}

$tituloPagina = 'Solicitudes de Actualización de Tutores - UPDS';
require __DIR__ . '/../views/admin/solicitudes_tutor.php';
