<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/ConfiguracionModel.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$idExpediente = (int) ($_GET['id_expediente'] ?? 0);
if ($idExpediente <= 0) {
    flash_set('error', 'Expediente no válido.');
    header('Location: mg_expedientes_listar.php');
    exit;
}

$expModel = new MgExpedienteModel($pdo);
$expediente = $expModel->obtenerPorId($idExpediente);
if (!$expediente) {
    flash_set('error', 'El expediente solicitado no existe.');
    header('Location: mg_expedientes_listar.php');
    exit;
}

$asignacion = $expModel->asignacionActiva($idExpediente);
if (!$asignacion) {
    flash_set('error', 'El expediente no tiene una asignación activa de tutor.');
    header('Location: mg_expediente.php?id=' . $idExpediente);
    exit;
}

$config = new ConfiguracionModel($pdo);
$datosPagina = mgDatosInstitucion($config);

if (empty($asignacion['correlativo_carta'])) {
    $gestion = is_array($datosPagina) && ($datosPagina['gestion'] ?? '') !== '' ? $datosPagina['gestion'] : date('Y');
    $correlativo = mgSiguienteCorrelativo($pdo, 'carta_tutor', $gestion);
    $stmt = $pdo->prepare(
        "UPDATE asignaciones_tutor SET correlativo_carta = :corr WHERE id_asignacion_tutor = :id"
    );
    $stmt->execute([':corr' => $correlativo, ':id' => (int) $asignacion['id_asignacion_tutor']]);
    $asignacion['correlativo_carta'] = $correlativo;
}

require_once __DIR__ . '/../views/mg/cartas/carta_tutor.php';