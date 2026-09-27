<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_expedientes_listar.php');
    exit;
}

csrf_validar();

$idAsignacion = (int) ($_POST['id_asignacion_tutor'] ?? 0);
$idExpediente = (int) ($_POST['id_expediente_mg'] ?? 0);

if ($idAsignacion <= 0) {
    header('Location: mg_expedientes_listar.php');
    exit;
}

try {
    (new MgExpedienteModel($pdo))->finalizarAsignacion($idAsignacion);
    flash_set('success', 'Asignación de tutor finalizada. El registro se conserva en el historial.');
} catch (Throwable $e) {
    flash_set('error', 'No se pudo finalizar la asignación.');
}

$destino = $idExpediente > 0 ? 'mg_expediente.php?id=' . $idExpediente : 'mg_expedientes_listar.php';
header('Location: ' . $destino);
exit;