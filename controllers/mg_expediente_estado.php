<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_expedientes_listar.php');
    exit;
}

csrf_validar();

$idExpediente = (int) ($_POST['id_expediente_mg'] ?? 0);
$nuevoEstado = $_POST['estado'] ?? '';
$resolucion = trim($_POST['resolucion_admin'] ?? '');

if ($idExpediente <= 0) {
    header('Location: mg_expedientes_listar.php');
    exit;
}

$expedienteModel = new MgExpedienteModel($pdo);
$expediente = $expedienteModel->obtenerPorId($idExpediente);
if (!$expediente) {
    flash_set('error', 'El expediente no existe.');
    header('Location: mg_expedientes_listar.php');
    exit;
}

if (!$expedienteModel->esEstadoValido($nuevoEstado)) {
    flash_set('error', 'El estado seleccionado no es válido.');
    header('Location: mg_expediente.php?id=' . $idExpediente);
    exit;
}

try {
    $expedienteModel->cambiarEstado($idExpediente, $nuevoEstado, (int) ($_SESSION['id_usuario'] ?? 0));
    if ($resolucion !== '') {
        $expedienteModel->actualizarResolucion($idExpediente, $resolucion);
    }
    mgAvisarEstudiante(
        $pdo,
        $idExpediente,
        'MG_EXPEDIENTE_ESTADO',
        'Tu expediente N° ' . $idExpediente . ' de Modalidad de Grado cambio al estado "'
            . mgExpEstadoLabel($nuevoEstado) . '" (' . mgFechaEspanol(date('Y-m-d')) . ').',
        '/views/estudiante/mg_portal.php',
        (int) ($_SESSION['id_usuario'] ?? 0)
    );
    flash_set('success', "Estado del expediente N° $idExpediente actualizado a \"" . mgExpEstadoLabel($nuevoEstado) . "\".");
} catch (Throwable $e) {
    flash_set('error', 'Error al actualizar el estado del expediente.');
}

header('Location: mg_expediente.php?id=' . $idExpediente);
exit;