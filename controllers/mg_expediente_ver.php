 <?php
/**
 * Controlador — Ver detalle de Expediente
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';

$modelo = new MgExpedienteModel();
$id = (int)($_GET['id_expediente'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<script>alert('Expediente no válido');history.back();</script>";
    exit;
}

$expediente = $modelo->obtenerPorId($id);

if (!$expediente) {
    echo "<script>alert('Expediente no encontrado');history.back();</script>";
    exit;
}

require_once __DIR__ . '/../views/mg_expedientes/ver.php';